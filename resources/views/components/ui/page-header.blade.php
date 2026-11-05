@props([
    'title',
    'subtitle' => null,
    'icon' => null,
])

<div {{ $attributes->class('flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between') }}>
    <div class="flex min-w-0 flex-1 items-start gap-4">
        @if ($icon)
            <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-primary-soft text-primary-soft-ink">
                <x-ui.icon :name="$icon" :size="24" />
            </span>
        @endif
        <div class="min-w-0 max-w-3xl space-y-1">
            <h1 class="text-2xl leading-8 font-bold tracking-tight text-ink sm:text-[28px] sm:leading-9">{{ $title }}</h1>
            @if ($subtitle)
                <p class="text-ink-muted">{{ $subtitle }}</p>
            @endif
        </div>
    </div>

    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2 sm:flex-nowrap">{{ $actions }}</div>
    @endisset
</div>
