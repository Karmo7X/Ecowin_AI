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
        'brand_ar',
        'brand_en',
        'user_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
