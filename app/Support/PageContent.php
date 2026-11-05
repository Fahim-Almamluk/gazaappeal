<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Setting;
use Illuminate\Contracts\Cache\Repository as Cache;

final class PageContent
{
    private const string CACHE_KEY = 'page-content';

    private const string PREFIX = 'page_';

    public function __construct(private readonly Cache $cache) {}

    /**
     * @return array<string, array<string, array<string, array{key: string, label: string, max: int, number: int, multiline: bool}>>>
     */
    public function sections(): array
    {
        return config('pages');
    }

    /**
     * @return array<string, array{key: string, label: string, max: int, number: int, multiline: bool}>
     */
    public function fields(): array
    {
        return collect($this->sections())->flatMap(fn (array $groups): array => array_merge(...array_values($groups)))->all();
    }

    public function text(string $key): string
    {
        $name = $this->nameFor($key);
        $locale = app()->getLocale();
        $stored = $name ? ($this->stored()[$this->column($name, $locale)] ?? null) : null;

        return filled($stored) ? $stored : __("site.{$key}");
    }

    /**
     * @return array<string, string>
     */
    public function translations(string $name): array
    {
        $key = $this->fields()[$name]['key'];

        return collect($this->locales())
            ->mapWithKeys(fn (string $locale): array => [$locale => $this->stored()[$this->column($name, $locale)] ?? __("site.{$key}", [], $locale)])
            ->all();
    }

    /**
     * @param  array{label: string, number: int}  $field
     */
    public function label(array $field): string
    {
        $label = __("admin.pages.labels.{$field['label']}");

        return $field['number'] > 0 ? __('admin.pages.numbered', ['label' => $label, 'number' => $field['number']]) : $label;
    }

    public function column(string $name, string $locale): string
    {
        return $locale === config('app.fallback_locale') ? self::PREFIX.$name : self::PREFIX."{$name}_{$locale}";
    }

    /**
     * @param  array<string, ?string>  $values
     */
    public function update(array $values): void
    {
        foreach ($this->fields() as $name => $field) {
            foreach ($this->locales() as $locale) {
                $column = $this->column($name, $locale);

                if (! array_key_exists($column, $values)) {
                    continue;
                }

                $value = trim((string) $values[$column]);

                if ($value === '' || $value === __("site.{$field['key']}", [], $locale)) {
                    Setting::query()->where('key', $column)->delete();
                } else {
                    Setting::query()->updateOrCreate(['key' => $column], ['value' => $value]);
                }
            }
        }

        $this->cache->forget(self::CACHE_KEY);
    }

    /**
     * @return list<string>
     */
    public function locales(): array
    {
        return array_keys(config('appeal.locales'));
    }

    private function nameFor(string $key): ?string
    {
        return collect($this->fields())->search(fn (array $field): bool => $field['key'] === $key) ?: null;
    }

    /**
     * @return array<string, string>
     */
    private function stored(): array
    {
        return $this->cache->rememberForever(
            self::CACHE_KEY,
            fn (): array => Setting::query()->where('key', 'like', self::PREFIX.'%')->pluck('value', 'key')->all(),
        );
    }
}
