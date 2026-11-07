@props(['tag' => 'div'])

<{{ $tag }} {{ $attributes->class('relative isolate overflow-hidden rounded-panel bg-linear-to-l from-brand-700 via-brand-600 to-brand-500 text-white') }}>
    <div class="pointer-events-none absolute -top-24 start-10 -z-10 size-72 rounded-full bg-brand-300/30 blur-3xl"></div>
    <div class="grain pointer-events-none absolute inset-0 -z-10 opacity-[0.15] mix-blend-overlay"></div>
    <div class="dot-grid pointer-events-none absolute inset-0 -z-10 opacity-30 [mask-image:linear-gradient(to_left,transparent_35%,#000)] ltr:[mask-image:linear-gradient(to_right,transparent_35%,#000)]"></div>

    {{ $slot }}
</{{ $tag }}>
