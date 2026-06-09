<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'nama',
        'deskripsi',
        'harga',
        'diskon',
        'stok',
        'kategori',
        'gambar',
    ];

    public function pesans()
    {
        return $this->belongsToMany(Pesan::class)->withPivot('quantity', 'notes');
    }
}
