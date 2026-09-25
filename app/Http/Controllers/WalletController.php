<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\User;
use App\Models\UserPackage;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * แสดงหน้ากระเป๋าเงิน เติมเงิน และรายการแพ็กเกจเวลา
     * สมาชิกคนที่ 4: ระบบกระเป๋าเงินและการคิดเงิน (Member 4)
     */
    public function index()
    {
        $user = Auth::user();

        // 1. ดึงรายการแพ็กเกจทั้งหมดที่มีในร้าน
        $packages = Package::with('zone')->get();

        // 2. ดึงรายการแพ็กเกจที่ผู้ใช้ซื้อไว้และยังมีเวลาเหลือ
        $userPackages = UserPackage::with('package')
            ->where('user_id', $user->id)
            ->where('remaining_minutes', '>', 0)
            ->latest()
            ->get();

        // 3. ดึงประวัติการทำรายการกระเป๋าเงิน 10 รายการล่าสุด
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('pages.customer.topup', [
            'user' => $user,
            'packages' => $packages,
            'userPackages' => $userPackages,
            'myPackages' => $userPackages,
            'transactions' => $transactions,
        ]);
    }

    /**
     * ฟังก์ชันเติมเงินเข้ากระเป๋าเงิน (Wallet)
     * สมาชิกคนที่ 4: ระบบกระเป๋าเงินและการคิดเงิน (Member 4)
     */
    public function topUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'topup_method' => 'required|in:qr,cash',
        ]);

        $amount = (float) $request->input('amount');
        $topupMethod = $request->input('topup_method');
        $refType = ($topupMethod == 'qr') ? 'qr_topup' : 'cash_topup';

        // 1. ค้นหาผู้ใช้และเพิ่มยอดเงินในกระเป๋า
        $user = Auth::user();
        $freshUser = User::find($user->id);
        $freshUser->balance = (float) $freshUser->balance + $amount;
        $freshUser->save();

        // 2. บันทึกประวัติการเติมเงินลงตาราง wallet_transactions
        WalletTransaction::create([
            'user_id' => $freshUser->id,
            'type' => 'topup',
            'amount' => $amount,
            'ref_type' => $refType,
            'ref_id' => null,
        ]);

        return redirect()->back()->with('success', 'เติมเงินสำเร็จ ฿' . number_format($amount, 2) . ' ยอดเงินคงเหลืออัปเดตเรียบร้อยแล้ว');
    }

    /**
     * ฟังก์ชันซื้อแพ็กเกจเวลา
     * สมาชิกคนที่ 4: ระบบกระเป๋าเงินและการคิดเงิน (Member 4)
     */
    public function buyPackage(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        $packageId = (int) $request->input('package_id');
        $user = Auth::user();

        // 1. ค้นหาข้อมูลแพ็กเกจ
        $package = Package::find($packageId);
        if ($package == null) {
            return redirect()->back()->with('error', 'ไม่พบแพ็กเกจที่เลือก');
        }

        $packagePrice = (float) $package->price;
        $freshUser = User::find($user->id);

        // 2. ตรวจสอบว่าเงินในกระเป๋าพอซื้อไหม
        if ((float) $freshUser->balance < $packagePrice) {
            return redirect()->back()->with('error', 'ยอดเงินในกระเป๋าไม่พอซื้อแพ็กเกจนี้ กรุณาเติมเงินก่อน');
        }

        // 3. หักเงินค่าแพ็กเกจออกจากกระเป๋า
        $freshUser->balance = (float) $freshUser->balance - $packagePrice;
        $freshUser->save();

        // 4. บันทึกประวัติการหักเงินลงตาราง wallet_transactions
        WalletTransaction::create([
            'user_id' => $freshUser->id,
            'type' => 'deduct',
            'amount' => $packagePrice,
            'ref_type' => 'package_purchase',
            'ref_id' => $package->id,
        ]);

        // 5. บันทึกข้อมูลแพ็กเกจของสมาชิก (อายุการใช้งาน 30 วัน)
        $totalMinutes = $package->duration_hours * 60;
        UserPackage::create([
            'user_id' => $freshUser->id,
            'package_id' => $package->id,
            'remaining_minutes' => $totalMinutes,
            'purchased_at' => Carbon::now(),
            'expired_at' => Carbon::now()->addDays(30),
        ]);

        return redirect()->back()->with('success', 'ซื้อแพ็กเกจ ' . $package->name . ' สำเร็จ! ได้รับเวลา ' . $totalMinutes . ' นาที');
    }
}
