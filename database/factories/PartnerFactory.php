<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

final class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'website' => fake()->url(),
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
