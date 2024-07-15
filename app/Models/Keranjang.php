<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    use HasFactory;

    protected $table = 'keranjang';

    protected $fillable = [
        'user_id',
        'item_id',
        'warna',
        'qty',
        'total'
    ];

    public function item()
    {
        return $this->belongsTo(Items::class, 'item_id');
    }

}
