@props([
    'title',
    'description' => null,
    'step' => null,
    'plain' => false,
])

<section {{ $attributes->class('overflow-hidden rounded-card border border-t-[3px] border-line border-t-primary bg-surface shadow-elevated') }}>
    <header class="flex items-center gap-3 border-b border-line px-6 py-4">
        @if ($step)
            <span class="tabular flex size-7 shrink-0 items-center justify-center rounded-md border border-primary/40 bg-primary-soft text-sm font-bold text-primary-soft-ink">{{ $step }}</span>
        @endif
        <div class="min-w-0">
            <h2 class="text-base leading-6 font-bold text-ink">{{ $title }}</h2>
            @if ($description)
                <p class="text-[13px] leading-5 text-ink-muted">{{ $description }}</p>
            @endif
        </div>
    </header>
    @if ($plain)
        {{ $slot }}
    @else
        <dl class="grid gap-x-8 gap-y-5 p-6 sm:grid-cols-2">{{ $slot }}</dl>
    @endif
</section>
