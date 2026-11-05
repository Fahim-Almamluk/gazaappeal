<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Setting;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Str;

final class SiteSettings
{
    private const string CACHE_KEY = 'site-settings';

    public function __construct(private readonly Cache $cache) {}

    public function all(): array
    {
        return array_replace($this->defaults(), array_intersect_key($this->stored(), $this->defaults()));
    }

    public function get(string $key): ?string
    {
        return $this->all()[$key] ?? null;
    }

    public function localized(string $key): ?string
    {
        $locale = app()->getLocale();

        if ($locale !== config('app.fallback_locale') && filled($this->get("{$key}_{$locale}"))) {
            return $this->get("{$key}_{$locale}");
        }

        return $this->get($key);
    }

    public function socials(): array
    {
        return collect($this->all())
            ->filter(fn (?string $value, string $key): bool => Str::startsWith($key, 'social_') && filled($value))
            ->mapWithKeys(fn (string $value, string $key): array => [Str::after($key, 'social_') => $value])
            ->all();
    }

    public function update(array $values): void
    {
        foreach (array_intersect_key($values, $this->defaults()) as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->cache->forget(self::CACHE_KEY);
    }

    private function defaults(): array
    {
        return config('appeal.settings');
    }

    private function stored(): array
    {
        return $this->cache->rememberForever(
            self::CACHE_KEY,
            fn (): array => Setting::query()->pluck('value', 'key')->all(),
        );
    }
}
