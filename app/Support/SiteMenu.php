<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\MenuItem;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class SiteMenu
{
    private const string CACHE_KEY = 'site-menu';

    public function __construct(private readonly Cache $cache) {}

    /**
     * @return list<array{label: string, url: ?string, active: bool, children: list<array{label: string, url: string, active: bool}>}>
     */
    public function tree(): array
    {
        $rows = collect($this->rows());
        $children = $rows->whereNotNull('parent_id')->groupBy('parent_id');

        return $rows
            ->whereNull('parent_id')
            ->map(fn (array $row): ?array => $this->node($row, $children->get($row['id'], collect())))
            ->filter()
            ->values()
            ->all();
    }

    public function flush(): void
    {
        $this->cache->forget(self::CACHE_KEY);
    }

    public function reset(): void
    {
        DB::transaction(function (): void {
            MenuItem::query()->delete();
            MenuItem::query()->insert(
                collect(config('navigation.defaults'))
                    ->values()
                    ->map(fn (string $page, int $position): array => ['page' => $page, 'sort_order' => $position])
                    ->all(),
            );
        });

        $this->flush();
    }

    /**
     * @param  array{id: int, page: ?string, url: ?string, label: array<string, string>}  $row
     * @param  Collection<int, array{id: int, page: ?string, url: ?string, label: array<string, string>}>  $children
     * @return ?array{label: string, url: ?string, active: bool, children: list<array{label: string, url: string, active: bool}>}
     */
    private function node(array $row, Collection $children): ?array
    {
        $self = $this->leaf($row);

        if ($self === null) {
            return null;
        }

        $items = $children
            ->map(fn (array $child): ?array => $this->leaf($child))
            ->filter(fn (?array $leaf): bool => $leaf !== null && $leaf['url'] !== null)
            ->values();

        if ($self['url'] === null && $items->isEmpty()) {
            return null;
        }

        return [...$self, 'active' => $self['active'] || $items->contains('active', true), 'children' => $items->all()];
    }

    /**
     * @param  array{page: ?string, url: ?string, label: array<string, string>}  $row
     * @return ?array{label: string, url: ?string, active: bool}
     */
    private function leaf(array $row): ?array
    {
        $label = $this->label($row);

        if ($label === '') {
            return null;
        }

        if ($row['page'] === null) {
            return ['label' => $label, 'url' => $row['url'], 'active' => false];
        }

        $page = config('navigation.pages')[$row['page']] ?? null;

        return $page === null ? null : [
            'label' => $label,
            'url' => route($row['page']),
            'active' => request()->routeIs($page['active']),
        ];
    }

    /**
     * @param  array{page: ?string, label: array<string, string>}  $row
     */
    private function label(array $row): string
    {
        $text = $row['label'][app()->getLocale()] ?? $row['label'][config('app.fallback_locale')] ?? null;

        if (filled($text)) {
            return $text;
        }

        $key = $row['page'] === null ? null : (config('navigation.pages')[$row['page']]['label'] ?? null);

        return $key === null ? '' : __($key);
    }

    /**
     * @return list<array{id: int, parent_id: ?int, page: ?string, url: ?string, label: array<string, string>}>
     */
    private function rows(): array
    {
        return $this->cache->rememberForever(self::CACHE_KEY, fn (): array => $this->load());
    }

    /**
     * @return list<array{id: int, parent_id: ?int, page: ?string, url: ?string, label: array<string, string>}>
     */
    private function load(): array
    {
        $items = MenuItem::query()->ordered()->get();

        if ($items->isEmpty()) {
            return collect(config('navigation.defaults'))
                ->values()
                ->map(fn (string $page, int $position): array => ['id' => $position + 1, 'parent_id' => null, 'page' => $page, 'url' => null, 'label' => []])
                ->all();
        }

        $roots = $items->whereNull('parent_id')->where('is_visible', true)->pluck('id');

        return $items
            ->filter(fn (MenuItem $item): bool => $item->is_visible && ($item->parent_id === null || $roots->contains($item->parent_id)))
            ->map(fn (MenuItem $item): array => [
                'id' => $item->id,
                'parent_id' => $item->parent_id,
                'page' => $item->page,
                'url' => $item->url,
                'label' => $item->translations('label'),
            ])
            ->values()
            ->all();
    }
}
