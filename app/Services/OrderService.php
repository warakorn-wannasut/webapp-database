<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Seat;
use App\Models\SeatSession;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * Place a food & beverage order with atomic stock deduction.
     * $items = [ ['product_id' => 1, 'quantity' => 2], ... ]
     */
    public function placeOrder(User $user, int $seatId, array $items, string $paymentMethod): Order
    {
        if (empty($items)) {
            throw new Exception('กรุณาเลือกรายการอาหารอย่างน้อย 1 รายการ');
        }

        if (!in_array($paymentMethod, ['wallet', 'promptpay', 'cash'])) {
            throw new Exception('ช่องทางการชำระเงินไม่ถูกต้อง');
        }

        return DB::transaction(function () use ($user, $seatId, $items, $paymentMethod) {
            $seat = Seat::findOrFail($seatId);

            // Find active session for this user at this seat (optional)
            $session = SeatSession::where('user_id', $user->id)
                ->where('seat_id', $seat->id)
                ->where('status', 'active')
                ->latest()
                ->first();

            // Calculate total and lock products for atomic stock deduction
            $totalAmount = 0.00;
            $orderItemsData = [];

            foreach ($items as $item) {
                $productId = $item['product_id'];
                $quantity = (int) $item['quantity'];

                if ($quantity <= 0) {
                    continue;
                }

                $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

                if ($product->stock_quantity < $quantity) {
                    throw new Exception("สินค้า {$product->name} มีจำนวนคงเหลือไม่พอ (เหลือ {$product->stock_quantity} ชิ้น)");
                }

                $unitPrice = (float) $product->price;
                $subtotal = round($unitPrice * $quantity, 2);
                $totalAmount += $subtotal;

                // Decrement stock immediately
                $product->decrement('stock_quantity', $quantity);

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];
            }

            if (empty($orderItemsData)) {
                throw new Exception('ไม่มีรายการสินค้าที่ถูกต้อง');
            }

            // Determine payment status
            $paymentStatus = 'pending';
            if ($paymentMethod === 'wallet') {
                // Deduct from wallet
                $this->walletService->deduct($user, $totalAmount, 'order', null);
                $paymentStatus = 'paid';
            } elseif ($paymentMethod === 'promptpay') {
                // Simulated QR payment: marked paid upon placement
                $paymentStatus = 'paid';
            } elseif ($paymentMethod === 'cash') {
                // Cash on delivery: staff will confirm payment
                $paymentStatus = 'pending_payment';
            }

            $order = Order::create([
                'user_id' => $user->id,
                'seat_id' => $seat->id,
                'session_id' => $session?->id,
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'order_status' => 'pending',
            ]);

            // Save order items
            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            return $order;
        });
    }

    /**
     * Confirm cash payment by staff.
     */
    public function confirmCashPayment(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();
            $order->update(['payment_status' => 'paid']);
            return $order;
        });
    }

    /**
     * Update order status (pending -> preparing -> served / cancelled).
     */
    public function updateOrderStatus(Order $order, string $status): Order
    {
        return DB::transaction(function () use ($order, $status) {
            $order = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();

            if ($status === 'cancelled' && $order->order_status !== 'cancelled') {
                // Restore stock
                foreach ($order->items as $item) {
                    Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
                }

                // If paid with wallet, refund
                if ($order->payment_method === 'wallet' && $order->payment_status === 'paid') {
                    $user = User::findOrFail($order->user_id);
                    $this->walletService->refund($user, (float) $order->total_amount, 'order_refund', $order->id);
                }

                $order->payment_status = 'cancelled';
            }

            $order->update(['order_status' => $status]);
            return $order;
        });
    }
}
