@props(['title' => null, 'index' => false, 'description' => null])

@inject('localization', 'App\Support\Localization')
@inject('settings', 'App\Support\SiteSettings')

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $localization->direction() }}" data-theme="light" data-v-required="{{ __('validation.client.required') }}" data-v-invalid="{{ __('validation.client.invalid') }}" data-v-short="{{ __('validation.client.too_short') }}" data-v-long="{{ __('validation.client.too_long') }}" data-v-range="{{ __('validation.client.out_of_range') }}" data-v-summary="{{ __('validation.client.summary') }}" data-v-tamper="{{ __('validation.client.tamper') }}" data-v-ok="{{ __('validation.client.ok') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#1568C9">
    @if ($index)
        @if ($description)
            <meta name="description" content="{{ $description }}">
            <meta property="og:description" content="{{ $description }}">
        @endif
        <meta property="og:title" content="{{ $title ? $title.' · ' : '' }}{{ __('ui.brand.name') }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ $settings->imageUrl('logo_mark') ?? asset('images/brand/mark.png') }}">
        <link rel="canonical" href="{{ url()->current() }}">
        <meta property="og:locale" content="{{ $localization->ogLocale() }}">
        @foreach ($localization->alternates() as $locale => $url)
            <link rel="alternate" hreflang="{{ $locale }}" href="{{ $url }}">
        @endforeach
    @else
        <meta name="robots" content="noindex, nofollow">
    @endif
    <title>{{ $title ? $title.' · ' : '' }}{{ __('ui.brand.name') }}</title>
    <link rel="icon" href="{{ $settings->imageUrl('logo_mark') ?? asset('images/brand/mark.png') }}">
    <script nonce="{{ Vite::cspNonce() }}">
        (() => {
            try {
                const mode = localStorage.getItem('gaza.theme') ?? 'system';
                const dark = mode === 'dark' || (mode === 'system' && matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.dataset.theme = dark ? 'dark' : 'light';
            } catch {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body {{ $attributes->class('min-h-dvh') }}>
    {{ $slot }}

    <x-ui.flash />
</body>
</html>
