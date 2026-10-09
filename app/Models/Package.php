<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'zone_id',
        'name',
        'duration_hours',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'duration_hours' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function userPackages()
    {
        return $this->hasMany(UserPackage::class);
    }

    public function normalPrice(): ?float
    {
        if ($this->zone) {
            return (float) $this->zone->hourly_rate * $this->duration_hours;
        }
        return null;
    }

    public function savingsAmount(): ?float
    {
        $normal = $this->normalPrice();
        if ($normal !== null) {
            return max(0.0, round($normal - (float) $this->price, 2));
        }
        return null;
    }

    public function formattedDuration(): string
    {
        return UserPackage::formatMinutes($this->duration_hours * 60);
    }
}
