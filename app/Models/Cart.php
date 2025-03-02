<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'total_price'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public function refreshTotalPrice()
{
    $this->total_price = $this->cartItems->sum(fn($item) => $item->quantity * $item->price);
    $this->save();
}

}
