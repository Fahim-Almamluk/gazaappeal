@props([
    'tone' => 'info',
    'title' => null,
])

@php
    $styles = [
        'success' => ['bg-success-soft text-success', 'circle-check'],
        'warning' => ['bg-warning-soft text-warning', 'triangle-alert'],
        'danger' => ['bg-danger-soft text-danger', 'circle-alert'],
        'info' => ['bg-info-soft text-info', 'info'],
    ];
    [$classes, $icon] = $styles[$tone];
@endphp

<div {{ $attributes->class('flex gap-3 rounded-xl px-4 py-3 text-sm '.$classes) }} role="{{ $tone === 'danger' ? 'alert' : 'status' }}">
    <x-ui.icon :name="$icon" class="mt-0.5" />
    <div class="space-y-0.5">
        @if ($title)<p class="font-semibold">{{ $title }}</p>@endif
        <div class="text-ink-muted dark:text-ink">{{ $slot }}</div>
    </div>
</div>
