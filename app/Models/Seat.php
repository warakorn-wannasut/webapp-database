<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = [
        'zone_id',
        'seat_number',
        'status',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function seatSessions()
    {
        return $this->hasMany(SeatSession::class);
    }

    public function activeSession()
    {
        return $this->hasOne(SeatSession::class)->where('status', 'active')->latestOfMany();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }
}
