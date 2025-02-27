<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name_ar' => $this->faker->word(), // Generate a random category name
            'name_en' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, 10, 1000), // Random price between 10 and 1000
            'image' => $this->faker->imageUrl(200, 200, 'products'), // Generates a fake image URL
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id ?? \App\Models\Category::factory(), // Get random category or create one
        ];
    }
}
