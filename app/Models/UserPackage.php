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
}
