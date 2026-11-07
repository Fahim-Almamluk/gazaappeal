@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'parent' => null,
    'parentUrl' => null,
])

<header {{ $attributes->class('rounded-panel bg-linear-to-l from-brand-700 via-brand-600 to-brand-500 px-5 py-4 text-white sm:px-6 sm:py-5') }}>
    <div class="space-y-3">
        @if ($parent)
            <nav aria-label="breadcrumb" class="flex items-center gap-1.5 text-[13px] text-white/65">
                <a href="{{ $parentUrl }}" class="font-medium transition hover:text-white">{{ $parent }}</a>
                <x-ui.icon name="chevron-left" :size="13" class="text-white/45" />
                <span class="font-semibold text-white">{{ $title }}</span>
            </nav>
        @endif

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 flex-1 items-center gap-3.5">
                @if ($icon)
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white ring-1 ring-white/25">
                        <x-ui.icon :name="$icon" :size="22" />
                    </span>
                @endif
                <div class="min-w-0 max-w-3xl">
                    <h1 class="text-xl leading-7 font-bold tracking-tight sm:text-2xl sm:leading-8">{{ $title }}</h1>
                    @if ($subtitle)
                        <p class="mt-0.5 text-sm text-white/75">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            @isset($actions)
                <div @class([
                    'flex shrink-0 flex-wrap items-center gap-2 sm:flex-nowrap',
                    '[&_[data-variant=primary]]:bg-white [&_[data-variant=primary]]:text-brand-700 [&_[data-variant=primary]]:shadow-none [&_[data-variant=primary]:hover]:bg-white/90',
                    '[&_[data-variant=gradient]]:bg-none [&_[data-variant=gradient]]:bg-white [&_[data-variant=gradient]]:text-brand-700 [&_[data-variant=gradient]]:shadow-none',
                    '[&_[data-variant=secondary]]:border-white/30 [&_[data-variant=secondary]]:bg-white/10 [&_[data-variant=secondary]]:text-white [&_[data-variant=secondary]:hover]:bg-white/20',
                    '[&_[data-variant=soft]]:bg-white/15 [&_[data-variant=soft]]:text-white [&_[data-variant=soft]:hover]:bg-white/25',
                    '[&_[data-variant=ghost]]:text-white/80 [&_[data-variant=ghost]:hover]:bg-white/15 [&_[data-variant=ghost]:hover]:text-white',
                    '[&_[data-variant=danger-soft]]:bg-white [&_[data-variant=danger-soft]]:text-danger',
                ])>{{ $actions }}</div>
            @endisset
        </div>
    </div>
</header>
