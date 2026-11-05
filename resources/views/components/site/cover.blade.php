@props([
    'src' => null,
    'alt' => '',
    'icon' => 'zap',
])

<div {{ $attributes->class('relative isolate overflow-hidden bg-linear-to-br from-brand-400 via-brand-600 to-brand-800') }}>
    @if ($src)
        <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy" decoding="async" class="size-full object-cover transition duration-500 group-hover:scale-[1.03]">
    @else
        <div class="dot-grid absolute inset-0 -z-10 opacity-60"></div>
        <div class="absolute -top-10 -end-10 -z-10 size-40 rounded-full bg-white/15 blur-2xl"></div>
        <div class="flex size-full items-center justify-center text-white/90">
            <span class="flex size-16 items-center justify-center rounded-2xl border border-white/25 bg-white/10 backdrop-blur transition duration-300 group-hover:scale-110">
                <x-ui.icon :name="$icon" :size="30" />
            </span>
        </div>
    @endif
</div>
