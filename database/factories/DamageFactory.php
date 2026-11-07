<?php

namespace Database\Factories;

use App\Enums\Governorate;
use App\Enums\LookupType;
use App\Models\Damage;
use App\Models\Lookup;
use Illuminate\Database\Eloquent\Factories\Factory;

final class DamageFactory extends Factory
{
    protected $model = Damage::class;

    public function definition(): array
    {
        return [
            'governorate' => fake()->randomElement(Governorate::cases()),
            'category_id' => fn (): int => Lookup::query()->ofType(LookupType::DamageCategory)->inRandomOrder()->value('id') ?? Lookup::factory()->type(LookupType::DamageCategory)->create()->id,
            'item' => fake()->words(3, true),
            'amount' => fake()->numberBetween(100_000, 90_000_000),
            'sort_order' => 0,
        ];
    }
}
