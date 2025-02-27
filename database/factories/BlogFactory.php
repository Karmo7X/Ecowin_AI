<?php

namespace Database\Factories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Blog::class;
    public function definition(): array
    {
        return [
            'title_ar' => $this->faker->sentence,
            'title_en' => $this->faker->sentence,
            'body_ar' => $this->faker->paragraph,
            'body_en' => $this->faker->paragraph,
            'image' => $this->faker->imageUrl(640, 480, 'blog', true, 'Faker'), // Fake image URL
        ];
    }
}
