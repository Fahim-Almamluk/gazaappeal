<?php

namespace Database\Factories;

use App\Enums\DamageCategory;
use App\Enums\Governorate;
use App\Models\Damage;
use Illuminate\Database\Eloquent\Factories\Factory;

final class DamageFactory extends Factory
{
    protected $model = Damage::class;

    public function definition(): array
    {
        return [
            'governorate' => fake()->randomElement(Governorate::cases()),
            'category' => fake()->randomElement(DamageCategory::cases()),
            'item' => fake()->words(3, true),
            'amount' => fake()->numberBetween(100_000, 90_000_000),
            'sort_order' => 0,
        ];
    }
}
