<?php

namespace App\Livewire\Customer;

use App\Models\Package;
use App\Models\UserPackage;
use App\Services\BillingService;
use App\Services\WalletService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Top-up & Buy Packages')]
class Topup extends Component
{
    public float $amount = 100.00;
    public string $topupMethod = 'qr'; // qr, cash

    public function selectAmount(float $val)
    {
        $this->amount = $val;
    }

    public function doTopup(WalletService $walletService)
    {
        if ($this->amount <= 0) {
            session()->flash('error', 'กรุณาระบุจำนวนเงินที่ต้องการเติมมากกว่า 0 บาท');
            return;
        }

        try {
            $user = Auth::user();
            $refType = $this->topupMethod === 'qr' ? 'qr_topup' : 'cash_topup';
            $walletService->topUp($user, $this->amount, $refType);
            session()->flash('success', "เติมเงินสำเร็จ ฿" . number_format($this->amount, 2) . " ยอดคงเหลือถูกอัปเดตเรียบร้อยแล้ว");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function buyPackage(int $packageId, BillingService $billingService)
    {
        $user = Auth::user();

        try {
            $upkg = $billingService->buyPackage($user, $packageId);
            session()->flash('success', "ซื้อแพ็กเกจ {$upkg->package->name} สำเร็จ! ได้รับเวลาสะสม {$upkg->remaining_minutes} นาที");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();
        $packages = Package::with('zone')->get();

        $myPackages = UserPackage::with('package')
            ->where('user_id', $user->id)
            ->where('remaining_minutes', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expired_at')
                    ->orWhere('expired_at', '>', Carbon::now());
            })
            ->latest()
            ->get();

        return view('livewire.customer.topup', [
            'user' => $user,
            'packages' => $packages,
            'myPackages' => $myPackages,
        ]);
    }
}
