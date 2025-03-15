<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name_ar', 'name_en', 'brand_image'];

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($brand) {
            if ($brand->image) {
                Storage::disk('public')->delete($brand->image);
            }
        });
    }
}
