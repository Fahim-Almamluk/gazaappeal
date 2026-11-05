@props([
    'name',
    'label',
    'value' => [],
    'names' => null,
    'hint' => null,
    'multiline' => false,
    'required' => false,
    'maxlength' => null,
    'rows' => 5,
])

@php
    $locales = array_keys(config('appeal.locales'));
    $source = config('app.fallback_locale');
    $dotted = fn (string $field): string => str_replace(['[', ']'], ['.', ''], $field);
    $fieldName = fn (string $locale): string => $names[$locale] ?? "{$name}[{$locale}]";
    $fieldId = fn (string $locale): string => str_replace(['[', ']'], ['-', ''], $fieldName($locale));
    $failing = collect($locales)->first(fn (string $locale): bool => $errors->has($dotted($fieldName($locale))));
    $missing = collect($locales)->reject(fn (string $locale): bool => $locale === $source || filled(old($dotted($fieldName($locale)), $value[$locale] ?? null)))->values();
@endphp

<div {{ $attributes->class('space-y-1.5') }} x-data="localeTabs" x-on:reveal-field="reveal" data-open="{{ $failing ?? $source }}">
    <div class="flex items-center justify-between gap-3">
        <span class="block text-[13px] font-semibold text-ink">{{ $label }}@if ($required)<span class="ms-1 text-danger" aria-hidden="true">*</span>@endif</span>

        <div class="inline-flex items-center gap-0.5 rounded-full border border-line bg-surface-2 p-0.5" role="tablist" aria-label="{{ __('ui.form.translations') }}">
            @foreach ($locales as $locale)
                <button
                    type="button"
                    role="tab"
                    data-locale="{{ $locale }}"
                    x-on:click="select"
                    aria-selected="{{ $locale === ($failing ?? $source) ? 'true' : 'false' }}"
                    aria-controls="{{ $fieldId($locale) }}"
                    class="relative inline-flex h-7 min-w-9 items-center justify-center rounded-full px-2.5 text-xs font-bold text-ink-muted transition hover:text-ink aria-selected:bg-surface aria-selected:text-primary aria-selected:shadow-sm"
                >
                    {{ config("appeal.locales.{$locale}.short") }}
                    @if ($missing->contains($locale))
                        <span class="absolute -top-0.5 -end-0.5 size-2 rounded-full bg-warning ring-2 ring-surface-2" title="{{ __('ui.form.missing_translation') }}"></span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    @foreach ($locales as $locale)
        @php
            $field = $fieldName($locale);
            $direction = config("appeal.locales.{$locale}.dir");
            $isRequired = $required && $locale === $source;
        @endphp

        <div data-locale-panel="{{ $locale }}" @if ($locale !== ($failing ?? $source)) hidden @endif>
            @if ($multiline)
                <x-ui.textarea
                    :name="$field"
                    :id="$fieldId($locale)"
                    :hint="$locale === $source ? $hint : null"
                    :value="old($dotted($field), $value[$locale] ?? null)"
                    :rows="$rows"
                    :maxlength="$maxlength"
                    :dir="$direction"
                    :lang="$locale"
                    :required="$isRequired"
                />
            @else
                <x-ui.input
                    :name="$field"
                    :id="$fieldId($locale)"
                    :hint="$locale === $source ? $hint : null"
                    :value="old($dotted($field), $value[$locale] ?? null)"
                    :maxlength="$maxlength"
                    :dir="$direction"
                    :lang="$locale"
                    :required="$isRequired"
                />
            @endif
        </div>
    @endforeach
</div>
