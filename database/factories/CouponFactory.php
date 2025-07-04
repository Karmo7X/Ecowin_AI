<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(10)),
            'discount_value' => $this->faker->randomFloat(2, 5, 50),
            'price' => $this->faker->randomFloat(2, 50, 500),
//            'user_id' => 1,
            'brand_id' => 1, // Generates a fake image URL
            'expires_at' => Carbon::now()->addDays(90),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
