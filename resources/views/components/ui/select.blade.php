@props([
    'name',
    'options' => [],
    'selected' => null,
    'label' => null,
    'hint' => null,
    'placeholder' => null,
    'bag' => 'default',
    'compact' => false,
    'searchable' => null,
])

@php
    $id = $attributes->get('id', $name);
    $invalid = $errors->getBag($bag)->has($name);
    $current = (string) old($name, $selected);
    $searchable ??= $compact && count($options) > 7;
    $currentText = collect($options)->first(fn (mixed $text, int|string $value): bool => (string) $value === $current, $placeholder);
@endphp

<x-ui.field :required="(bool) $attributes->get('required')" :name="$name" :label="$label" :hint="$hint" :bag="$bag" :for="$id">
    @if ($searchable)
        <div class="relative" x-data="combobox" x-on:click.outside="close" x-on:keydown.escape="close">
            <input type="hidden" id="{{ $id }}" name="{{ $name }}" value="{{ $current }}" data-input>
            <button
                type="button"
                data-trigger
                x-on:click="toggle"
                x-bind:aria-expanded="open"
                aria-haspopup="listbox"
                @if ($attributes->has('aria-label')) aria-label="{{ $attributes->get('aria-label') }}" @endif
                @class([
                    'flex w-full items-center rounded-xl border bg-surface ps-4 pe-10 text-start text-ink shadow-[0_1px_2px_rgb(20_26_64/0.04)] transition duration-150 focus:outline-none dark:bg-surface-2',
                    'border-line hover:border-line-strong focus:border-primary focus:ring-4 focus:ring-primary/12',
                    'h-10 text-sm' => $compact,
                    'h-12 text-[15px]' => ! $compact,
                ])
            >
                <span class="truncate" data-label>{{ $currentText }}</span>
            </button>
            <span class="pointer-events-none absolute inset-y-0 end-0 flex w-10 items-center justify-center text-ink-subtle">
                <x-ui.icon name="chevron-down" :size="16" />
            </span>

            <div x-show="open" x-cloak class="absolute start-0 top-full z-40 mt-1.5 w-full min-w-60 overflow-hidden rounded-xl border border-line bg-surface shadow-overlay">
                <div class="relative border-b border-line p-2">
                    <span class="pointer-events-none absolute inset-y-0 start-2 flex w-9 items-center justify-center text-ink-subtle"><x-ui.icon name="search" :size="15" /></span>
                    <input
                        type="search"
                        data-search
                        autocomplete="off"
                        placeholder="{{ __('ui.filter.search_options') }}"
                        aria-label="{{ __('ui.filter.search_options') }}"
                        x-on:input="filter"
                        x-on:keydown.enter.prevent="first"
                        x-on:keydown.arrow-down.prevent="move"
                        x-on:keydown.arrow-up.prevent="move"
                        class="h-9 w-full rounded-lg border border-line bg-surface ps-9 pe-3 text-sm text-ink placeholder:text-ink-subtle focus:border-primary focus:ring-4 focus:ring-primary/12 focus:outline-none dark:bg-surface-2"
                    >
                </div>
                <ul role="listbox" class="max-h-64 overflow-y-auto p-1.5" x-on:keydown.arrow-down.prevent="move" x-on:keydown.arrow-up.prevent="move">
                    @if ($placeholder !== null)
                        <li><button type="button" role="option" data-option data-value="" data-text="{{ $placeholder }}" aria-selected="{{ $current === '' ? 'true' : 'false' }}" x-on:click="pick" class="flex w-full items-center rounded-lg px-3 py-2 text-start text-sm text-ink-muted transition hover:bg-surface-hover focus:bg-surface-hover focus:outline-none aria-selected:bg-primary-soft aria-selected:font-semibold aria-selected:text-primary-soft-ink">{{ $placeholder }}</button></li>
                    @endif
                    @foreach ($options as $value => $text)
                        <li><button type="button" role="option" data-option data-value="{{ $value }}" data-text="{{ $text }}" aria-selected="{{ $current === (string) $value ? 'true' : 'false' }}" x-on:click="pick" class="flex w-full items-center rounded-lg px-3 py-2 text-start text-sm text-ink-muted transition hover:bg-surface-hover focus:bg-surface-hover focus:outline-none aria-selected:bg-primary-soft aria-selected:font-semibold aria-selected:text-primary-soft-ink">{{ $text }}</button></li>
                    @endforeach
                </ul>
                <p data-none hidden class="px-4 py-5 text-center text-sm text-ink-subtle">{{ __('ui.filter.no_results') }}</p>
            </div>
        </div>
    @else
    <div class="relative">
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            @if ((bool) $attributes->get('required')) data-req="1" @endif
            @if ($invalid) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
            {{ $attributes->except('id')->class([
                'block w-full appearance-none rounded-xl border bg-surface ps-4 pe-10 text-ink shadow-[0_1px_2px_rgb(20_26_64/0.04)] transition duration-150 focus:outline-none dark:bg-surface-2',
                'border-line hover:border-line-strong focus:border-primary focus:ring-4 focus:ring-primary/12' => ! $invalid,
                'border-danger focus:ring-4 focus:ring-danger/12' => $invalid,
                'h-10 text-sm' => $compact,
                'h-12 text-[15px]' => ! $compact,
            ]) }}
        >
            @if ($placeholder !== null)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach ($options as $value => $text)
                <option value="{{ $value }}" @selected($current === (string) $value)>{{ $text }}</option>
            @endforeach
        </select>
        <span class="pointer-events-none absolute inset-y-0 end-0 flex w-10 items-center justify-center text-ink-subtle">
            <x-ui.icon name="chevron-down" :size="16" />
        </span>
    </div>
    @endif
</x-ui.field>
