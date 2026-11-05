@props([
    'title',
    'items' => [],
    'tone' => 'info',
])

<div {{ $attributes->class('rounded-card border border-line bg-surface p-5 shadow-elevated') }}>
    <h3 class="flex items-center gap-2 font-bold text-ink"><x-ui.icon name="info" :size="17" class="text-primary" />{{ $title }}</h3>
    <ul class="mt-3 space-y-3">
        @foreach ($items as $item)
            <li class="flex gap-2.5 text-sm leading-6 text-ink-muted">
                <x-ui.icon name="check" :size="15" class="mt-1 shrink-0 text-success" />
                <span>{{ $item }}</span>
            </li>
        @endforeach
    </ul>
    {{ $slot }}
</div>
