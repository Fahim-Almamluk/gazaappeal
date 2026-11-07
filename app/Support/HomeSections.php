<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Setting;
use Illuminate\Contracts\Cache\Repository as Cache;

final class HomeSections
{
    private const string CACHE_KEY = 'home-sections';

    private const string SETTING = 'home_sections';

    public function __construct(private readonly Cache $cache) {}

    /**
     * @return list<array{key: string, icon: string, visible: bool}>
     */
    public function all(): array
    {
        $stored = $this->stored();
        $known = config('home.sections');
        $order = array_values(array_unique([...array_intersect($stored['order'], array_keys($known)), ...array_keys($known)]));

        return array_map(fn (string $key): array => [
            'key' => $key,
            'icon' => $known[$key]['icon'],
            'visible' => ! in_array($key, $stored['hidden'], true),
        ], $order);
    }

    /**
     * @return list<string>
     */
    public function visible(): array
    {
        return collect($this->all())->where('visible', true)->pluck('key')->values()->all();
    }

    /**
     * @return list<string>
     */
    public function keys(): array
    {
        return array_keys(config('home.sections'));
    }

    /**
     * @param  list<array{id: string, visible: bool}>  $items
     */
    public function update(array $items): void
    {
        $payload = [
            'order' => array_column($items, 'id'),
            'hidden' => array_column(array_filter($items, fn (array $item): bool => ! $item['visible']), 'id'),
        ];

        Setting::query()->updateOrCreate(['key' => self::SETTING], ['value' => json_encode($payload)]);
        $this->flush();
    }

    public function reset(): void
    {
        Setting::query()->where('key', self::SETTING)->delete();
        $this->flush();
    }

    private function flush(): void
    {
        $this->cache->forget(self::CACHE_KEY);
    }

    /**
     * @return array{order: list<string>, hidden: list<string>}
     */
    private function stored(): array
    {
        $raw = $this->cache->rememberForever(
            self::CACHE_KEY,
            fn (): string => (string) Setting::query()->whereKey(self::SETTING)->value('value'),
        );

        $decoded = $raw === '' ? [] : json_decode($raw, true);

        return [
            'order' => array_values(array_filter($decoded['order'] ?? [], 'is_string')),
            'hidden' => array_values(array_filter($decoded['hidden'] ?? [], 'is_string')),
        ];
    }
}
