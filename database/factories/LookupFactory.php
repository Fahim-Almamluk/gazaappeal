<?php

namespace Database\Factories;

use App\Enums\LookupType;
use App\Models\Lookup;
use Illuminate\Database\Eloquent\Factories\Factory;

final class LookupFactory extends Factory
{
    protected $model = Lookup::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(LookupType::cases()),
            'name' => ['ar' => 'ثابت '.fake()->unique()->numberBetween(1, 99999), 'en' => fake()->words(2, true)],
            'icon' => fake()->randomElement(Lookup::ICONS),
            'tone' => null,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    public function type(LookupType $type): static
    {
        return $this->state(['type' => $type]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
