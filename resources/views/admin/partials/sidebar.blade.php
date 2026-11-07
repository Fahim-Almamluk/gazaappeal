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
    class="fixed inset-y-0 start-0 z-50 flex w-[280px] translate-x-full flex-col ltr:-translate-x-full bg-surface shadow-[0_0_48px_-24px_rgb(20_30_100/0.35)] transition-transform duration-200 ease-out-soft group-data-[mobile-open=true]/shell:translate-x-0 ltr:group-data-[mobile-open=true]/shell:translate-x-0 lg:sticky lg:top-0 lg:z-auto lg:h-dvh lg:w-auto lg:translate-x-0 lg:shadow-[1px_0_32px_-20px_rgb(20_30_100/0.3)] ltr:lg:translate-x-0 dark:shadow-none dark:lg:shadow-none"
    x-on:keydown.escape.window="closeMobile"
    aria-label="{{ __('ui.common.menu') }}"
>
    <div class="relative flex h-16 shrink-0 items-center justify-center px-6 lg:group-data-[collapsed=true]/shell:px-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center rounded-lg">
            <x-brand.logo class="h-10 lg:group-data-[collapsed=true]/shell:hidden" />
            <x-brand.mark :size="36" class="hidden lg:group-data-[collapsed=true]/shell:block" />
        </a>
        <x-ui.button variant="ghost" size="icon-sm" class="absolute end-3 top-1/2 -translate-y-1/2 lg:hidden" x-on:click="closeMobile" aria-label="{{ __('ui.common.close') }}">
            <x-ui.icon name="x" />
        </x-ui.button>
    </div>

    <nav class="flex-1 space-y-3 overflow-x-hidden overflow-y-auto px-4 pt-2 pb-6 lg:group-data-[collapsed=true]/shell:px-3">
        @foreach ($groups as $group)
            @php
                $collapsible = $group['collapsible'] ?? true;
                $panel = 'nav-group-'.$group['key'];
            @endphp

            <div @if ($collapsible) x-data="navGroup" data-open="true" @endif>
                @if ($collapsible)
                    <button
                        type="button"
                        x-on:click="toggle"
                        x-bind:aria-expanded="open"
                        aria-expanded="true"
                        aria-controls="{{ $panel }}"
                        aria-label="{{ __('ui.nav.toggle_group', ['name' => __($group['label'])]) }}"
                        class="group/head flex h-9 w-full items-center gap-2 rounded-lg px-3 text-start text-[11px] font-bold tracking-wide text-ink-subtle transition hover:text-ink lg:group-data-[collapsed=true]/shell:hidden"
                    >
                        <span class="min-w-0 flex-1 truncate">{{ __($group['label']) }}</span>
                        @if ($group['current'])
                            <span class="size-1.5 shrink-0 rounded-full bg-primary" aria-hidden="true"></span>
                        @endif
                        <x-ui.icon name="chevron-down" :size="14" class="shrink-0 transition-transform duration-300 ease-out-soft group-aria-expanded/head:rotate-180" />
                    </button>
                @else
                    <p class="mb-1 truncate px-3 py-2 text-[11px] font-bold tracking-wide text-ink-subtle {{ $hideWhenCollapsed }}">{{ __($group['label']) }}</p>
                @endif

                <div
                    id="{{ $panel }}"
                    @if ($collapsible) x-bind:data-open="shown" x-bind:inert="closed" @endif
                    @if ($collapsible) data-open="true" @endif
                    @class([
                        'grid grid-rows-[0fr] transition-[grid-template-rows] duration-300 ease-out-soft data-[open=true]:grid-rows-[1fr] lg:group-data-[collapsed=true]/shell:grid-rows-[1fr]' => $collapsible,
                    ])
                >
                    <div class="min-h-0 overflow-hidden">
                        <div class="space-y-1 pt-1 pb-3">
                            @foreach ($group['items'] as $item)
                                <a
                                    href="{{ route($item['route']) }}"
                                    title="{{ __($item['label']) }}"
                                    @if ($item['active']) aria-current="page" @endif
                                    @class([
                                        'group relative flex h-11 items-center gap-3 overflow-hidden rounded-xl px-3 text-sm transition duration-200 ease-out-soft lg:group-data-[collapsed=true]/shell:justify-center',
                                        'bg-linear-to-l from-brand-500 to-brand-700 font-semibold text-white shadow-[inset_0_1px_0_rgb(255_255_255/0.25),0_10px_22px_-12px_rgb(21_104_201/0.8)] dark:shadow-[inset_0_1px_0_rgb(255_255_255/0.2)]' => $item['active'],
                                        'font-medium text-ink-muted hover:bg-surface-hover hover:text-ink' => ! $item['active'],
                                    ])
                                >
                                    <span @class(['flex shrink-0 transition duration-200 ease-out-soft', 'group-hover:-translate-x-0.5 group-hover:text-primary ltr:group-hover:translate-x-0.5' => ! $item['active']])>
                                        <x-ui.icon :name="$item['icon']" :size="19" />
                                    </span>
                                    <span class="truncate {{ $hideWhenCollapsed }}">{{ __($item['label']) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </nav>
</aside>
