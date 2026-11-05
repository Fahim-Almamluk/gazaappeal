@props([
    'name',
    'label' => null,
    'hint' => null,
    'bag' => 'default',
    'for' => null,
    'required' => false,
])

@php
    $error = $errors->getBag($bag)->first(str_replace(['[', ']'], ['.', ''], $name));
    $id = $for ?? $name;
@endphp

<div {{ $attributes->class('space-y-1.5') }}>
    @if ($label)
        <label for="{{ $id }}" class="block text-[13px] font-semibold text-ink">{{ $label }}@if ($required)<span class="ms-1 text-danger" aria-hidden="true" title="{{ __('validation.client.required') }}">*</span>@endif</label>
    @endif

    {{ $slot }}

    @if ($error)
        <p id="{{ $id }}-error" class="flex items-center gap-1.5 text-[13px] font-medium text-danger" role="alert">
            <x-ui.icon name="circle-alert" :size="15" />
            {{ $error }}
        </p>
    @elseif ($hint)
        <p id="{{ $id }}-hint" class="text-[13px] text-ink-subtle">{{ $hint }}</p>
    @endif
</div>
