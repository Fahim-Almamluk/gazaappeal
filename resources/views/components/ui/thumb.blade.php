@props([
    'src' => null,
    'icon' => 'image',
])

@if ($src)
    <img src="{{ $src }}" alt="" loading="lazy" {{ $attributes->class('size-12 shrink-0 rounded-xl border border-line object-cover') }}>
@else
    <span {{ $attributes->class('flex size-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink') }}>
        <x-ui.icon :name="$icon" :size="20" />
    </span>
@endif
