<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('+9705########'),
            'organization' => fake()->company(),
            'body' => fake()->paragraph(),
        ];
    }

    public function read(): static
    {
        return $this->state(['read_at' => now()]);
    }

    public function archived(): static
    {
        return $this->state(['read_at' => now(), 'archived_at' => now()]);
    }
}
