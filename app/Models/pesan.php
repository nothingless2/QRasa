<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    protected $fillable = [
        'total',
        'user_id',
        'status',
        'payment_method',
        'status_pembayaran',
        'meja_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meja()
    {
        return $this->belongsTo(Meja::class);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class)->withPivot('quantity', 'notes');
    }
}
