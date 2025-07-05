<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Blog extends Model
{
    use HasFactory;
    protected $fillable = [
        'title_ar',
        'title_en',
        'body_ar',
        'body_en',
        'image',
    ];
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($product) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
        });
    }

    public function getImageUrlAttribute()
    {
        return Storage::url($this->image);
    }
}
