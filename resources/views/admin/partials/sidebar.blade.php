@php
    $hideWhenCollapsed = 'lg:group-data-[collapsed=true]/shell:sr-only';
    $user = auth()->user();
    $groups = collect(config('navigation.admin'))
        ->map(function (array $group) use ($user): array {
            $items = collect($group['items'])
                ->reject(fn (array $item): bool => isset($item['permission']) && ! $user->hasPermission($item['permission']))
                ->map(fn (array $item): array => [...$item, 'active' => request()->routeIs($item['active'] ?? $item['route'])])
                ->values();

            return [...$group, 'items' => $items];
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
    <div class="flex h-16 shrink-0 items-center gap-2 px-6 lg:group-data-[collapsed=true]/shell:justify-center lg:group-data-[collapsed=true]/shell:px-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center rounded-lg">
            <x-brand.logo class="h-10 lg:group-data-[collapsed=true]/shell:hidden" />
            <x-brand.mark :size="36" class="hidden lg:group-data-[collapsed=true]/shell:block" />
        </a>
        <x-ui.button variant="ghost" size="icon-sm" class="ms-auto lg:hidden" x-on:click="closeMobile" aria-label="{{ __('ui.common.close') }}">
            <x-ui.icon name="x" />
        </x-ui.button>
    </div>

    <nav class="flex-1 space-y-6 overflow-x-hidden overflow-y-auto px-4 pt-2 pb-6 lg:group-data-[collapsed=true]/shell:space-y-3 lg:group-data-[collapsed=true]/shell:px-3">
        @foreach ($groups as $group)
            <div>
                <p class="mb-2 truncate px-3 text-[11px] font-bold tracking-wide text-ink-subtle {{ $hideWhenCollapsed }}">{{ __($group['label']) }}</p>

                <div class="space-y-1">
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
        @endforeach
    </nav>
</aside>
