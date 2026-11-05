<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $goal = fake()->numberBetween(20, 400) * 1000;

        return [
            'title' => fake()->unique()->sentence(4),
            'summary' => fake()->sentence(12),
            'body' => fake()->paragraphs(3, true),
            'goal_amount' => $goal,
            'raised_amount' => fake()->numberBetween(0, $goal),
            'status' => ProjectStatus::Active,
            'is_urgent' => false,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }

    public function urgent(): static
    {
        return $this->state(['is_urgent' => true]);
    }

    public function hidden(): static
    {
        return $this->state(['is_published' => false]);
    }
}
