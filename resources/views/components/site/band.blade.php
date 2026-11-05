<div {{ $attributes->class('relative isolate border-y border-line bg-surface py-20 sm:py-24') }}>
    <div class="dot-grid pointer-events-none absolute inset-x-0 top-0 -z-10 h-48 opacity-40 [mask-image:linear-gradient(to_bottom,#000,transparent)]"></div>
    {{ $slot }}
</div>
