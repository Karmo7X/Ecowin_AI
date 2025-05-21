<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $attributes = [
        'status' => 'pending',
    ];
    protected $fillable=['user_id', 'agent_id', 'address_id', 'points', 'status'];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function agent(){
        return $this->belongsTo(Agent::class);
    }
    public function orderItems()
    {
        return $this->hasMany(Order_item::class, 'order_id');
    }
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
