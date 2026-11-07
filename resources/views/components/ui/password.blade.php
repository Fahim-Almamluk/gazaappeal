@props([
    'name' => 'password',
    'label' => null,
    'bag' => 'default',
    'strength' => false,
])

@php
    $id = $attributes->get('id', $name);
    $invalid = $errors->getBag($bag)->has($name);
@endphp

<x-ui.field :required="(bool) $attributes->get('required')" :name="$name" :label="$label" :bag="$bag" :for="$id">
    <div @if ($strength) x-data="passwordStrength" x-on:input="measure" data-tone="" data-weak="{{ __('ui.password.weak') }}" data-medium="{{ __('ui.password.medium') }}" data-strong="{{ __('ui.password.strong') }}" data-very-strong="{{ __('ui.password.very_strong') }}" class="group/strength" @endif>
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
            @if ($strength) minlength="8" @endif
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

    @if ($strength)
        <div class="mt-3 space-y-2.5">
            <div class="flex items-center gap-3">
                <div class="flex flex-1 gap-1" aria-hidden="true">
                    <span data-segment="1" data-on="false" class="h-1.5 flex-1 rounded-full bg-line-strong transition-colors group-data-[tone=danger]/strength:data-[on=true]:bg-danger-solid group-data-[tone=warning]/strength:data-[on=true]:bg-warning group-data-[tone=success]/strength:data-[on=true]:bg-success"></span>
                    <span data-segment="2" data-on="false" class="h-1.5 flex-1 rounded-full bg-line-strong transition-colors group-data-[tone=danger]/strength:data-[on=true]:bg-danger-solid group-data-[tone=warning]/strength:data-[on=true]:bg-warning group-data-[tone=success]/strength:data-[on=true]:bg-success"></span>
                    <span data-segment="3" data-on="false" class="h-1.5 flex-1 rounded-full bg-line-strong transition-colors group-data-[tone=danger]/strength:data-[on=true]:bg-danger-solid group-data-[tone=warning]/strength:data-[on=true]:bg-warning group-data-[tone=success]/strength:data-[on=true]:bg-success"></span>
                    <span data-segment="4" data-on="false" class="h-1.5 flex-1 rounded-full bg-line-strong transition-colors group-data-[tone=danger]/strength:data-[on=true]:bg-danger-solid group-data-[tone=warning]/strength:data-[on=true]:bg-warning group-data-[tone=success]/strength:data-[on=true]:bg-success"></span>
                </div>
                <span x-ref="label" aria-live="polite" class="min-w-16 text-end text-xs font-bold text-ink-subtle group-data-[tone=danger]/strength:text-danger group-data-[tone=warning]/strength:text-warning group-data-[tone=success]/strength:text-success"></span>
            </div>
            <ul class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-ink-subtle">
                @foreach (['min', 'case', 'digit'] as $rule)
                    <li data-rule="{{ $rule }}" data-met="false" class="flex items-center gap-1.5 data-[met=true]:text-success">
                        <x-ui.icon name="circle-check" :size="14" />
                        {{ __("ui.password.{$rule}") }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    </div>
</x-ui.field>
