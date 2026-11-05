<?php

namespace Database\Factories;

use App\Enums\DonationMethodType;
use App\Models\DonationMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

final class DonationMethodFactory extends Factory
{
    protected $model = DonationMethod::class;

    public function definition(): array
    {
        return [
            'type' => DonationMethodType::Link,
            'name' => fake()->company(),
            'description' => fake()->sentence(8),
            'url' => fake()->url(),
            'is_published' => true,
            'sort_order' => 0,
        ];
    }

    public function bank(): static
    {
        return $this->state([
            'type' => DonationMethodType::BankTransfer,
            'url' => null,
            'bank_details' => [
                'bank_name' => fake()->company(),
                'beneficiary' => fake()->company(),
                'account_number' => (string) fake()->numberBetween(10000, 99999),
                'iban' => 'PS'.fake()->numerify('##########################'),
            ],
        ]);
    }
}
