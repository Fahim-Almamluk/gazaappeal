@inject('localization', 'App\Support\Localization')

@php
    $target = $localization->alternate();
@endphp

<a
    href="{{ $localization->urlFor($target) }}"
    hreflang="{{ $target }}"
    title="{{ __('ui.language.label') }}"
    {{ $attributes->class('group inline-flex h-10 shrink-0 items-center gap-2 rounded-full border border-primary/20 bg-primary-soft/70 ps-1 pe-3.5 text-sm font-bold text-primary-soft-ink transition hover:border-primary/40 hover:bg-primary-soft hover:shadow-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary max-sm:pe-1') }}
>
    <span lang="{{ $target }}" aria-hidden="true" class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-extrabold text-primary-fg shadow-sm transition group-hover:scale-105">{{ __('ui.language.switch_short') }}</span>
    <span lang="{{ $target }}" class="max-sm:sr-only">{{ __('ui.language.switch') }}</span>
</a>
