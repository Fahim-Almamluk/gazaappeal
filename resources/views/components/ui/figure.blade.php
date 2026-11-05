@props([
    'label',
    'tone' => 'neutral',
    'hint' => null,
])

@php
    $tones = ['neutral' => 'text-ink', 'danger' => 'text-danger', 'success' => 'text-success', 'brand' => 'text-primary'];
@endphp

<div {{ $attributes->class('rounded-card border border-line bg-surface px-5 py-4 shadow-elevated') }}>
    <p class="text-xs font-semibold text-ink-subtle">{{ $label }}</p>
    <p class="tabular mt-1.5 text-2xl leading-8 font-extrabold {{ $tones[$tone] }}">{{ $slot }}</p>
    @if ($hint)
        <p class="mt-0.5 text-xs leading-5 text-ink-muted">{{ $hint }}</p>
    @endif
</div>
