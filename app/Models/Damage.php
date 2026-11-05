<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DamageCategory;
use App\Enums\Governorate;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Sortable;
use Database\Factories\DamageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['governorate', 'category', 'item', 'amount', 'sort_order'])]
final class Damage extends Model
{
    use HasFactory;
    use HasTranslations;
    use Sortable;

    /**
     * @var list<string>
     */
    protected array $translatable = ['item'];

    protected function casts(): array
    {
        return [
            'governorate' => Governorate::class,
            'category' => DamageCategory::class,
            'amount' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected static function newFactory(): DamageFactory
    {
        return DamageFactory::new();
    }
}
