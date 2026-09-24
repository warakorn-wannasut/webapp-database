<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'seat_id',
        'session_id',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
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

    public function session()
    {
        return $this->belongsTo(SeatSession::class, 'session_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
