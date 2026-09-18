<?php

namespace App\Livewire\Customer;

use App\Models\Seat;
use App\Models\SeatSession;
use App\Models\UserPackage;
use App\Models\Zone;
use App\Services\BillingService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Seat Map & Check-in')]
class SeatMap extends Component
{
    public ?int $selectedSeatId = null;
    public string $billingMode = 'pay_as_you_go'; // 'pay_as_you_go' or 'package'
    public ?int $selectedUserPackageId = null;

    public function selectSeat(int $seatId)
    {
        $seat = Seat::findOrFail($seatId);
        if ($seat->status !== 'available') {
            session()->flash('error', "ที่นั่ง {$seat->seat_number} ไม่ว่างสำหรับเช็คอิน");
            return;
        }

        $this->selectedSeatId = $seatId;
        $this->resetErrorBag();
    }

    public function cancelSelection()
    {
        $this->selectedSeatId = null;
        $this->selectedUserPackageId = null;
    }

    public function checkIn(BillingService $billingService)
    {
        if (! $this->selectedSeatId) {
            return;
        }

        $user = Auth::user();
        $userPackageId = null;

        if ($this->billingMode === 'package') {
            if (! $this->selectedUserPackageId) {
                $this->addError('selectedUserPackageId', 'กรุณาเลือกแพ็กเกจที่ต้องการใช้งาน');
                return;
            }
            $userPackageId = $this->selectedUserPackageId;
        }

        try {
            $billingService->checkIn($user, $this->selectedSeatId, $userPackageId);
            $this->selectedSeatId = null;
            session()->flash('success', 'เปิดเครื่องและเช็คอินสำเร็จ!');
            return $this->redirect(route('dashboard'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();
        $zones = Zone::with(['seats' => function ($query) {
            $query->orderBy('seat_number');
        }])->get();

        $activeSession = SeatSession::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        $availablePackages = UserPackage::with('package')
            ->where('user_id', $user->id)
            ->where('remaining_minutes', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expired_at')
                    ->orWhere('expired_at', '>', Carbon::now());
            })
            ->get();

        $selectedSeat = $this->selectedSeatId ? Seat::with('zone')->find($this->selectedSeatId) : null;

        return view('livewire.customer.seat-map', [
            'zones' => $zones,
            'activeSession' => $activeSession,
            'availablePackages' => $availablePackages,
            'selectedSeat' => $selectedSeat,
            'user' => $user,
        ]);
    }
}
