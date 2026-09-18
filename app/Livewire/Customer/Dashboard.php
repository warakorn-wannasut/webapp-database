<?php

namespace App\Livewire\Customer;

use App\Models\SeatSession;
use App\Models\UserPackage;
use App\Models\WalletTransaction;
use App\Models\Order;
use App\Services\BillingService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Customer Dashboard')]
class Dashboard extends Component
{
    public function checkOut(BillingService $billingService)
    {
        $user = Auth::user();
        $session = SeatSession::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (! $session) {
            session()->flash('error', 'ไม่พบเซสชันที่กำลังใช้งานอยู่');
            return;
        }

        try {
            $billingService->checkOut($session);
            session()->flash('success', 'เช็คเอาท์ออกจากเครื่องสำเร็จ');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();

        // Active session
        $activeSession = SeatSession::with(['seat.zone', 'userPackage.package'])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        $elapsedMinutes = 0;
        $estimatedCost = 0.00;
        if ($activeSession) {
            $startTime = Carbon::parse($activeSession->start_time);
            $elapsedSeconds = max(1, (int) $startTime->diffInSeconds(Carbon::now()));
            $elapsedMinutes = max(1, (int) ceil($elapsedSeconds / 60));

            if ($activeSession->user_package_id && $activeSession->userPackage) {
                if ($elapsedMinutes > $activeSession->userPackage->remaining_minutes) {
                    $excess = $elapsedMinutes - $activeSession->userPackage->remaining_minutes;
                    $estimatedCost = round(($excess / 60) * (float) $activeSession->rate_snapshot, 2);
                }
            } else {
                $estimatedCost = round(($elapsedMinutes / 60) * (float) $activeSession->rate_snapshot, 2);
            }
        }

        // Active packages
        $userPackages = UserPackage::with('package')
            ->where('user_id', $user->id)
            ->where('remaining_minutes', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expired_at')
                    ->orWhere('expired_at', '>', Carbon::now());
            })
            ->latest()
            ->get();

        // Recent wallet transactions
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Recent food orders
        $recentOrders = Order::with('seat')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.customer.dashboard', [
            'user' => $user,
            'activeSession' => $activeSession,
            'elapsedMinutes' => $elapsedMinutes,
            'estimatedCost' => $estimatedCost,
            'userPackages' => $userPackages,
            'transactions' => $transactions,
            'recentOrders' => $recentOrders,
        ]);
    }
}
