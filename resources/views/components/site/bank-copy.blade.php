@props(['method'])

<div {{ $attributes->class('flex flex-wrap items-center justify-center gap-2') }}>
    @foreach (['iban', 'account_number'] as $field)
        @if ($method->bankDetail($field))
            <span x-data="copyText" data-text="{{ $method->bankDetail($field) }}" data-copy="{{ __('site.giving.copy.'.$field) }}" data-copied="{{ __('site.donate.copied') }}">
                <button type="button" x-on:click="copy" class="inline-flex h-10 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-semibold text-ink transition hover:border-primary hover:text-primary">
                    <span x-show="idle"><x-ui.icon name="copy" :size="16" /></span>
                    <span x-show="copied" x-cloak class="text-success"><x-ui.icon name="check" :size="16" /></span>
                    <span x-text="label">{{ __('site.giving.copy.'.$field) }}</span>
                </button>
            </span>
        @endif
    @endforeach
    {{ $slot }}
</div>
