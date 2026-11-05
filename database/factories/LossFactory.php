<?php

namespace Database\Factories;

use App\Enums\LossIcon;
use App\Models\Loss;
use Illuminate\Database\Eloquent\Factories\Factory;

final class LossFactory extends Factory
{
    protected $model = Loss::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'figure' => fake()->numberBetween(10, 900),
            'unit' => fake()->word(),
            'description' => fake()->sentence(14),
            'icon' => fake()->randomElement(LossIcon::cases()),
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
