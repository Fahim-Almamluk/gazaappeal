@props(['hint' => null])

<div class="sticky bottom-4 z-10 flex flex-col-reverse items-stretch justify-between gap-3 rounded-2xl border border-line bg-surface px-5 py-3.5 shadow-overlay sm:flex-row sm:items-center">
    <p class="flex items-center gap-2 text-sm text-ink-muted">
        @if ($hint)
            <x-ui.icon name="info" :size="15" class="shrink-0 text-ink-subtle" />{{ $hint }}
        @endif
    </p>
    <div class="flex flex-wrap items-center gap-2.5">{{ $slot }}</div>
</div>
