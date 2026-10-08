<?php

namespace App\Services;

use App\Models\Seat;
use App\Models\SeatSession;
use App\Models\User;
use App\Models\UserPackage;
use App\Models\WalletTransaction;
use Illuminate\Support\Carbon;

class EndSeatSession
{

    // คิดค่าบริการ ปิด session และคืนเครื่องให้เป็นเครื่องว่าง
    // ใช้ร่วมกันทั้งการเช็คเอาท์ของลูกค้าและการปิดเครื่องโดยพนักงาน

    public function handle(SeatSession $session): float
    {
        $user = User::find($session->user_id);
        $seat = Seat::find($session->seat_id);
        $endTime = Carbon::now();
        $usedSeconds = Carbon::parse($session->start_time)->diffInSeconds($endTime);
        $usedMinutes = max(1, (int) ceil($usedSeconds / 60));
        $totalCost = 0.00;

        if ($session->user_package_id !== null) {
            $userPackage = UserPackage::find($session->user_package_id);

            if ($userPackage !== null) {
                if ($userPackage->remaining_minutes >= $usedMinutes) {
                    $userPackage->remaining_minutes -= $usedMinutes;
                    $userPackage->save();
                } else {
                    $excessMinutes = $usedMinutes - $userPackage->remaining_minutes;
                    $userPackage->remaining_minutes = 0;
                    $userPackage->save();

                    $totalCost = round(($excessMinutes / 60) * (float) $session->rate_snapshot, 2);
                    $this->deductFromWallet($user, $session, $totalCost, 'session_overtime');
                }
            }
        } else {
            $totalCost = round(($usedMinutes / 60) * (float) $session->rate_snapshot, 2);
            $this->deductFromWallet($user, $session, $totalCost, 'session');
        }

        $session->end_time = $endTime;
        $session->total_cost = $totalCost;
        $session->status = 'completed';
        $session->save();

        if ($seat !== null) {
            $seat->status = 'available';
            $seat->save();
        }

        return $totalCost;
    }

    private function deductFromWallet(?User $user, SeatSession $session, float $amount, string $referenceType): void
    {
        if ($amount <= 0 || $user === null) {
            return;
        }

        $user->balance = (float) $user->balance - $amount;
        $user->save();

        WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'deduct',
            'amount' => $amount,
            'ref_type' => $referenceType,
            'ref_id' => $session->id,
        ]);
    }
}
