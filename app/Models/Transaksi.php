<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'user_id',
        'invoice_number',
        'order_id',
        'items',
        'buyer',
        'total',
        'status',
        'cart_ids',
        'is_shipping',
    ];

    protected $casts = [
        'items' => 'json',
        'buyer' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    // PENDING', 'SUCCESS', 'FAILED
    public function setSuccess()
    {
        $this->attributes['status'] = 'SUCCESS';
        $this->attributes['paid_at'] = now();
        self::save();
    }

    /**
     * Set status to Failed
     *
     * @return void
     */
    public function setFailed($isExpired = false)
    {
        $this->attributes['status'] = 'FAILED';
        $this->attributes['expired_at'] = $isExpired ? now() : null;
        self::save();
    }

    // delete cart
    public function deleteCart()
    {
        $cartIds = explode(',', $this->cart_ids);
        Keranjang::whereIn('id', $cartIds)->delete();
    }
}
