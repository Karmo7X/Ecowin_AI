<?php

namespace Database\Factories;

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
            'brand_ar' => $this->faker->word(),
            'brand_en' => $this->faker->word(),
            'user_id' => 1,
            'brand_image' => $this->faker->imageUrl(200, 200, 'brands'), // Generates a fake image URL
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
