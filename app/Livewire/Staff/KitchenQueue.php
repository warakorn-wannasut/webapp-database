<?php

namespace App\Livewire\Staff;

use App\Models\Order;
use App\Services\OrderService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kitchen Order Queue')]
class KitchenQueue extends Component
{
    public string $filterStatus = 'active'; // active (pending/preparing), served, cancelled, all

    public function updateStatus(int $orderId, string $status, OrderService $orderService)
    {
        $order = Order::findOrFail($orderId);
        try {
            $orderService->updateOrderStatus($order, $status);
            session()->flash('success', "อัปเดตสถานะออเดอร์ #{$order->id} เป็น '{$status}' เรียบร้อยแล้ว");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function confirmCash(int $orderId, OrderService $orderService)
    {
        $order = Order::findOrFail($orderId);
        try {
            $orderService->confirmCashPayment($order);
            session()->flash('success', "ยืนยันรับเงินสดออเดอร์ #{$order->id} ยอด ฿" . number_format($order->total_amount, 2) . " สำเร็จ");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $query = Order::with(['user', 'seat', 'items.product'])->latest();

        if ($this->filterStatus === 'active') {
            $query->whereIn('order_status', ['pending', 'preparing']);
        } elseif ($this->filterStatus !== 'all') {
            $query->where('order_status', $this->filterStatus);
        }

        $orders = $query->paginate(15);

        $pendingCount = Order::where('order_status', 'pending')->count();
        $preparingCount = Order::where('order_status', 'preparing')->count();

        return view('livewire.staff.kitchen-queue', [
            'orders' => $orders,
            'pendingCount' => $pendingCount,
            'preparingCount' => $preparingCount,
        ]);
    }
}
