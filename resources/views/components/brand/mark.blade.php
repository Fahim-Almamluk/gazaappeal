@props([
    'size' => 40,
    'variant' => 'color',
])

<img {{ $attributes->class(['shrink-0', 'brightness-0 invert' => $variant === 'on-brand']) }} src="{{ asset('images/brand/mark.png') }}" width="{{ $size }}" height="{{ $size }}" alt="{{ __('ui.brand.name') }}" decoding="async">
