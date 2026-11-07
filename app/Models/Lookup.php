<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LookupType;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Sortable;
use Database\Factories\LookupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

#[Fillable(['name', 'icon', 'tone', 'sort_order', 'is_active'])]
final class Lookup extends Model
{
    use HasFactory;
    use HasTranslations;
    use Sortable;

    public const array TONES = ['neutral', 'info', 'success', 'warning', 'danger', 'brand'];

    public const array ICONS = [
        'clock', 'zap', 'circle-check', 'ban', 'flag', 'target', 'sparkles', 'clipboard-check', 'calendar-days', 'chart-column',
        'utility-pole', 'cable', 'factory', 'building-2', 'house', 'school', 'tent', 'truck', 'wallet', 'banknote',
        'hand-coins', 'landmark', 'solar-panel', 'battery-charging', 'lightbulb', 'wrench', 'droplets', 'heart-pulse',
        'heart-handshake', 'users', 'user-round', 'award', 'globe', 'map-pin', 'shield-check', 'zap-off',
    ];

    /**
     * @var list<string>
     */
    protected array $translatable = ['name'];

    protected function casts(): array
    {
        return [
            'type' => LookupType::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'status_id')->withTrashed();
    }

    public function damages(): HasMany
    {
        return $this->hasMany(Damage::class, 'category_id');
    }

    public function volunteers(): HasMany
    {
        return $this->hasMany(Volunteer::class, 'expertise_id');
    }

    public function usages(): HasMany
    {
        return $this->{$this->type->usage()}();
    }

    public function badgeTone(): string
    {
        return $this->tone ?? 'neutral';
    }

    public function displayIcon(): string
    {
        return $this->icon ?? $this->type->icon();
    }

    /**
     * @return array<int, string>
     */
    public static function options(LookupType $type, bool $activeOnly = false, ?int $keep = null): array
    {
        return self::query()
            ->ofType($type)
            ->when($activeOnly, fn (Builder $query) => $query->where(fn (Builder $inner) => $inner->where('is_active', true)->when($keep, fn (Builder $kept) => $kept->orWhereKey($keep))))
            ->ordered()
            ->get()
            ->mapWithKeys(fn (self $lookup): array => [$lookup->id => $lookup->name])
            ->all();
    }

    public static function rule(LookupType $type, ?int $keep = null): Exists
    {
        return Rule::exists(self::class, 'id')
            ->where('type', $type->value)
            ->where(fn ($query) => $query->where('is_active', true)->when($keep, fn ($kept) => $kept->orWhere('id', $keep)));
    }

    #[Scope]
    protected function ofType(Builder $query, LookupType $type): void
    {
        $query->where('type', $type);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    protected static function newFactory(): LookupFactory
    {
        return LookupFactory::new();
    }
}
