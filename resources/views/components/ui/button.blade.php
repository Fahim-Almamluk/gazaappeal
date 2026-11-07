@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'icon' => null,
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'bg-primary text-primary-fg shadow-brand hover:bg-primary-hover dark:shadow-none',
        'gradient' => 'bg-linear-to-l from-brand-400 to-brand-600 text-white shadow-brand hover:from-brand-500 hover:to-brand-700 dark:shadow-none',
        'secondary' => 'border border-line bg-surface text-ink hover:border-line-strong hover:bg-surface-hover',
        'soft' => 'bg-primary-soft text-primary-soft-ink hover:brightness-[.97] dark:hover:brightness-125',
        'ghost' => 'text-ink-muted hover:bg-surface-hover hover:text-ink',
        'danger' => 'bg-danger-solid text-white shadow-sm hover:brightness-110',
        'excel' => 'bg-excel text-excel-fg shadow-[0_8px_18px_-10px_var(--excel)] hover:bg-excel-hover',
        'danger-soft' => 'bg-danger-soft text-danger hover:brightness-[.97] dark:hover:brightness-125',
        'cancel' => 'border border-line-strong bg-neutral-soft text-ink hover:border-ink-subtle hover:bg-surface-hover',
        'reset' => 'border border-warning/35 bg-warning-soft text-warning hover:border-warning hover:brightness-95 dark:hover:brightness-125',
        'back' => 'border border-danger-solid bg-danger-solid text-white shadow-[0_8px_18px_-10px_var(--danger-solid)] hover:brightness-110',
    ];

    $sizes = [
        'sm' => 'h-8 gap-1.5 rounded-lg px-3 text-[13px]',
        'md' => 'h-10 gap-2 rounded-xl px-4 text-sm',
        'lg' => 'h-12 gap-2 rounded-xl px-5 text-[15px]',
        'xl' => 'h-[52px] gap-2 rounded-2xl px-6 text-base',
        'icon' => 'size-10 rounded-xl',
        'icon-sm' => 'size-8 rounded-lg',
    ];

    $classes = 'inline-flex shrink-0 items-center justify-center font-semibold whitespace-nowrap transition duration-150 ease-out-soft select-none disabled:pointer-events-none disabled:opacity-60 '
        .$variants[$variant].' '.$sizes[$size];
@endphp

@if ($href)
    <a href="{{ $href }}" data-variant="{{ $variant }}" {{ $attributes->class($classes) }}>
        @if ($icon)<x-ui.icon :name="$icon" :size="$size === 'sm' ? 16 : 18" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" data-variant="{{ $variant }}" {{ $attributes->class($classes) }}>
        @if ($icon)<x-ui.icon :name="$icon" :size="$size === 'sm' ? 16 : 18" />@endif
        {{ $slot }}
    </button>
@endif
