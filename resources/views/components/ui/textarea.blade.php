@props([
    'name',
    'label' => null,
    'hint' => null,
    'bag' => 'default',
    'rows' => 5,
])

@php
    $id = $attributes->get('id', $name);
    $invalid = $errors->getBag($bag)->has(str_replace(['[', ']'], ['.', ''], $name));
@endphp

<x-ui.field :required="(bool) $attributes->get('required')" :name="$name" :label="$label" :hint="$hint" :bag="$bag" :for="$id">
    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        @if ((bool) $attributes->get('required')) data-req="1" @endif
        rows="{{ $rows }}"
        @if ($invalid) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->except('id', 'value')->class([
            'block w-full resize-y rounded-xl border bg-surface px-4 py-3 text-[15px] leading-7 text-ink shadow-[0_1px_2px_rgb(20_26_64/0.04)] transition duration-150 placeholder:text-ink-subtle focus:outline-none dark:bg-surface-2',
            'border-line hover:border-line-strong focus:border-primary focus:ring-4 focus:ring-primary/12' => ! $invalid,
            'border-danger focus:ring-4 focus:ring-danger/12' => $invalid,
        ]) }}
    >{{ $attributes->get('value', $slot) }}</textarea>
</x-ui.field>
