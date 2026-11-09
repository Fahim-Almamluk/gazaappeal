@props([
    'icon',
    'label',
    'href' => null,
    'tone' => 'neutral',
    'type' => 'button',
])

@php
    $tones = [
        'neutral' => 'text-ink-muted hover:bg-surface-hover hover:text-ink',
        'primary' => 'text-primary hover:bg-primary-soft',
        'danger' => 'text-danger hover:bg-danger-soft',
        'success' => 'text-success hover:bg-success-soft',
        'warning' => 'text-warning hover:bg-warning-soft',
        'excel' => 'text-excel hover:bg-excel-soft',
    ];

    $classes = 'group/tip relative inline-flex size-9 shrink-0 items-center justify-center rounded-lg transition duration-150 select-none focus-visible:ring-4 focus-visible:ring-primary/20 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 '.$tones[$tone];
@endphp

@if ($href)
    <a href="{{ $href }}" aria-label="{{ $label }}" {{ $attributes->class($classes) }}>
        <x-ui.icon :name="$icon" :size="18" />
        <span role="tooltip" class="pointer-events-none absolute -top-8 left-1/2 z-30 -translate-x-1/2 rounded-md bg-ink px-2 py-1 text-[11px] font-semibold whitespace-nowrap text-bg opacity-0 shadow-overlay transition duration-150 group-hover/tip:opacity-100 group-focus-visible/tip:opacity-100">{{ $label }}</span>
    </a>
@else
    <button type="{{ $type }}" aria-label="{{ $label }}" {{ $attributes->class($classes) }}>
        <x-ui.icon :name="$icon" :size="18" />
        <span role="tooltip" class="pointer-events-none absolute -top-8 left-1/2 z-30 -translate-x-1/2 rounded-md bg-ink px-2 py-1 text-[11px] font-semibold whitespace-nowrap text-bg opacity-0 shadow-overlay transition duration-150 group-hover/tip:opacity-100 group-focus-visible/tip:opacity-100">{{ $label }}</span>
    </button>
@endif
