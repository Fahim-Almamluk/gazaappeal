@props(['padding' => true])

<div {{ $attributes->class(['rounded-card border border-line bg-surface shadow-elevated', 'p-5 sm:p-6' => $padding]) }}>
    {{ $slot }}
</div>
