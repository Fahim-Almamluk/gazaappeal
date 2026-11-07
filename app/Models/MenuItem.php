<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\MenuItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['parent_id', 'page', 'url', 'label', 'sort_order', 'is_visible'])]
final class MenuItem extends Model
{
    use HasFactory;
    use HasTranslations;

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected array $translatable = ['label'];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->ordered();
    }

    public function type(): string
    {
        return match (true) {
            $this->page !== null => 'page',
            $this->url !== null => 'url',
            default => 'group',
        };
    }

    public function title(): string
    {
        return filled($this->label) ? $this->label : $this->pageLabel();
    }

    public function pageLabel(): string
    {
        $key = config('navigation.pages')[$this->page]['label'] ?? null;

        return $key === null ? '' : __($key);
    }

    protected static function newFactory(): MenuItemFactory
    {
        return MenuItemFactory::new();
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    #[Scope]
    protected function roots(Builder $query): void
    {
        $query->whereNull('parent_id');
    }
}
