@props([
    'name' => 'password',
    'label' => null,
    'bag' => 'default',
])

@php
    $id = $attributes->get('id', $name);
    $invalid = $errors->getBag($bag)->has($name);
@endphp

<x-ui.field :required="(bool) $attributes->get('required')" :name="$name" :label="$label" :bag="$bag" :for="$id">
    <div
        class="group/input relative"
        x-data="passwordField"
        data-show-label="{{ __('auth.login.show_password') }}"
        data-hide-label="{{ __('auth.login.hide_password') }}"
    >
        <span @class([
            'pointer-events-none absolute inset-y-0 start-0 flex w-11 items-center justify-center transition',
            'text-ink-subtle group-focus-within/input:text-primary' => ! $invalid,
            'text-danger' => $invalid,
        ])>
            <x-ui.icon name="lock" :size="18" />
        </span>

        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="password"
            x-bind:type="type"
            @if ($invalid) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
            @if ((bool) $attributes->get('required')) data-req="1" @endif
                {{ $attributes->except('id')->merge(['autocomplete' => 'current-password'])->class([
                'block h-12 w-full rounded-xl border bg-surface ps-11 pe-12 text-[15px] text-ink shadow-[0_1px_2px_rgb(20_26_64/0.04)] transition duration-150 placeholder:text-ink-subtle focus:outline-none dark:bg-surface-2',
                'border-line hover:border-line-strong focus:border-primary focus:ring-4 focus:ring-primary/12' => ! $invalid,
                'border-danger focus:ring-4 focus:ring-danger/12' => $invalid,
            ]) }}
        >

        <button
            type="button"
            class="absolute inset-y-0 end-1.5 my-auto flex size-9 items-center justify-center rounded-lg text-ink-subtle transition hover:bg-surface-hover hover:text-ink"
            x-on:click="toggle"
            x-bind:aria-label="label"
            x-bind:aria-pressed="visible"
            aria-label="{{ __('auth.login.show_password') }}"
        >
            <span x-show="hidden"><x-ui.icon name="eye" :size="18" /></span>
            <span x-show="visible" x-cloak><x-ui.icon name="eye-off" :size="18" /></span>
        </button>
    </div>
</x-ui.field>
