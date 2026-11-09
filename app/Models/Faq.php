<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\Sortable;
use Database\Factories\FaqFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['question', 'answer', 'is_published', 'sort_order'])]
final class Faq extends Model
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;
    use Publishable;
    use SoftDeletes;
    use Sortable;

    /**
     * @var list<string>
     */
    protected array $translatable = ['question', 'answer'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function newFactory(): FaqFactory
    {
        return FaqFactory::new();
    }
}
