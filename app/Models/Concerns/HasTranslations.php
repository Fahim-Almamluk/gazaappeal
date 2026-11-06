<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait HasTranslations
{
    public function getAttributeValue($key): mixed
    {
        if (! $this->isTranslatable($key)) {
            return parent::getAttributeValue($key);
        }

        return $this->translation($key, app()->getLocale());
    }

    public function setAttribute($key, $value): mixed
    {
        if (! $this->isTranslatable($key) || $value === null) {
            return parent::setAttribute($key, $value);
        }

        $translations = is_array($value) ? $value : [...$this->translations($key), app()->getLocale() => $value];
        $translations = array_filter($translations, fn (mixed $text): bool => filled($text));

        return parent::setAttribute($key, $translations === [] ? null : json_encode($translations, JSON_UNESCAPED_UNICODE));
    }

    public function translation(string $key, string $locale): ?string
    {
        $translations = $this->translations($key);

        return $translations[$locale] ?? $translations[config('app.fallback_locale')] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public function translations(string $key): array
    {
        $raw = $this->getAttributes()[$key] ?? null;

        if ($raw === null) {
            return [];
        }

        $decoded = json_decode((string) $raw, true);

        return is_array($decoded) ? $decoded : [config('app.fallback_locale') => (string) $raw];
    }

    public function isTranslatable(string $key): bool
    {
        return in_array($key, $this->translatable, true);
    }

    #[Scope]
    protected function whereTranslation(Builder $query, string $key, string $value, ?string $locale = null): void
    {
        $query->where($key.'->'.($locale ?? config('app.fallback_locale')), $value);
    }
}
