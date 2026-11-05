@props([
    'name',
    'options' => [],
    'selected' => null,
    'label' => null,
    'hint' => null,
    'placeholder' => null,
    'bag' => 'default',
    'compact' => false,
])

@php
    $id = $attributes->get('id', $name);
    $invalid = $errors->getBag($bag)->has($name);
    $current = (string) old($name, $selected);
@endphp

<x-ui.field :required="(bool) $attributes->get('required')" :name="$name" :label="$label" :hint="$hint" :bag="$bag" :for="$id">
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
</x-ui.field>
