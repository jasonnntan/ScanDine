<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'sst_enabled',
        'sst_rate',
        'service_charge_enabled',
        'service_charge_rate',
    ];

    protected $casts = [
        'sst_enabled' => 'boolean',
        'sst_rate' => 'float',
        'service_charge_enabled' => 'boolean',
        'service_charge_rate' => 'float',
    ];

    // there is only ever one settings row; create it with the defaults if it is missing
    public static function current()
    {
        return static::first() ?? static::create([]);
    }
}
