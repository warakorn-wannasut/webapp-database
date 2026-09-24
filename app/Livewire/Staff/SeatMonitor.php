<?php

namespace App\Livewire\Staff;

use App\Models\Seat;
use App\Models\SeatSession;
use App\Models\Zone;
use App\Services\BillingService;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Staff Seat Monitor')]
class SeatMonitor extends Component
{
    public function forceEnd(int $sessionId, BillingService $billingService)
    {
        $session = SeatSession::findOrFail($sessionId);

        try {
            $billingService->forceEndSession($session);
            session()->flash('success', "ปิดเครื่อง {$session->seat->seat_number} และคิดเงินสำเร็จเรียบร้อยแล้ว");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function toggleMaintenance(int $seatId)
    {
        $seat = Seat::findOrFail($seatId);
        if ($seat->status === 'available') {
            $seat->update(['status' => 'maintenance']);
            session()->flash('success', "ปรับสถานะเครื่อง {$seat->seat_number} เป็นซ่อมบำรุง");
        } elseif ($seat->status === 'maintenance') {
            $seat->update(['status' => 'available']);
            session()->flash('success', "ปรับสถานะเครื่อง {$seat->seat_number} เป็นพร้อมใช้งาน");
        }
    }

    public function render()
    {
        $zones = Zone::with(['seats' => function ($query) {
            $query->with(['activeSession.user', 'activeSession.userPackage.package'])
                ->orderBy('seat_number');
        }])->get();

        $totalSeats = Seat::count();
        $occupiedSeats = Seat::where('status', 'occupied')->count();
        $availableSeats = Seat::where('status', 'available')->count();
        $maintenanceSeats = Seat::where('status', 'maintenance')->count();

        return view('livewire.staff.seat-monitor', [
            'zones' => $zones,
            'totalSeats' => $totalSeats,
            'occupiedSeats' => $occupiedSeats,
            'availableSeats' => $availableSeats,
            'maintenanceSeats' => $maintenanceSeats,
        ]);
    }
}
