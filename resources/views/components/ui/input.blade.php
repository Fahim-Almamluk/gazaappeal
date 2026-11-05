@props([
    'name',
    'label' => null,
    'hint' => null,
    'icon' => null,
    'type' => 'text',
    'bag' => 'default',
    'compact' => false,
])

@php
    $id = $attributes->get('id', $name);
    $invalid = $errors->getBag($bag)->has(str_replace(['[', ']'], ['.', ''], $name));
@endphp

<x-ui.field :required="(bool) $attributes->get('required')" :name="$name" :label="$label" :hint="$hint" :bag="$bag" :for="$id">
    <div class="group/input relative">
        @if ($icon)
            <span @class([
                'pointer-events-none absolute inset-y-0 start-0 flex w-11 items-center justify-center transition',
                'text-ink-subtle group-focus-within/input:text-primary' => ! $invalid,
                'text-danger' => $invalid,
            ])>
                <x-ui.icon :name="$icon" :size="18" />
            </span>
        @endif

        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if ((bool) $attributes->get('required')) data-req="1" @endif
            @if ($invalid) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
            {{ $attributes->except('id')->class([
                'block w-full rounded-xl border bg-surface text-ink shadow-[0_1px_2px_rgb(20_26_64/0.04)] transition duration-150 placeholder:text-ink-subtle focus:outline-none dark:bg-surface-2',
                'border-line hover:border-line-strong focus:border-primary focus:ring-4 focus:ring-primary/12' => ! $invalid,
                'border-danger focus:ring-4 focus:ring-danger/12' => $invalid,
                'h-10 text-sm' => $compact,
                'h-12 text-[15px]' => ! $compact,
                'ps-11' => $icon,
                'ps-4' => ! $icon,
                'pe-4',
            ]) }}
        >

        {{ $slot }}
    </div>
</x-ui.field>
