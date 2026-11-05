@props([
    'value' => 0,
    'tone' => 'primary',
    'animated' => false,
])

@inject('localization', 'App\Support\Localization')

@php
    $fill = ['primary' => 'fill-primary', 'success' => 'fill-success', 'danger' => 'fill-danger-solid', 'white' => 'fill-white'][$tone];
    $track = $tone === 'white' ? 'fill-white/25' : 'fill-line';
@endphp

<svg {{ $attributes->class('h-2 w-full overflow-hidden rounded-full') }} viewBox="0 0 100 2" preserveAspectRatio="none" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $value }}">
    <rect width="100" height="2" class="{{ $track }}"/>
    <g @if ($localization->direction() === 'rtl') transform="matrix(-1 0 0 1 100 0)" @endif>
        <rect width="{{ max(0, min(100, $value)) }}" height="2" @class([$fill, 'origin-left animate-grow-x [transform-box:fill-box] motion-reduce:animate-none' => $animated])/>
    </g>
</svg>
