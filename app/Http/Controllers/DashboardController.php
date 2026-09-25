<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Seat;
use App\Models\SeatSession;
use App\Models\User;
use App\Models\UserPackage;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * แสดงหน้าแดชบอร์ดหลักของลูกค้า
     * สมาชิกคนที่ 1: ระบบแดชบอร์ดและโปรไฟล์ลูกค้า (Member 1)
     */
    public function index()
    {
        $user = Auth::user();

        // 1. ตรวจสอบและตัดจบเซสชันที่เวลาหรือเงินหมดอัตโนมัติ
        $this->autoEndExpiredSessions();

        // 2. ดึงข้อมูลเครื่องที่กำลังเปิดใช้งานอยู่
        $activeSession = SeatSession::with(['seat.zone', 'userPackage.package'])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        // 3. คำนวณเวลาและค่าบริการที่ใช้ไปในเซสชันปัจจุบัน
        $elapsedMinutes = 0;
        $estimatedCost = 0.00;

        if ($activeSession != null) {
            $startTime = Carbon::parse($activeSession->start_time);
            $usedSeconds = $startTime->diffInSeconds(Carbon::now());
            $elapsedMinutes = (int) ceil($usedSeconds / 60);
            if ($elapsedMinutes < 1) {
                $elapsedMinutes = 1;
            }

            if ($activeSession->user_package_id != null && $activeSession->userPackage != null) {
                $packageMins = $activeSession->userPackage->remaining_minutes;
                if ($elapsedMinutes > $packageMins) {
                    $excessMinutes = $elapsedMinutes - $packageMins;
                    $excessHours = $excessMinutes / 60;
                    $estimatedCost = round($excessHours * (float) $activeSession->rate_snapshot, 2);
                }
            } else {
                $hours = $elapsedMinutes / 60;
                $estimatedCost = round($hours * (float) $activeSession->rate_snapshot, 2);
            }
        }

        // 4. คำนวณยอดเงินคงเหลือที่ใช้ได้จริง (หักค่าชั่วโมงที่กำลังเล่นอยู่)
        $availableBalance = $this->calculateAvailableBalance($user);

        // 5. ดึงรายการแพ็กเกจที่ผู้ใช้ซื้อไว้
        $userPackages = UserPackage::with('package')
            ->where('user_id', $user->id)
            ->where('remaining_minutes', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expired_at')
                    ->orWhere('expired_at', '>', Carbon::now());
            })
            ->latest()
            ->get();

        // 6. ดึงประวัติการเติมเงินและสั่งอาหารล่าสุด
        $transactions = WalletTransaction::where('user_id', $user->id)->latest()->take(5)->get();
        $recentOrders = Order::with('seat')->where('user_id', $user->id)->latest()->take(5)->get();

        return view('pages.customer.dashboard', [
            'user' => $user,
            'activeSession' => $activeSession,
            'elapsedMinutes' => $elapsedMinutes,
            'estimatedCost' => $estimatedCost,
            'availableBalance' => $availableBalance,
            'userPackages' => $userPackages,
            'transactions' => $transactions,
            'recentOrders' => $recentOrders,
        ]);
    }

    /**
     * ฟังก์ชันเช็คเอาท์และปิดเครื่อง
     * สมาชิกคนที่ 1: ระบบแดชบอร์ดและโปรไฟล์ลูกค้า (Member 1)
     */
    public function checkOut(Request $request)
    {
        $user = Auth::user();

        // 1. ค้นหาเครื่องที่ผู้ใช้กำลังเปิดใช้งานอยู่
        $session = SeatSession::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($session == null) {
            return redirect()->back()->with('error', 'ไม่พบเครื่องที่กำลังใช้งานอยู่');
        }

        $seat = Seat::find($session->seat_id);
        $freshUser = User::find($user->id);

        // 2. คำนวณเวลาที่เล่นไป
        $startTime = Carbon::parse($session->start_time);
        $endTime = Carbon::now();
        $usedSeconds = $startTime->diffInSeconds($endTime);

        $usedMinutes = (int) ceil($usedSeconds / 60);
        if ($usedMinutes < 1) {
            $usedMinutes = 1;
        }

        $totalCost = 0.00;

        // 3. ตรวจสอบว่าเป็นแบบใช้แพ็กเกจหรือแบบคิดตามจริง
        if ($session->user_package_id != null) {
            $userPackage = UserPackage::find($session->user_package_id);
            if ($userPackage != null) {
                if ($userPackage->remaining_minutes >= $usedMinutes) {
                    $userPackage->remaining_minutes = $userPackage->remaining_minutes - $usedMinutes;
                    $userPackage->save();
                    $totalCost = 0.00;
                } else {
                    $excessMinutes = $usedMinutes - $userPackage->remaining_minutes;
                    $userPackage->remaining_minutes = 0;
                    $userPackage->save();

                    $excessHours = $excessMinutes / 60;
                    $hourlyRate = (float) $session->rate_snapshot;
                    $totalCost = round($excessHours * $hourlyRate, 2);

                    if ($totalCost > 0) {
                        $freshUser->balance = $freshUser->balance - $totalCost;
                        $freshUser->save();

                        WalletTransaction::create([
                            'user_id' => $freshUser->id,
                            'type' => 'deduct',
                            'amount' => $totalCost,
                            'ref_type' => 'session_overtime',
                            'ref_id' => $session->id,
                        ]);
                    }
                }
            }
        } else {
            // กรณีเล่นแบบคิดตามจริง (Pay as you go)
            $hours = $usedMinutes / 60;
            $hourlyRate = (float) $session->rate_snapshot;
            $totalCost = round($hours * $hourlyRate, 2);

            if ($totalCost > 0) {
                $freshUser->balance = $freshUser->balance - $totalCost;
                $freshUser->save();

                WalletTransaction::create([
                    'user_id' => $freshUser->id,
                    'type' => 'deduct',
                    'amount' => $totalCost,
                    'ref_type' => 'session',
                    'ref_id' => $session->id,
                ]);
            }
        }

        // 4. บันทึกข้อมูลการสิ้นสุดการใช้งานลงตาราง seat_sessions
        $session->end_time = $endTime;
        $session->total_cost = $totalCost;
        $session->status = 'completed';
        $session->save();

        // 5. คืนสถานะที่นั่งเป็น available (ว่าง)
        if ($seat != null) {
            $seat->status = 'available';
            $seat->save();
        }

        return redirect()->back()->with('success', 'เช็คเอาท์ออกจากเครื่องสำเร็จ');
    }

    // คำนวณยอดเงินที่ใช้ได้จริงหลังหักค่าเครื่องที่กำลังเล่นอยู่
    private function calculateAvailableBalance(User $user): float
    {
        $freshUser = User::find($user->id);
        if ($freshUser == null) {
            return 0.00;
        }

        $activeSession = SeatSession::where('user_id', $freshUser->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if ($activeSession == null) {
            return max(0.00, (float) $freshUser->balance);
        }

        $startTime = Carbon::parse($activeSession->start_time);
        $usedSeconds = $startTime->diffInSeconds(Carbon::now());
        $usedMinutes = (int) ceil($usedSeconds / 60);
        if ($usedMinutes < 1) {
            $usedMinutes = 1;
        }

        $estimatedCost = 0.00;
        if ($activeSession->user_package_id != null && $activeSession->userPackage != null) {
            $remainingMinutes = $activeSession->userPackage->remaining_minutes;
            if ($usedMinutes > $remainingMinutes) {
                $excess = $usedMinutes - $remainingMinutes;
                $estimatedCost = round(($excess / 60) * (float) $activeSession->rate_snapshot, 2);
            }
        } else {
            $estimatedCost = round(($usedMinutes / 60) * (float) $activeSession->rate_snapshot, 2);
        }

        return max(0.00, round((float) $freshUser->balance - $estimatedCost, 2));
    }

    // ตรวจสอบและตัดจบเซสชันอัตโนมัติเมื่อเงินหมด
    private function autoEndExpiredSessions(): void
    {
        $activeSessions = SeatSession::where('status', 'active')->with(['user', 'userPackage'])->get();

        foreach ($activeSessions as $session) {
            $user = $session->user;
            if ($user == null) {
                continue;
            }

            $startTime = Carbon::parse($session->start_time);
            $usedSeconds = $startTime->diffInSeconds(Carbon::now());
            $usedMinutes = (int) ceil($usedSeconds / 60);

            $isExpired = false;

            if ($session->user_package_id != null && $session->userPackage != null) {
                if ($usedMinutes >= $session->userPackage->remaining_minutes && $user->balance <= 0) {
                    $isExpired = true;
                }
            } else {
                $cost = round(($usedMinutes / 60) * (float) $session->rate_snapshot, 2);
                if ($cost >= $user->balance && $user->balance <= 0) {
                    $isExpired = true;
                } elseif ($user->balance > 0) {
                    $maxMinutes = (int) floor(($user->balance / (float) $session->rate_snapshot) * 60);
                    if ($usedMinutes >= $maxMinutes && $maxMinutes > 0) {
                        $isExpired = true;
                    }
                }
            }

            if ($isExpired) {
                $seat = Seat::find($session->seat_id);
                $session->end_time = Carbon::now();
                $session->status = 'completed';
                $session->save();

                if ($seat != null) {
                    $seat->status = 'available';
                    $seat->save();
                }
            }
        }
    }
}
