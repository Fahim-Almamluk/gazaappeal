@props([
    'label',
    'value',
    'icon',
    'source' => null,
    'tone' => 'brand',
])

@php
    $iconTones = [
        'brand' => 'bg-primary-soft text-primary-soft-ink',
        'success' => 'bg-success-soft text-success',
        'warning' => 'bg-warning-soft text-warning',
        'danger' => 'bg-danger-soft text-danger',
    ];
@endphp

<x-ui.card {{ $attributes->class('group relative overflow-hidden') }}>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0 space-y-2">
            <p class="text-[13px] font-medium text-ink-muted">{{ $label }}</p>
            <p class="tabular text-[32px] leading-9 font-bold tracking-tight text-ink">{{ $value }}</p>
        </div>
        <span class="flex size-11 items-center justify-center rounded-xl {{ $iconTones[$tone] }}">
            <x-ui.icon :name="$icon" :size="20" />
        </span>
    </div>

    @if ($source)
        <p class="mt-4 flex items-center gap-1.5 border-t border-line pt-3 text-xs text-ink-subtle">
            <x-ui.icon name="clock" :size="13" />
            {{ $source }}
        </p>
    @endif
</x-ui.card>
