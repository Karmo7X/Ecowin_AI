<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
     protected $model = Brand::class;
    public function definition(): array
    {
        return [
            'name_ar' => $this->faker->word(), // Generate a random category name
            'name_en' => $this->faker->word(),
            'brand_image' => $this->faker->imageUrl(200, 200, 'brands') // Fake category image URL
        ];
    }
}
