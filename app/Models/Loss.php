<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LossIcon;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\Sortable;
use Database\Factories\LossFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'figure', 'unit', 'description', 'icon', 'is_published', 'sort_order'])]
final class Loss extends Model
{
    use HasFactory;
    use HasTranslations;
    use Publishable;
    use SoftDeletes;
    use Sortable;

    /**
     * @var list<string>
     */
    protected array $translatable = ['title', 'unit', 'description'];

    protected function casts(): array
    {
        return [
            'figure' => 'decimal:2',
            'icon' => LossIcon::class,
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function formattedFigure(): string
    {
        $figure = (float) $this->figure;

        return number_format($figure, fmod($figure, 1.0) === 0.0 ? 0 : 1);
    }

    protected static function newFactory(): LossFactory
    {
        return LossFactory::new();
    }
}
