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
}
