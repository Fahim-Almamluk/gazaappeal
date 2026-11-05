@inject('localization', 'App\Support\Localization')

@php
    $target = $localization->alternate();
@endphp

<a
    href="{{ $localization->urlFor($target) }}"
    hreflang="{{ $target }}"
    title="{{ __('ui.language.label') }}"
    {{ $attributes->class('inline-flex h-10 shrink-0 items-center gap-1.5 rounded-full border border-line bg-surface px-3 text-sm font-bold text-ink-muted shadow-sm transition hover:border-primary/40 hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary') }}
>
    <x-ui.icon name="languages" :size="16" />
    <span lang="{{ $target }}" class="max-sm:hidden">{{ __('ui.language.switch') }}</span>
    <span lang="{{ $target }}" class="sm:hidden">{{ __('ui.language.switch_short') }}</span>
</a>
