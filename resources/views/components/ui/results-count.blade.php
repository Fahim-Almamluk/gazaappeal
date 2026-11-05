@props(['paginator' => null, 'total' => null])

@php($count = $paginator?->total() ?? $total ?? 0)

<div class="flex flex-wrap items-center justify-between gap-2 border-b border-line bg-surface-2/50 px-6 py-2.5 text-sm">
    <p class="flex items-center gap-2 text-ink-muted">
        <x-ui.icon name="list" :size="15" class="text-ink-subtle" />
        {{ __('ui.results.total') }}
        <span class="tabular rounded-full bg-primary-soft px-2.5 py-0.5 text-xs font-bold text-primary-soft-ink">{{ number_format($count) }}</span>
    </p>
    @if ($paginator !== null && $count > 0)
        <p class="tabular text-ink-subtle">{{ __('ui.results.range', ['from' => $paginator->firstItem(), 'to' => $paginator->lastItem()]) }}</p>
    @endif
</div>
