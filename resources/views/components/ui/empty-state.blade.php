@props([
    'icon' => 'inbox',
    'title',
    'body' => null,
])

<div {{ $attributes->class('flex flex-col items-center gap-3 px-6 py-14 text-center') }}>
    <span class="flex size-14 items-center justify-center rounded-2xl bg-surface-2 text-ink-subtle">
        <x-ui.icon :name="$icon" :size="26" />
    </span>
    <p class="font-bold text-ink">{{ $title }}</p>
    @if ($body)
        <p class="max-w-md text-sm leading-6 text-ink-muted">{{ $body }}</p>
    @endif
    {{ $slot }}
</div>
