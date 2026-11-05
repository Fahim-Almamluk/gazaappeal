@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->class('text-center') }}>
    <div class="flex items-center gap-3 sm:gap-5">
        <span class="h-px min-w-6 flex-1 bg-linear-to-r from-primary/45 to-transparent"></span>
        <span class="size-1.5 shrink-0 rounded-full bg-primary"></span>
        <h2 class="min-w-0 text-[21px] leading-tight font-bold text-balance text-ink sm:text-[25px]">{{ $title }}</h2>
        <span class="size-1.5 shrink-0 rounded-full bg-primary"></span>
        <span class="h-px min-w-6 flex-1 bg-linear-to-l from-primary/45 to-transparent"></span>
    </div>

    @if ($subtitle)
        <p class="mx-auto mt-3 max-w-2xl leading-7 text-ink-muted">{{ $subtitle }}</p>
    @endif
</div>
