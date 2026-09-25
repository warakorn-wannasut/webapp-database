<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Seat;
use App\Models\SeatSession;
use App\Models\User;
use App\Models\UserPackage;
use App\Models\WalletTransaction;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StaffController extends Controller
{
    /**
     * แสดงหน้าจอมอนิเตอร์สถานะเครื่องคอมพิวเตอร์หน้าร้าน
     * สมาชิกคนที่ 5: ระบบพนักงานและครัว (Member 5)
     */
    public function seatMonitor()
    {
        // 1. ดึงข้อมูลโซนและเครื่องทั้งหมดพร้อมข้อมูลเซสชันที่กำลังเล่น
        $zones = Zone::with(['seats.zone', 'seats.sessions' => function ($q) {
            $q->where('status', 'active')->with(['user', 'userPackage.package']);
        }])->get();

        $seats = Seat::with(['zone', 'sessions' => function ($q) {
            $q->where('status', 'active')->with(['user', 'userPackage.package']);
        }])->orderBy('seat_number')->get();

        // 2. สรุปจำนวนเครื่องตามสถานะ
        $totalSeats = Seat::count();
        $occupiedSeats = Seat::where('status', 'occupied')->count();
        $availableSeats = Seat::where('status', 'available')->count();
        $maintenanceSeats = Seat::where('status', 'maintenance')->count();

        return view('pages.staff.seat-monitor', [
            'zones' => $zones,
            'seats' => $seats,
            'totalSeats' => $totalSeats,
            'occupiedSeats' => $occupiedSeats,
            'availableSeats' => $availableSeats,
            'maintenanceSeats' => $maintenanceSeats,
        ]);
    }

    /**
     * ฟังก์ชันบังคับปิดเครื่องและคิดเงิน (โดยพนักงาน)
     * สมาชิกคนที่ 5: ระบบพนักงานและครัว (Member 5)
     */
    public function forceEnd(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:seat_sessions,id',
        ]);

        $sessionId = (int) $request->input('session_id');

        // 1. ค้นหาข้อมูลเซสชัน
        $session = SeatSession::find($sessionId);
        if ($session == null) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลเซสชัน');
        }

        $seat = Seat::find($session->seat_id);
        $user = User::find($session->user_id);

        // 2. คำนวณเวลาที่เล่นไป
        $startTime = Carbon::parse($session->start_time);
        $endTime = Carbon::now();
        $usedSeconds = $startTime->diffInSeconds($endTime);

        $usedMinutes = (int) ceil($usedSeconds / 60);
        if ($usedMinutes < 1) {
            $usedMinutes = 1;
        }

        $totalCost = 0.00;

        // 3. คิดค่าบริการตามเงื่อนไข
        if ($session->user_package_id != null) {
            $userPackage = UserPackage::find($session->user_package_id);
            if ($userPackage != null) {
                if ($userPackage->remaining_minutes >= $usedMinutes) {
                    $userPackage->remaining_minutes = $userPackage->remaining_minutes - $usedMinutes;
                    $userPackage->save();
                } else {
                    $excessMinutes = $usedMinutes - $userPackage->remaining_minutes;
                    $userPackage->remaining_minutes = 0;
                    $userPackage->save();

                    $excessHours = $excessMinutes / 60;
                    $hourlyRate = (float) $session->rate_snapshot;
                    $totalCost = round($excessHours * $hourlyRate, 2);

                    if ($totalCost > 0 && $user != null) {
                        $user->balance = (float) $user->balance - $totalCost;
                        $user->save();

                        WalletTransaction::create([
                            'user_id' => $user->id,
                            'type' => 'deduct',
                            'amount' => $totalCost,
                            'ref_type' => 'session_overtime',
                            'ref_id' => $session->id,
                        ]);
                    }
                }
            }
        } else {
            $hours = $usedMinutes / 60;
            $hourlyRate = (float) $session->rate_snapshot;
            $totalCost = round($hours * $hourlyRate, 2);

            if ($totalCost > 0 && $user != null) {
                $user->balance = (float) $user->balance - $totalCost;
                $user->save();

                WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'deduct',
                    'amount' => $totalCost,
                    'ref_type' => 'session',
                    'ref_id' => $session->id,
                ]);
            }
        }

        // 4. บันทึกปิดเซสชันและคืนสถานะที่นั่ง
        $session->end_time = $endTime;
        $session->total_cost = $totalCost;
        $session->status = 'completed';
        $session->save();

        if ($seat != null) {
            $seat->status = 'available';
            $seat->save();
        }

        return redirect()->back()->with('success', 'ปิดเครื่อง ' . ($seat ? $seat->seat_number : '') . ' และคิดเงินสำเร็จเรียบร้อยแล้ว');
    }

    /**
     * ฟังก์ชันสลับสถานะเครื่องระหว่าง "พร้อมใช้งาน" กับ "ซ่อมบำรุง"
     * สมาชิกคนที่ 5: ระบบพนักงานและครัว (Member 5)
     */
    public function toggleMaintenance(Request $request)
    {
        $request->validate([
            'seat_id' => 'required|exists:seats,id',
        ]);

        $seatId = (int) $request->input('seat_id');
        $seat = Seat::find($seatId);

        if ($seat == null) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลเครื่อง');
        }

        if ($seat->status == 'available') {
            $seat->status = 'maintenance';
            $seat->save();
            return redirect()->back()->with('success', 'ปรับสถานะเครื่อง ' . $seat->seat_number . ' เป็นซ่อมบำรุง');
        } elseif ($seat->status == 'maintenance') {
            $seat->status = 'available';
            $seat->save();
            return redirect()->back()->with('success', 'ปรับสถานะเครื่อง ' . $seat->seat_number . ' เป็นพร้อมใช้งาน');
        }

        return redirect()->back()->with('error', 'ไม่สามารถปรับสถานะเครื่องที่กำลังใช้งานอยู่ได้');
    }

    /**
     * แสดงหน้าคิวอาหารในห้องครัว (Kitchen Queue)
     * สมาชิกคนที่ 5: ระบบพนักงานและครัว (Member 5)
     */
    public function kitchenQueue(Request $request)
    {
        $filterStatus = $request->input('status', 'active');

        $query = Order::with(['items.product', 'seat', 'user']);
        if ($filterStatus == 'active') {
            $query->whereIn('order_status', ['pending', 'preparing']);
        } elseif ($filterStatus == 'served') {
            $query->where('order_status', 'served');
        } elseif ($filterStatus == 'cancelled') {
            $query->where('order_status', 'cancelled');
        }
        $orders = $query->orderBy('created_at', 'asc')->get();

        $pendingCount = Order::where('order_status', 'pending')->count();
        $preparingCount = Order::where('order_status', 'preparing')->count();

        return view('pages.staff.kitchen-queue', [
            'orders' => $orders,
            'activeOrders' => $orders,
            'filterStatus' => $filterStatus,
            'pendingCount' => $pendingCount,
            'preparingCount' => $preparingCount,
        ]);
    }

    /**
     * ฟังก์ชันอัปเดตสถานะอาหารในครัว
     * สมาชิกคนที่ 5: ระบบพนักงานและครัว (Member 5)
     */
    public function updateOrderStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:pending,preparing,served,cancelled',
        ]);

        $orderId = (int) $request->input('order_id');
        $status = $request->input('status');
        $order = Order::find($orderId);

        if ($order == null) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลออเดอร์');
        }

        // ถ้ากดยกเลิกออเดอร์ ให้คืนสต็อกสินค้า
        if ($status == 'cancelled' && $order->order_status != 'cancelled') {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product != null) {
                    $product->stock_quantity = $product->stock_quantity + $item->quantity;
                    $product->save();
                }
            }
        }

        $order->order_status = $status;
        $order->save();

        return redirect()->back()->with('success', 'อัปเดตสถานะออเดอร์ #' . $order->id . ' เป็น ' . $status . ' เรียบร้อยแล้ว');
    }

    /**
     * ฟังก์ชันยืนยันการรับเงินสด
     * สมาชิกคนที่ 5: ระบบพนักงานและครัว (Member 5)
     */
    public function confirmCashPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $orderId = (int) $request->input('order_id');
        $order = Order::find($orderId);

        if ($order == null) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลออเดอร์');
        }

        if ($order->payment_method != 'cash') {
            return redirect()->back()->with('error', 'ออเดอร์นี้ไม่ได้ชำระด้วยเงินสด');
        }

        $order->payment_status = 'paid';
        $order->save();

        return redirect()->back()->with('success', 'ยืนยันรับเงินสดออเดอร์ #' . $order->id . ' ยอด ฿' . number_format($order->total_amount, 2) . ' สำเร็จ');
    }
}
