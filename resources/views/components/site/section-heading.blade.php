@props([
    'title',
    'eyebrow' => null,
    'subtitle' => null,
])

<div {{ $attributes->class('flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between') }}>
    <div class="space-y-2">
        @if ($eyebrow)
            <p class="flex items-center gap-2 text-sm font-bold text-primary">
                <span class="h-px w-6 bg-primary/50"></span>
                {{ $eyebrow }}
            </p>
        @endif
        <h2 class="text-[28px] leading-tight font-extrabold tracking-tight text-ink sm:text-[34px]">{{ $title }}</h2>
        @if ($subtitle)
            <p class="max-w-2xl text-ink-muted">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($action)
        <div class="shrink-0">{{ $action }}</div>
    @endisset
</div>
