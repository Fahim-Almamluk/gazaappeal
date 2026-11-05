@props([
    'title',
    'icon' => null,
    'flush' => false,
])

<section {{ $attributes->class('overflow-hidden rounded-card border border-line bg-surface shadow-elevated') }}>
    <header class="flex items-center justify-between gap-3 border-b border-line px-5 py-3.5">
        <h2 class="flex min-w-0 items-center gap-2.5 font-bold text-ink">
            @if ($icon)
                <span class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary-soft-ink"><x-ui.icon :name="$icon" :size="15" /></span>
            @endif
            <span class="truncate">{{ $title }}</span>
        </h2>
        @isset($action){{ $action }}@endisset
    </header>
    <div @class(['space-y-4 p-5' => ! $flush])>{{ $slot }}</div>
</section>
