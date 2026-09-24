<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeatSession extends Model
{
    protected $fillable = [
        'user_id',
        'seat_id',
        'user_package_id',
        'rate_snapshot',
        'start_time',
        'end_time',
        'total_cost',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'rate_snapshot' => 'decimal:2',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'total_cost' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function userPackage()
    {
        return $this->belongsTo(UserPackage::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'session_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
