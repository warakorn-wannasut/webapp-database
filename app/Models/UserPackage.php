<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPackage extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'remaining_minutes',
        'purchased_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'remaining_minutes' => 'integer',
            'purchased_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function seatSessions()
    {
        return $this->hasMany(SeatSession::class);
    }

    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    public function formattedRemainingTime(): string
    {
        return self::formatMinutes($this->remaining_minutes);
    }

    public static function formatMinutes(int|float $totalMinutes): string
    {
        $mins = (int) floor($totalMinutes);
        if ($mins <= 0) {
            return '0 นาที';
        }

        $days = (int) floor($mins / 1440);
        $hours = (int) floor(($mins % 1440) / 60);
        $minutes = $mins % 60;

        $parts = [];
        if ($days > 0) {
            $parts[] = "{$days} วัน";
        }
        if ($hours > 0) {
            $parts[] = "{$hours} ชั่วโมง";
        }
        if ($minutes > 0 || empty($parts)) {
            $parts[] = "{$minutes} นาที";
        }

        return implode(' ', $parts);
    }
}
