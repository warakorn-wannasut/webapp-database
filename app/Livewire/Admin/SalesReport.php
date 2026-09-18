<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SeatSession;
use App\Models\WalletTransaction;
use App\Models\Zone;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Sales & Revenue Report')]
class SalesReport extends Component
{
    public string $period = 'all'; // today, 7days, month, all

    public function render()
    {
        $startDate = null;
        if ($this->period === 'today') {
            $startDate = Carbon::today();
        } elseif ($this->period === '7days') {
            $startDate = Carbon::now()->subDays(7);
        } elseif ($this->period === 'month') {
            $startDate = Carbon::now()->subDays(30);
        }

        // 1. Hourly Sessions Revenue
        $sessionsQuery = SeatSession::where('status', 'completed');
        if ($startDate) {
            $sessionsQuery->where('created_at', '>=', $startDate);
        }
        $hourlyRevenue = (float) $sessionsQuery->sum('total_cost');
        $completedSessionsCount = $sessionsQuery->count();

        // 2. Package Purchases Revenue
        $pkgTxQuery = WalletTransaction::where('ref_type', 'package_purchase');
        if ($startDate) {
            $pkgTxQuery->where('created_at', '>=', $startDate);
        }
        $packageRevenue = (float) $pkgTxQuery->sum('amount');
        $packageSalesCount = $pkgTxQuery->count();

        // Total Gaming Revenue
        $totalGamingRevenue = $hourlyRevenue + $packageRevenue;

        // 3. Food & Drink Revenue
        $ordersQuery = Order::where('payment_status', 'paid');
        if ($startDate) {
            $ordersQuery->where('created_at', '>=', $startDate);
        }
        $foodRevenue = (float) $ordersQuery->sum('total_amount');
        $paidOrdersCount = $ordersQuery->count();

        // Total Combined Revenue
        $grandTotalRevenue = $totalGamingRevenue + $foodRevenue;

        // 4. Total Topup
        $topupQuery = WalletTransaction::where('type', 'topup');
        if ($startDate) {
            $topupQuery->where('created_at', '>=', $startDate);
        }
        $totalTopup = (float) $topupQuery->sum('amount');

        // 5. Top 5 Best Selling Items (Aggregate Query)
        $topProductsQuery = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_sales')
            )
            ->groupBy('order_items.product_id', 'products.name')
            ->orderByDesc('total_qty')
            ->take(5);

        if ($startDate) {
            $topProductsQuery->where('orders.created_at', '>=', $startDate);
        }
        $topProducts = $topProductsQuery->get();

        // 6. Revenue by Zone
        $zoneStats = Zone::withCount(['seats'])
            ->get()
            ->map(function ($zone) use ($startDate) {
                $q = SeatSession::whereHas('seat', function ($sq) use ($zone) {
                    $sq->where('zone_id', $zone->id);
                })->where('status', 'completed');

                if ($startDate) {
                    $q->where('created_at', '>=', $startDate);
                }

                $zoneRevenue = (float) $q->sum('total_cost');
                $zoneSessions = $q->count();

                return [
                    'zone' => $zone,
                    'revenue' => $zoneRevenue,
                    'sessions_count' => $zoneSessions,
                ];
            });

        return view('livewire.admin.sales-report', [
            'hourlyRevenue' => $hourlyRevenue,
            'packageRevenue' => $packageRevenue,
            'totalGamingRevenue' => $totalGamingRevenue,
            'foodRevenue' => $foodRevenue,
            'grandTotalRevenue' => $grandTotalRevenue,
            'totalTopup' => $totalTopup,
            'completedSessionsCount' => $completedSessionsCount,
            'packageSalesCount' => $packageSalesCount,
            'paidOrdersCount' => $paidOrdersCount,
            'topProducts' => $topProducts,
            'zoneStats' => $zoneStats,
        ]);
    }
}
