<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'question_ar' => 'ما هو ' . $this->faker->word . '؟',
            'question_en' => 'What is ' . $this->faker->word . '?',
            'answer_ar' => $this->faker->text(200),
            'answer_en' => $this->faker->text(200),
        ];
    }
}
