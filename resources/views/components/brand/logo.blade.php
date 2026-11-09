@inject('settings', 'App\Support\SiteSettings')

@php
    $locale = app()->getLocale();
    $source = $settings->imageUrl("logo_{$locale}") ?? asset("images/brand/logo-{$locale}.webp");
@endphp

<img {{ $attributes->class('w-auto shrink-0') }} src="{{ $source }}" alt="{{ __('ui.brand.name') }}" decoding="async">
