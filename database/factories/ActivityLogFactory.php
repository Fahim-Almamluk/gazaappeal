<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event' => fake()->randomElement(ActivityLog::EVENTS),
            'subject_type' => fake()->randomElement(ActivityLog::SUBJECTS),
            'subject_id' => fake()->numberBetween(1, 500),
            'subject_label' => fake()->sentence(3),
            'changes' => null,
        ];
    }
}
