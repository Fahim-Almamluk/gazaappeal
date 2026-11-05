@php
    $items = config('navigation.public');
    $isActive = fn (array $item): bool => request()->routeIs($item['active'] ?? $item['route']);
@endphp

<header class="sticky top-0 z-40 border-b border-line/70 bg-bg/80 backdrop-blur-xl" x-data="dropdown" x-on:keydown.escape.window="close">
    <div class="scroll-progress pointer-events-none absolute inset-x-0 bottom-[-1px] h-0.5 bg-linear-to-l from-brand-400 to-brand-600" aria-hidden="true"></div>
    <div class="mx-auto flex h-[72px] w-full max-w-[1280px] items-center gap-4 px-5 sm:px-8">
        <a href="{{ route('home') }}" class="shrink-0">
            <x-brand.logo class="h-11" />
        </a>

        <nav class="mx-auto hidden items-center gap-1 rounded-full border border-line bg-surface p-1 shadow-sm xl:flex" aria-label="{{ __('ui.common.menu') }}">
            @foreach ($items as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @if ($isActive($item)) aria-current="page" @endif
                    @class([
                        'rounded-full px-3.5 py-2 text-sm font-semibold whitespace-nowrap transition duration-150',
                        'bg-primary text-primary-fg shadow-sm' => $isActive($item),
                        'text-ink-muted hover:bg-surface-hover hover:text-ink' => ! $isActive($item),
                    ])
                >
                    {{ __($item['label']) }}
                </a>
            @endforeach
        </nav>

        <div class="ms-auto flex items-center gap-1.5 xl:ms-0">
            <x-ui.language-switch />
            @include('partials.theme-menu')
            <x-ui.button :href="route('donate')" icon="heart" class="max-sm:hidden">{{ __('site.nav.donate') }}</x-ui.button>
            <x-ui.button variant="ghost" size="icon" class="xl:hidden" x-on:click="toggle" x-bind:aria-expanded="open" aria-label="{{ __('site.nav.menu') }}">
                <x-ui.icon name="menu" />
            </x-ui.button>
        </div>
    </div>

    <div x-show="open" x-cloak x-transition class="border-t border-line bg-bg xl:hidden">
        <nav class="mx-auto flex max-w-[1280px] flex-col gap-1 px-5 py-4 sm:px-8">
            @foreach ($items as $item)
                <a href="{{ route($item['route']) }}" @class(['rounded-xl px-4 py-3 text-[15px] font-semibold', 'bg-primary-soft text-primary-soft-ink' => $isActive($item), 'text-ink-muted hover:bg-surface-hover' => ! $isActive($item)])>
                    {{ __($item['label']) }}
                </a>
            @endforeach
            <x-ui.button :href="route('donate')" icon="heart" class="mt-2">{{ __('site.nav.donate') }}</x-ui.button>
        </nav>
    </div>
</header>
