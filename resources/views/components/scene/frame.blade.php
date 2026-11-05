@props([
    'label',
    'pins' => [],
])

@php
    $platform = (new App\Support\Isometric())->platform(150, 16);
@endphp

<div {{ $attributes->class('relative aspect-[4/3] w-full select-none') }}>
    <div class="absolute inset-[14%] -z-10 rounded-full bg-brand-400/25 blur-3xl dark:bg-brand-500/20"></div>

    <svg viewBox="0 0 600 450" class="absolute inset-0 size-full" role="img" aria-label="{{ $label }}">
        @isset($defs)
            <defs>{{ $defs }}</defs>
        @endisset

        @isset($sky)
            {{ $sky }}
        @endisset

        <polygon points="{{ $platform['right'] }}" class="fill-line-strong dark:fill-surface-hover"/>
        <polygon points="{{ $platform['left'] }}" class="fill-line dark:fill-surface-2"/>
        <polygon points="{{ $platform['top'] }}" class="fill-surface stroke-brand-200 dark:fill-primary-soft dark:stroke-line" stroke-width="1.5"/>
        <path d="{{ $platform['grid'] }}" class="stroke-brand-100 dark:stroke-line" stroke-width="1" fill="none"/>

        {{ $slot }}

        @foreach ($pins as $pin)
            <g class="{{ $pin['class'] ?? '' }}">
                <path d="M{{ $pin['from'][0] }} {{ $pin['from'][1] }}V{{ $pin['to'][1] }}" class="stroke-primary" stroke-width="1.5" stroke-linecap="round"/>
                <circle cx="{{ $pin['from'][0] }}" cy="{{ $pin['from'][1] }}" r="9" class="fill-primary" fill-opacity=".18"/>
                <circle cx="{{ $pin['from'][0] }}" cy="{{ $pin['from'][1] }}" r="4.5" class="fill-primary stroke-surface" stroke-width="2.5"/>
            </g>
        @endforeach
    </svg>

    @foreach ($pins as $pin)
        <div class="absolute {{ $pin['position'] }} {{ $pin['class'] ?? '' }} flex -translate-x-1/2 items-center gap-1.5 rounded-xl border border-line bg-surface px-2 py-1.5 shadow-elevated sm:gap-2 sm:px-2.5" aria-hidden="true">
            <span class="flex size-5 shrink-0 items-center justify-center rounded-md bg-primary-soft text-primary-soft-ink sm:size-6">
                <x-ui.icon :name="$pin['icon']" :size="14" />
            </span>
            <span class="text-[10px] font-bold whitespace-nowrap text-ink sm:text-xs">{{ $pin['label'] }}</span>
        </div>
    @endforeach
</div>
