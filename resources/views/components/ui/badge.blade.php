@props([
    'tone' => 'neutral',
    'icon' => null,
])

@php
    $tones = [
        'success' => 'bg-success-soft text-success',
        'warning' => 'bg-warning-soft text-warning',
        'danger' => 'bg-danger-soft text-danger',
        'info' => 'bg-info-soft text-info',
        'neutral' => 'bg-neutral-soft text-neutral',
        'brand' => 'bg-primary-soft text-primary-soft-ink',
    ];
@endphp

<span {{ $attributes->class('inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap '.$tones[$tone]) }}>
    @if ($icon)<x-ui.icon :name="$icon" :size="13" />@endif
    {{ $slot }}
</span>
