<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasImage;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\Sortable;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'summary', 'body', 'image', 'goal_amount', 'raised_amount', 'status_id', 'is_urgent', 'is_published', 'sort_order'])]
final class Project extends Model
{
    use HasFactory;
    use HasImage;
    use HasTranslations;
    use Publishable;
    use SoftDeletes;
    use Sortable;

    /**
     * @var list<string>
     */
    protected array $translatable = ['title', 'summary', 'body'];

    protected function casts(): array
    {
        return [
            'goal_amount' => 'decimal:2',
            'raised_amount' => 'decimal:2',
            'is_urgent' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_id');
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function progress(): ?int
    {
        if ((float) $this->goal_amount <= 0) {
            return null;
        }

        return (int) min(100, round((float) $this->raised_amount / (float) $this->goal_amount * 100));
    }

    #[Scope]
    protected function urgent(Builder $query): void
    {
        $query->where('is_urgent', true);
    }

    protected static function newFactory(): ProjectFactory
    {
        return ProjectFactory::new();
    }
}
