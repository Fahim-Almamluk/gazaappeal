<?php

namespace Database\Factories;

use App\Enums\VolunteerExpertise;
use App\Enums\VolunteerStatus;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Factories\Factory;

final class VolunteerFactory extends Factory
{
    protected $model = Volunteer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('+9705########'),
            'country' => fake()->country(),
            'expertise' => fake()->randomElement(VolunteerExpertise::cases()),
            'experience_years' => fake()->numberBetween(1, 30),
            'message' => fake()->paragraph(),
            'status' => VolunteerStatus::New,
        ];
    }
}
