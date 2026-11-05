<?php

namespace Database\Factories;

use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

final class GalleryItemFactory extends Factory
{
    protected $model = GalleryItem::class;

    public function definition(): array
    {
        return [
            'caption' => fake()->sentence(4),
            'image' => 'gallery/'.fake()->uuid().'.jpg',
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
