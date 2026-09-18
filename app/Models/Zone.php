<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = [
        'name',
        'hourly_rate',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'decimal:2',
        ];
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}
