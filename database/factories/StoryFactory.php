<?php

namespace Database\Factories;

use App\Models\Story;
use Illuminate\Database\Eloquent\Factories\Factory;

final class StoryFactory extends Factory
{
    protected $model = Story::class;

    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(4),
            'summary' => fake()->sentence(12),
            'body' => fake()->paragraphs(3, true),
            'is_published' => true,
        ];
    }
}
