@inject('menu', 'App\Support\SiteMenu')

@php
    $items = $menu->tree();
    $pill = 'rounded-full px-3.5 py-2 text-sm font-semibold whitespace-nowrap transition duration-150';
@endphp

<header class="sticky top-0 z-40 border-b border-line/70 bg-bg/80 backdrop-blur-xl" x-data="dropdown" x-on:keydown.escape.window="close">
    <div class="scroll-progress pointer-events-none absolute inset-x-0 bottom-[-1px] h-0.5 bg-linear-to-l from-brand-400 to-brand-600" aria-hidden="true"></div>
    <div class="mx-auto flex h-[72px] w-full max-w-[1280px] items-center gap-4 px-5 sm:px-8">
        <a href="{{ route('home') }}" class="shrink-0">
            <x-brand.logo class="h-11" />
        </a>

        <nav class="mx-auto hidden items-center gap-1 rounded-full border border-line bg-surface p-1 shadow-sm xl:flex" aria-label="{{ __('ui.common.menu') }}">
            @foreach ($items as $item)
                @if ($item['children'])
                    <div class="relative" x-data="dropdown" x-on:mouseenter="show" x-on:mouseleave="close" x-on:keydown.escape="close" x-on:click.outside="close">
                        <button
                            type="button"
                            x-on:click="show"
                            x-bind:aria-expanded="open"
                            aria-expanded="false"
                            aria-haspopup="true"
                            @class([
                                $pill,
                                'group/trigger inline-flex items-center gap-1',
                                'bg-primary text-primary-fg shadow-sm' => $item['active'],
                                'text-ink-muted hover:bg-surface-hover hover:text-ink' => ! $item['active'],
                            ])
                        >
                            {{ $item['label'] }}
                            <x-ui.icon name="chevron-down" :size="14" class="transition-transform duration-200 group-aria-expanded/trigger:rotate-180" />
                        </button>

                        <div x-show="open" x-cloak x-transition.opacity.duration.150ms class="absolute start-0 top-full z-50 min-w-56 pt-3">
                            <div class="rounded-2xl border border-line bg-surface p-1.5 shadow-overlay">
                                @if ($item['url'])
                                    <a href="{{ $item['url'] }}" class="block rounded-xl px-3.5 py-2.5 text-sm font-bold text-ink transition hover:bg-surface-hover">{{ $item['label'] }}</a>
                                @endif
                                @foreach ($item['children'] as $child)
                                    <a href="{{ $child['url'] }}" @if ($child['active']) aria-current="page" @endif @class(['block rounded-xl px-3.5 py-2.5 text-sm font-semibold transition', 'bg-primary-soft text-primary-soft-ink' => $child['active'], 'text-ink-muted hover:bg-surface-hover hover:text-ink' => ! $child['active']])>{{ $child['label'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <a
                        href="{{ $item['url'] }}"
                        @if ($item['active']) aria-current="page" @endif
                        @class([
                            $pill,
                            'bg-primary text-primary-fg shadow-sm' => $item['active'],
                            'text-ink-muted hover:bg-surface-hover hover:text-ink' => ! $item['active'],
                        ])
                    >
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="ms-auto flex items-center gap-1.5 xl:ms-0">
            <x-ui.language-switch />
            @include('partials.theme-toggle')
            <x-ui.button :href="route('donate')" icon="heart" class="max-sm:hidden">{{ __('site.nav.donate') }}</x-ui.button>
            <x-ui.button variant="ghost" size="icon" class="xl:hidden" x-on:click="toggle" x-bind:aria-expanded="open" aria-label="{{ __('site.nav.menu') }}">
                <x-ui.icon name="menu" />
            </x-ui.button>
        </div>
    </div>

    <div x-show="open" x-cloak x-transition class="border-t border-line bg-bg xl:hidden">
        <nav class="mx-auto flex max-w-[1280px] flex-col gap-1 px-5 py-4 sm:px-8">
            @foreach ($items as $item)
                @if ($item['children'])
                    <div x-data="dropdown">
                        <button type="button" x-on:click="toggle" x-bind:aria-expanded="open" aria-expanded="false" @class(['group/trigger flex w-full items-center justify-between rounded-xl px-4 py-3 text-start text-[15px] font-semibold', 'bg-primary-soft text-primary-soft-ink' => $item['active'], 'text-ink-muted hover:bg-surface-hover' => ! $item['active']])>
                            {{ $item['label'] }}
                            <x-ui.icon name="chevron-down" :size="16" class="transition-transform duration-200 group-aria-expanded/trigger:rotate-180" />
                        </button>
                        <div x-show="open" x-cloak class="mt-1 flex flex-col gap-1 border-s-2 border-line ms-4 ps-3">
                            @if ($item['url'])
                                <a href="{{ $item['url'] }}" class="rounded-xl px-4 py-2.5 text-[15px] font-bold text-ink hover:bg-surface-hover">{{ $item['label'] }}</a>
                            @endif
                            @foreach ($item['children'] as $child)
                                <a href="{{ $child['url'] }}" @class(['rounded-xl px-4 py-2.5 text-[15px] font-semibold', 'bg-primary-soft text-primary-soft-ink' => $child['active'], 'text-ink-muted hover:bg-surface-hover' => ! $child['active']])>{{ $child['label'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $item['url'] }}" @class(['rounded-xl px-4 py-3 text-[15px] font-semibold', 'bg-primary-soft text-primary-soft-ink' => $item['active'], 'text-ink-muted hover:bg-surface-hover' => ! $item['active']])>
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
            <x-ui.button :href="route('donate')" icon="heart" class="mt-2">{{ __('site.nav.donate') }}</x-ui.button>
        </nav>
    </div>
</header>
