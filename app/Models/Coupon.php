<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_value',
        'price',
        "brand_id",
        'user_id',
        "expires_at",
        'redeemed_at',
        
    ];

    protected $casts = [
        'expires_at' => 'datetime', // يُنصح بهذه الإضافة للتعامل الأمثل مع التاريخ والوقت
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
