<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Top up user's wallet balance.
     */
    public function topUp(User $user, float $amount, string $refType = 'cash_topup', ?int $refId = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new Exception('จำนวนเงินเติมต้องมากกว่า 0 บาท');
        }

        return DB::transaction(function () use ($user, $amount, $refType, $refId) {
            $user = User::where('id', $user->id)->lockForUpdate()->firstOrFail();
            $user->balance = (float) $user->balance + $amount;
            $user->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'topup',
                'amount' => $amount,
                'ref_type' => $refType,
                'ref_id' => $refId,
            ]);
        });
    }

    /**
     * Deduct user's wallet balance.
     */
    public function deduct(User $user, float $amount, string $refType, ?int $refId = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new Exception('จำนวนเงินที่หักต้องมากกว่า 0 บาท');
        }

        return DB::transaction(function () use ($user, $amount, $refType, $refId) {
            $user = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            if ((float) $user->balance < $amount) {
                throw new Exception("ยอดเงินคงเหลือไม่เพียงพอ (คงเหลือ: {$user->balance} บาท, ต้องการ: {$amount} บาท)");
            }

            $user->balance = (float) $user->balance - $amount;
            $user->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'deduct',
                'amount' => $amount,
                'ref_type' => $refType,
                'ref_id' => $refId,
            ]);
        });
    }

    /**
     * Refund user's wallet balance.
     */
    public function refund(User $user, float $amount, string $refType, ?int $refId = null): WalletTransaction
    {
        if ($amount <= 0) {
            return new WalletTransaction();
        }

        return DB::transaction(function () use ($user, $amount, $refType, $refId) {
            $user = User::where('id', $user->id)->lockForUpdate()->firstOrFail();
            $user->balance = (float) $user->balance + $amount;
            $user->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'refund',
                'amount' => $amount,
                'ref_type' => $refType,
                'ref_id' => $refId,
            ]);
        });
    }
}
