<?php

namespace Database\Factories;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

final class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition(): array
    {
        return [
            'parent_id' => null,
            'page' => fake()->randomElement(array_keys(config('navigation.pages'))),
            'url' => null,
            'label' => null,
            'sort_order' => 0,
            'is_visible' => true,
        ];
    }

    public function link(string $url = 'https://example.com/help'): static
    {
        return $this->state(['page' => null, 'url' => $url, 'label' => ['ar' => 'رابط خارجي', 'en' => 'External']]);
    }

    public function group(string $arabic = 'عن الحملة', string $english = 'About'): static
    {
        return $this->state(['page' => null, 'url' => null, 'label' => ['ar' => $arabic, 'en' => $english]]);
    }

    public function hidden(): static
    {
        return $this->state(['is_visible' => false]);
    }
}
