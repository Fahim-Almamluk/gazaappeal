@props([
    'name' => 'image',
    'label',
    'current' => null,
    'hint' => null,
    'required' => false,
])

@php
    $invalid = $errors->has($name);
@endphp

<x-ui.field :name="$name" :label="$label" :hint="$hint ?? __('ui.upload.hint', ['size' => config('appeal.uploads.max_kilobytes') / 1024])" :required="$required" {{ $attributes->class('sm:col-span-2') }}>
    <div x-data="imagePreview" data-current="{{ $current }}" class="flex flex-col gap-4 sm:flex-row sm:items-center">
        <div class="flex aspect-video w-full shrink-0 items-center justify-center overflow-hidden rounded-xl border border-dashed border-line-strong bg-surface-2 sm:w-56">
            <img x-ref="image" x-show="hasImage" @if ($current) src="{{ $current }}" @else x-cloak @endif alt="" class="size-full object-cover">
            <span x-show="empty" @if ($current) x-cloak @endif class="flex flex-col items-center gap-1.5 text-ink-subtle">
                <x-ui.icon name="image" :size="26" />
                <span class="text-xs">{{ __('ui.upload.empty') }}</span>
            </span>
        </div>

        <label class="inline-flex h-10 cursor-pointer items-center justify-center gap-2 self-start rounded-xl border border-line bg-surface px-4 text-sm font-semibold text-ink transition hover:border-line-strong hover:bg-surface-hover focus-within:ring-4 focus-within:ring-primary/12">
            <x-ui.icon name="image" :size="18" />
            {{ $current ? __('ui.upload.replace') : __('ui.upload.choose') }}
            <input
                id="{{ $name }}"
                type="file"
                name="{{ $name }}"
                accept="{{ collect(config('appeal.uploads.mimes'))->map(fn ($ext) => '.'.$ext)->implode(',') }}"
                x-on:change="pick"
                class="sr-only"
                @if ($required && ! $current) required data-req="1" @endif
                @if ($invalid) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            >
        </label>
    </div>
</x-ui.field>
