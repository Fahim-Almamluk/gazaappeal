@php
    $locale = app()->getLocale();
@endphp

<img {{ $attributes->class('w-auto shrink-0') }} src="{{ asset('images/brand/logo-'.$locale.'.webp') }}" alt="{{ __('ui.brand.name') }}" decoding="async">
