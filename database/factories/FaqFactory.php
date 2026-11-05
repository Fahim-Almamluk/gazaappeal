<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

final class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition(): array
    {
        return [
            'question' => fake()->sentence(6).'؟',
            'answer' => fake()->paragraph(),
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
