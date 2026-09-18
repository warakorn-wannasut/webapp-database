<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Seat;
use App\Models\SeatSession;
use App\Models\User;
use App\Models\UserPackage;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * Purchase a time package using wallet balance.
     */
    public function buyPackage(User $user, int $packageId): UserPackage
    {
        return DB::transaction(function () use ($user, $packageId) {
            $package = Package::findOrFail($packageId);

            // Deduct price from wallet
            $this->walletService->deduct($user, (float) $package->price, 'package_purchase', $package->id);

            return UserPackage::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'remaining_minutes' => $package->duration_hours * 60,
                'purchased_at' => Carbon::now(),
                'expired_at' => Carbon::now()->addDays(30), // valid for 30 days
            ]);
        });
    }

    /**
     * Check in to a seat.
     */
    public function checkIn(User $user, int $seatId, ?int $userPackageId = null): SeatSession
    {
        return DB::transaction(function () use ($user, $seatId, $userPackageId) {
            // Check if user already has an active session
            $hasActiveSession = SeatSession::where('user_id', $user->id)
                ->where('status', 'active')
                ->exists();

            if ($hasActiveSession) {
                throw new Exception('คุณมีเครื่องที่กำลังใช้งานอยู่แล้ว กรุณาเช็คเอาท์ก่อน');
            }

            // Check and lock seat
            $seat = Seat::where('id', $seatId)->lockForUpdate()->firstOrFail();
            if ($seat->status !== 'available') {
                throw new Exception('ที่นั่งนี้ไม่ว่าง หรืออยู่ระหว่างการซ่อมบำรุง');
            }

            // If user selected a package, validate it
            $validUserPackage = null;
            if ($userPackageId) {
                $validUserPackage = UserPackage::where('id', $userPackageId)
                    ->where('user_id', $user->id)
                    ->firstOrFail();

                if ($validUserPackage->remaining_minutes <= 0) {
                    throw new Exception('แพ็กเกจนี้เวลาหมดแล้ว');
                }
                if ($validUserPackage->isExpired()) {
                    throw new Exception('แพ็กเกจนี้หมดอายุแล้ว');
                }
            } else {
                // If pay as you go, verify user has some balance
                $freshUser = User::findOrFail($user->id);
                if ((float) $freshUser->balance <= 0) {
                    throw new Exception('ยอดเงินในกระเป๋าของคุณไม่เพียงพอสำหรับการเล่นแบบคิดตามจริง (Pay as you go)');
                }
            }

            // Snapshot rate from the zone
            $rateSnapshot = (float) $seat->zone->hourly_rate;

            $session = SeatSession::create([
                'user_id' => $user->id,
                'seat_id' => $seat->id,
                'user_package_id' => $validUserPackage?->id,
                'rate_snapshot' => $rateSnapshot,
                'start_time' => Carbon::now(),
                'status' => 'active',
                'total_cost' => 0.00,
            ]);

            $seat->update(['status' => 'occupied']);

            return $session;
        });
    }

    /**
     * Check out from a session and calculate billing.
     */
    public function checkOut(SeatSession $session): SeatSession
    {
        return DB::transaction(function () use ($session) {
            $session = SeatSession::where('id', $session->id)->lockForUpdate()->firstOrFail();
            if ($session->status !== 'active') {
                return $session;
            }

            $user = User::where('id', $session->user_id)->lockForUpdate()->firstOrFail();
            $seat = Seat::where('id', $session->seat_id)->lockForUpdate()->firstOrFail();

            $startTime = Carbon::parse($session->start_time);
            $endTime = Carbon::now();
            $usedSeconds = max(1, (int) $startTime->diffInSeconds($endTime));
            $usedMinutes = max(1, (int) ceil($usedSeconds / 60));

            $totalCost = 0.00;

            if ($session->user_package_id) {
                $userPackage = UserPackage::where('id', $session->user_package_id)->lockForUpdate()->first();
                if ($userPackage) {
                    if ($userPackage->remaining_minutes >= $usedMinutes) {
                        $userPackage->decrement('remaining_minutes', $usedMinutes);
                        $totalCost = 0.00;
                    } else {
                        $excessMinutes = $usedMinutes - $userPackage->remaining_minutes;
                        $userPackage->update(['remaining_minutes' => 0]);

                        // Bill excess minutes based on rate_snapshot
                        $totalCost = round(($excessMinutes / 60) * (float) $session->rate_snapshot, 2);
                        if ($totalCost > 0) {
                            $this->walletService->deduct($user, $totalCost, 'session_overtime', $session->id);
                        }
                    }
                }
            } else {
                // Pay as you go
                $totalCost = round(($usedMinutes / 60) * (float) $session->rate_snapshot, 2);
                if ($totalCost > 0) {
                    $this->walletService->deduct($user, $totalCost, 'session', $session->id);
                }
            }

            $session->update([
                'end_time' => $endTime,
                'total_cost' => $totalCost,
                'status' => 'completed',
            ]);

            $seat->update(['status' => 'available']);

            return $session;
        });
    }

    /**
     * Force end session by staff/admin.
     */
    public function forceEndSession(SeatSession $session): SeatSession
    {
        return $this->checkOut($session);
    }
}
