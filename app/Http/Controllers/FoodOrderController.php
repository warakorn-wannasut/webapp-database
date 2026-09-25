<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Seat;
use App\Models\SeatSession;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FoodOrderController extends Controller
{
    /**
     * แสดงหน้าเมนูสั่งอาหารและเครื่องดื่ม
     * สมาชิกคนที่ 3: ระบบสั่งอาหารและเครื่องดื่ม POS (Member 3)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedCategoryId = $request->input('category_id');

        // 1. ดึงรายการหมวดหมู่อาหารพร้อมนับจำนวนสินค้าในแต่ละหมวด
        $categories = Category::withCount('products')->get();

        // 2. ดึงรายการสินค้าที่มีสต็อกพร้อมจำหน่าย (กรองตามหมวดหมู่ถ้ามี)
        $productsQuery = Product::with('category')->where('stock_quantity', '>', 0);
        if ($selectedCategoryId) {
            $productsQuery->where('category_id', $selectedCategoryId);
        }
        $products = $productsQuery->get();

        // 3. ตรวจสอบเครื่องที่ลูกค้ากำลังเปิดใช้งานอยู่
        $activeSession = SeatSession::with('seat.zone')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        // 4. คำนวณยอดเงินที่สามารถใช้สั่งอาหารได้จริง
        $availableBalance = (float) $user->balance;
        if ($activeSession && $activeSession->user_package_id == null) {
            $startTime = Carbon::parse($activeSession->start_time);
            $usedMinutes = (int) ceil($startTime->diffInSeconds(Carbon::now()) / 60);
            $estimatedCost = round(($usedMinutes / 60) * (float) $activeSession->rate_snapshot, 2);
            $availableBalance = max(0.00, $availableBalance - $estimatedCost);
        }

        return view('pages.customer.food-order', [
            'user' => $user,
            'categories' => $categories,
            'products' => $products,
            'activeSession' => $activeSession,
            'activeSeat' => $activeSession ? $activeSession->seat : null,
            'selectedCategoryId' => $selectedCategoryId,
            'availableBalance' => $availableBalance,
        ]);
    }

    /**
     * ฟังก์ชันสั่งซื้ออาหารและเครื่องดื่ม
     * สมาชิกคนที่ 3: ระบบสั่งอาหารและเครื่องดื่ม POS (Member 3)
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'seat_id' => 'required|exists:seats,id',
            'items' => 'required|array|min:1',
            'payment_method' => 'required|in:wallet,promptpay,cash',
        ]);

        $user = Auth::user();
        $freshUser = User::find($user->id);
        $seatId = (int) $request->input('seat_id');
        $rawItems = $request->input('items', []);
        $paymentMethod = $request->input('payment_method');

        // แปลงรูปแบบ items ให้ยืดหยุ่นรองรับทั้งแบบ array of objects และแบบ key-value
        $items = [];
        foreach ($rawItems as $key => $val) {
            if (is_array($val)) {
                $pId = $val['product_id'] ?? $key;
                $qty = $val['quantity'] ?? 0;
            } else {
                $pId = $key;
                $qty = (int) $val;
            }
            if ($qty > 0) {
                $items[] = [
                    'product_id' => (int) $pId,
                    'quantity' => (int) $qty,
                ];
            }
        }

        // 1. ตรวจสอบเครื่องที่ผู้ใช้กำลังเปิดใช้งานอยู่
        $seat = Seat::find($seatId);
        if ($seat == null) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลเครื่อง');
        }

        $session = SeatSession::where('user_id', $freshUser->id)
            ->where('seat_id', $seat->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        // 2. ตรวจสอบสต็อกสินค้าและคำนวณราคารวม
        $totalAmount = 0.00;
        $orderItemsList = [];

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($quantity <= 0) {
                continue;
            }

            $product = Product::find($productId);
            if ($product == null) {
                return redirect()->back()->with('error', 'ไม่พบข้อมูลสินค้า');
            }

            if ($product->stock_quantity < $quantity) {
                return redirect()->back()->with('error', 'สินค้า ' . $product->name . ' มีไม่พอ (เหลือ ' . $product->stock_quantity . ' ชิ้น)');
            }

            $unitPrice = (float) $product->price;
            $subtotal = round($unitPrice * $quantity, 2);
            $totalAmount = $totalAmount + $subtotal;

            $orderItemsList[] = [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];
        }

        if (empty($orderItemsList)) {
            return redirect()->back()->with('error', 'กรุณาเลือกรายการสินค้าอย่างน้อย 1 รายการ');
        }

        // 3. ตรวจสอบยอดเงิน (กรณีชำระด้วย Wallet)
        $paymentStatus = 'pending_payment';

        if ($paymentMethod == 'wallet') {
            $availableBalance = (float) $freshUser->balance;
            if ($session != null && $session->user_package_id == null) {
                $startTime = Carbon::parse($session->start_time);
                $usedMinutes = (int) ceil($startTime->diffInSeconds(Carbon::now()) / 60);
                $estimatedCost = round(($usedMinutes / 60) * (float) $session->rate_snapshot, 2);
                $availableBalance = max(0.00, $availableBalance - $estimatedCost);
            }

            if ($availableBalance < $totalAmount) {
                return redirect()->back()->with('error', 'ยอดเงินที่ใช้ได้ไม่เพียงพอสำหรับการสั่งอาหาร (ต้องกันเงินไว้จ่ายค่าเครื่องคอมพิวเตอร์)');
            }

            // หักเงินออกจากกระเป๋า
            $freshUser->balance = (float) $freshUser->balance - $totalAmount;
            $freshUser->save();

            $paymentStatus = 'paid';
        }

        // 4. บันทึกคำสั่งซื้อลงตาราง orders
        $order = Order::create([
            'user_id' => $freshUser->id,
            'seat_id' => $seat->id,
            'session_id' => $session ? $session->id : null,
            'total_amount' => $totalAmount,
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'order_status' => 'pending',
        ]);

        // 5. บันทึกรายการสินค้าในคำสั่งซื้อลงตาราง order_items และตัดสต็อกสินค้า
        foreach ($orderItemsList as $orderItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $orderItem['product']->id,
                'quantity' => $orderItem['quantity'],
                'unit_price' => $orderItem['unit_price'],
                'subtotal' => $orderItem['subtotal'],
            ]);

            // ตัดสต็อกสินค้า
            $productItem = $orderItem['product'];
            $productItem->stock_quantity = $productItem->stock_quantity - $orderItem['quantity'];
            $productItem->save();
        }

        // 6. บันทึกประวัติการหักเงินลงตาราง wallet_transactions (ถ้าจ่ายผ่าน Wallet)
        if ($paymentMethod == 'wallet') {
            WalletTransaction::create([
                'user_id' => $freshUser->id,
                'type' => 'deduct',
                'amount' => $totalAmount,
                'ref_type' => 'food_order',
                'ref_id' => $order->id,
            ]);
        }

        return redirect()->back()->with('success', 'สั่งอาหารสำเร็จ! บิลหมายเลข #' . $order->id . ' พนักงานกำลังจัดเตรียมอาหาร');
    }
}
