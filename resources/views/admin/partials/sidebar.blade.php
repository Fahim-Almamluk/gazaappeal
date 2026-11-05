@php
    $hideWhenCollapsed = 'lg:group-data-[collapsed=true]/shell:sr-only';
    $user = auth()->user();
    $groups = collect(config('navigation.admin'))
        ->map(function (array $group) use ($user): array {
            $items = collect($group['items'])
                ->reject(fn (array $item): bool => isset($item['permission']) && ! $user->hasPermission($item['permission']))
                ->map(fn (array $item): array => [...$item, 'active' => request()->routeIs($item['active'] ?? $item['route'])])
                ->values();

            return [...$group, 'items' => $items, 'current' => $items->contains('active', true)];
        })
        ->filter(fn (array $group): bool => $group['items']->isNotEmpty());
@endphp

<div
    x-show="mobileOpen"
    x-cloak
    x-transition.opacity
    x-on:click="closeMobile"
    class="fixed inset-0 z-40 bg-black/40 backdrop-blur-[2px] lg:hidden"
></div>

<aside
    class="fixed inset-y-0 start-0 z-50 flex w-[280px] translate-x-full flex-col ltr:-translate-x-full border-e border-line bg-surface transition-transform duration-200 ease-out-soft group-data-[mobile-open=true]/shell:translate-x-0 ltr:group-data-[mobile-open=true]/shell:translate-x-0 lg:sticky lg:top-0 lg:z-auto lg:h-dvh lg:w-auto lg:translate-x-0 ltr:lg:translate-x-0"
    x-on:keydown.escape.window="closeMobile"
    aria-label="{{ __('ui.common.menu') }}"
>
    <div class="flex h-16 shrink-0 items-center gap-2 border-b border-line px-5 lg:group-data-[collapsed=true]/shell:justify-center lg:group-data-[collapsed=true]/shell:px-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center rounded-lg">
            <x-brand.logo class="h-9 lg:group-data-[collapsed=true]/shell:hidden" />
            <x-brand.mark :size="34" class="hidden lg:group-data-[collapsed=true]/shell:block" />
        </a>
        <x-ui.button variant="ghost" size="icon-sm" class="ms-auto lg:hidden" x-on:click="closeMobile" aria-label="{{ __('ui.common.close') }}">
            <x-ui.icon name="x" />
        </x-ui.button>
    </div>

    <nav class="flex-1 space-y-1 overflow-x-hidden overflow-y-auto px-3 py-5">
        @foreach ($groups as $group)
            @php
                $collapsible = $group['collapsible'] ?? true;
                $panel = 'nav-group-'.$group['key'];
            @endphp

            <div
                @if ($collapsible) x-data="navGroup" data-open="{{ $group['current'] ? 'true' : 'false' }}" @endif
                @class(['pb-4' => ! $collapsible, 'border-t border-line/70 pt-1 first:border-t-0 lg:group-data-[collapsed=true]/shell:border-t-0' => $collapsible])
            >
                @if ($collapsible)
                    <button
                        type="button"
                        x-on:click="toggle"
                        x-bind:aria-expanded="open"
                        aria-controls="{{ $panel }}"
                        aria-label="{{ __('ui.nav.toggle_group', ['name' => __($group['label'])]) }}"
                        class="group/head flex h-10 w-full items-center gap-2 rounded-xl px-3 text-start text-[13px] font-bold text-ink-muted transition hover:bg-surface-hover hover:text-ink lg:group-data-[collapsed=true]/shell:hidden"
                    >
                        <span class="min-w-0 flex-1 truncate">{{ __($group['label']) }}</span>
                        @if ($group['current'])
                            <span class="size-1.5 shrink-0 rounded-full bg-primary" aria-hidden="true"></span>
                        @endif
                        <span class="tabular rounded-md bg-surface-2 px-1.5 text-[10.5px] font-semibold text-ink-subtle">{{ $group['items']->count() }}</span>
                        <x-ui.icon name="chevron-down" :size="16" class="shrink-0 text-ink-subtle transition-transform duration-200 group-aria-expanded/head:rotate-180" />
                    </button>
                    <span class="mx-auto my-2 hidden h-px w-6 bg-line lg:group-data-[collapsed=true]/shell:block" aria-hidden="true"></span>
                @else
                    <p class="truncate px-3 pb-1.5 text-[11px] font-semibold tracking-wide text-ink-subtle lg:group-data-[collapsed=true]/shell:hidden">{{ __($group['label']) }}</p>
                @endif

                <div
                    id="{{ $panel }}"
                    @if ($collapsible) x-bind:data-open="shown" x-bind:inert="closed" @endif
                    @class([
                        'grid transition-[grid-template-rows] duration-200 ease-out-soft grid-rows-[0fr] data-[open=true]:grid-rows-[1fr] lg:group-data-[collapsed=true]/shell:grid-rows-[1fr]' => $collapsible,
                    ])
                    @if ($collapsible && ! $group['current']) inert @endif
                    @if ($collapsible && $group['current']) data-open="true" @endif
                >
                    <div @class(['space-y-1', 'min-h-0 overflow-hidden pb-2' => $collapsible])>
                        @foreach ($group['items'] as $item)
                            <a
                                href="{{ route($item['route']) }}"
                                title="{{ __($item['label']) }}"
                                @if ($item['active']) aria-current="page" @endif
                                @class([
                                    'group relative flex h-10 items-center gap-3 rounded-xl px-3 text-sm transition duration-150 lg:group-data-[collapsed=true]/shell:justify-center',
                                    'ps-5 lg:group-data-[collapsed=true]/shell:ps-3' => $collapsible,
                                    'bg-primary-soft font-semibold text-primary-soft-ink' => $item['active'],
                                    'font-medium text-ink-muted hover:bg-surface-hover hover:text-ink' => ! $item['active'],
                                ])
                            >
                                @if ($item['active'])
                                    <span class="absolute inset-y-2 start-0 w-1 rounded-e-full bg-primary"></span>
                                @endif
                                <x-ui.icon :name="$item['icon']" :size="19" />
                                <span class="truncate {{ $hideWhenCollapsed }}">{{ __($item['label']) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </nav>
</aside>
