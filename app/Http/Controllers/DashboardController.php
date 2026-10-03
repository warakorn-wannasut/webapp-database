<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Seat;
use App\Models\SeatSession;
use App\Models\User;
use App\Models\UserPackage;
use App\Models\WalletTransaction;
use App\Services\EndSeatSession;
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
        $sessionRemainingMinutes = null;

        if ($activeSession != null) {
            $startTime = Carbon::parse($activeSession->start_time);
            $usedSeconds = $startTime->diffInSeconds(Carbon::now());
            $elapsedMinutes = (int) ceil($usedSeconds / 60);
            if ($elapsedMinutes < 1) {
                $elapsedMinutes = 1;
            }

            if ($activeSession->user_package_id != null && $activeSession->userPackage != null) {
                $packageMins = (int) $activeSession->userPackage->remaining_minutes;
                $sessionRemainingMinutes = max(0, $packageMins - $elapsedMinutes);

                if ($elapsedMinutes > $packageMins) {
                    $excessMinutes = $elapsedMinutes - $packageMins;
                    $excessHours = $excessMinutes / 60;
                    $estimatedCost = round($excessHours * (float) $activeSession->rate_snapshot, 2);
                }
            } else {
                $hours = $elapsedMinutes / 60;
                $estimatedCost = round($hours * (float) $activeSession->rate_snapshot, 2);

                $hourlyRate = (float) $activeSession->rate_snapshot;
                if ($hourlyRate > 0) {
                    $totalAffordableMins = (int) floor(((float) $user->balance / $hourlyRate) * 60);
                    $sessionRemainingMinutes = max(0, $totalAffordableMins - $elapsedMinutes);
                }
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

        return view('pages.customer.dashboard', compact(
            'user',
            'activeSession',
            'elapsedMinutes',
            'estimatedCost',
            'availableBalance',
            'userPackages',
            'transactions',
            'recentOrders',
            'sessionRemainingMinutes'
        ));
    }

    /**
     * ฟังก์ชันเช็คเอาท์และปิดเครื่อง
     * สมาชิกคนที่ 1: ระบบแดชบอร์ดและโปรไฟล์ลูกค้า (Member 1)
     */
    public function checkOut(Request $request, EndSeatSession $endSeatSession)
    {
        $user = Auth::user();

        // 1. ค้นหาเครื่องที่ผู้ใช้กำลังเปิดใช้งานอยู่
        $session = SeatSession::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($session == null) {
            return redirect()->back()->with('error', 'ไม่พบเครื่องที่กำลังใช้งานอยู่');
        }

        // ส่งการคิดเงินและปิดเครื่องให้บริการกลาง เพื่อใช้กฎเดียวกับพนักงาน
        $endSeatSession->handle($session);

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

    /**
     * คลังรายชื่อเกมสำหรับ Game Launcher จำลองระบบหน้าจอร้านเกม
     */
    private function getGameCatalog(): array
    {
        return [
            [
                'id' => 'valorant',
                'name' => 'VALORANT',
                'category' => 'fps',
                'category_label' => 'FPS / ยิงปืน',
                'publisher' => 'Riot Games',
                'badge' => '🔥 ยอดนิยม #1',
                'badge_color' => 'red',
                'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=600&q=80',
                'version' => 'v10.4 (ล่าสุด)',
                'active_players' => 18,
                'rating' => '98%',
                'executable' => 'VALORANT.exe',
            ],
            [
                'id' => 'lol',
                'name' => 'League of Legends',
                'category' => 'moba',
                'category_label' => 'MOBA / วางแผน',
                'publisher' => 'Riot Games',
                'badge' => '🔥 ยอดนิยม #2',
                'badge_color' => 'blue',
                'image' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=600&q=80',
                'version' => 'Season 2026',
                'active_players' => 14,
                'rating' => '96%',
                'executable' => 'LeagueClient.exe',
            ],
            [
                'id' => 'cs2',
                'name' => 'Counter-Strike 2',
                'category' => 'fps',
                'category_label' => 'FPS / ยิงปืน',
                'publisher' => 'Valve',
                'badge' => '⚡ 240Hz Ready',
                'badge_color' => 'amber',
                'image' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=600&q=80',
                'version' => 'Source 2 Update',
                'active_players' => 11,
                'rating' => '94%',
                'executable' => 'cs2.exe',
            ],
            [
                'id' => 'gta5',
                'name' => 'Grand Theft Auto V (FiveM)',
                'category' => 'rpg',
                'category_label' => 'RPG / Open World',
                'publisher' => 'Rockstar Games',
                'badge' => '🏙️ FiveM Server',
                'badge_color' => 'purple',
                'image' => 'https://images.unsplash.com/photo-1579373903781-fd5c0c30c4cd?auto=format&fit=crop&w=600&q=80',
                'version' => 'Build 3095',
                'active_players' => 9,
                'rating' => '97%',
                'executable' => 'FiveM.exe',
            ],
            [
                'id' => 'roblox',
                'name' => 'Roblox Studio & Player',
                'category' => 'rpg',
                'category_label' => 'Sandbox / มินิเกม',
                'publisher' => 'Roblox Corporation',
                'badge' => '⭐ เล่นฟรีทุกวัย',
                'badge_color' => 'emerald',
                'image' => 'https://images.unsplash.com/photo-1563089145-599997674d42?auto=format&fit=crop&w=600&q=80',
                'version' => 'v2.64',
                'active_players' => 8,
                'rating' => '95%',
                'executable' => 'RobloxPlayerLauncher.exe',
            ],
            [
                'id' => 'pubg',
                'name' => 'PUBG: BATTLEGROUNDS',
                'category' => 'br',
                'category_label' => 'Battle Royale',
                'publisher' => 'Krafton',
                'badge' => '🪂 เซิร์ฟเวอร์เอเชีย',
                'badge_color' => 'amber',
                'image' => 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?auto=format&fit=crop&w=600&q=80',
                'version' => 'Patch 33.1',
                'active_players' => 7,
                'rating' => '91%',
                'executable' => 'TslGame.exe',
            ],
            [
                'id' => 'eafc24',
                'name' => 'EA SPORTS FC 24',
                'category' => 'sports',
                'category_label' => 'กีฬา / แข่งขัน',
                'publisher' => 'Electronic Arts',
                'badge' => '🎮 ต่อจอยเล่นได้',
                'badge_color' => 'emerald',
                'image' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=600&q=80',
                'version' => 'Title Update 18',
                'active_players' => 6,
                'rating' => '90%',
                'executable' => 'FC24.exe',
            ],
            [
                'id' => 'genshin',
                'name' => 'Genshin Impact',
                'category' => 'rpg',
                'category_label' => 'Action RPG',
                'publisher' => 'HoYoverse',
                'badge' => '✨ v5.2 Natlan',
                'badge_color' => 'cyan',
                'image' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=600&q=80',
                'version' => 'v5.2.0',
                'active_players' => 5,
                'rating' => '95%',
                'executable' => 'GenshinImpact.exe',
            ],
            [
                'id' => 'dota2',
                'name' => 'Dota 2',
                'category' => 'moba',
                'category_label' => 'MOBA / วางแผน',
                'publisher' => 'Valve',
                'badge' => '⚔️ Ranked Match',
                'badge_color' => 'red',
                'image' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=600&q=80',
                'version' => '7.37e',
                'active_players' => 6,
                'rating' => '93%',
                'executable' => 'dota2.exe',
            ],
            [
                'id' => 'apex',
                'name' => 'Apex Legends',
                'category' => 'br',
                'category_label' => 'Battle Royale',
                'publisher' => 'Respawn / EA',
                'badge' => '🎯 120 FPS High',
                'badge_color' => 'red',
                'image' => 'https://images.unsplash.com/photo-1542751110-97427bbecf20?auto=format&fit=crop&w=600&q=80',
                'version' => 'Season 23',
                'active_players' => 8,
                'rating' => '92%',
                'executable' => 'r5apex.exe',
            ],
            [
                'id' => 'overwatch2',
                'name' => 'Overwatch 2',
                'category' => 'fps',
                'category_label' => 'Team Action FPS',
                'publisher' => 'Blizzard Entertainment',
                'badge' => '🛡️ 5v5 Competitive',
                'badge_color' => 'amber',
                'image' => 'https://images.unsplash.com/photo-1560253023-3ec5d502959f?auto=format&fit=crop&w=600&q=80',
                'version' => 'Season 14',
                'active_players' => 5,
                'rating' => '89%',
                'executable' => 'Overwatch.exe',
            ],
            [
                'id' => 'minecraft',
                'name' => 'Minecraft (Java & Bedrock)',
                'category' => 'rpg',
                'category_label' => 'Sandbox / เอาชีวิตรอด',
                'publisher' => 'Mojang Studios',
                'badge' => '🧱 เซิร์ฟเวอร์ในร้าน',
                'badge_color' => 'emerald',
                'image' => 'https://images.unsplash.com/photo-1627856013091-fed6e4e30025?auto=format&fit=crop&w=600&q=80',
                'version' => '1.21.4 Tricky Trials',
                'active_players' => 7,
                'rating' => '99%',
                'executable' => 'MinecraftLauncher.exe',
            ],
        ];
    }
}
