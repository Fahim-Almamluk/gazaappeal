@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'parent' => null,
    'parentUrl' => null,
])

<header {{ $attributes->class('relative overflow-hidden rounded-card border border-line bg-surface shadow-card') }}>
    <div class="pointer-events-none absolute inset-y-0 start-0 w-2/3 bg-linear-to-l from-primary-soft/70 to-transparent ltr:bg-linear-to-r dark:from-primary-soft/40" aria-hidden="true"></div>

    <div class="relative space-y-3 px-5 py-4 sm:px-6 sm:py-5">
        @if ($parent)
            <nav aria-label="breadcrumb" class="flex items-center gap-1.5 text-[13px] text-ink-subtle">
                <a href="{{ $parentUrl }}" class="font-medium text-ink-muted transition hover:text-primary">{{ $parent }}</a>
                <x-ui.icon name="chevron-left" :size="13" />
                <span class="font-semibold text-ink">{{ $title }}</span>
            </nav>
        @endif

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 flex-1 items-center gap-3.5">
                @if ($icon)
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-surface text-primary shadow-sm ring-1 ring-primary/20">
                        <x-ui.icon :name="$icon" :size="22" />
                    </span>
                @endif
                <div class="min-w-0 max-w-3xl">
                    <h1 class="text-xl leading-7 font-bold tracking-tight text-ink sm:text-2xl sm:leading-8">{{ $title }}</h1>
                    @if ($subtitle)
                        <p class="mt-0.5 text-sm text-ink-muted">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            @isset($actions)
                <div class="flex shrink-0 flex-wrap items-center gap-2 sm:flex-nowrap">{{ $actions }}</div>
            @endisset
        </div>
    </div>
</header>
