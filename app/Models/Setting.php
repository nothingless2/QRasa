<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * Disable mass assignment protection for simplicity, 
     * given this is a single-row restricted dictionary settings pattern controlled solely by Admins.
     */
    protected $guarded = [];

    protected $casts = [
        'operational_hours' => 'array',
    ];
}
