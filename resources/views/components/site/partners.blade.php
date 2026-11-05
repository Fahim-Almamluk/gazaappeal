@props(['partners'])

<ul {{ $attributes->class('reveal-stagger grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-5') }}>
    @foreach ($partners as $partner)
        @php
            $tag = $partner->website ? 'a' : 'div';
        @endphp

        <li class="reveal">
            <{{ $tag }}
                @if ($partner->website) href="{{ $partner->website }}" target="_blank" rel="noopener noreferrer" @endif
                class="group relative flex h-full min-h-40 flex-col items-center justify-center gap-4 rounded-[22px] border border-line bg-surface px-5 py-7 text-center shadow-card transition duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-float focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
            >
                <span class="pointer-events-none absolute inset-x-8 top-0 h-px bg-linear-to-l from-transparent via-primary/40 to-transparent opacity-0 transition duration-300 group-hover:opacity-100"></span>

                @if ($partner->website)
                    <span class="absolute end-3.5 top-3.5 text-ink-subtle opacity-0 transition duration-300 group-hover:opacity-100">
                        <x-ui.icon name="external-link" :size="14" />
                    </span>
                @endif

                @if ($partner->logo)
                    <span class="flex h-16 w-full items-center justify-center dark:rounded-xl dark:bg-white/90 dark:p-2">
                        <img src="{{ $partner->imageUrl() }}" alt="{{ $partner->name }}" loading="lazy" decoding="async" class="max-h-full max-w-[80%] object-contain transition duration-300 group-hover:scale-105">
                    </span>
                @else
                    <span class="flex size-16 items-center justify-center rounded-2xl border border-line bg-bg text-ink-subtle transition duration-300 group-hover:border-primary/30 group-hover:text-primary">
                        <x-ui.icon name="handshake" :size="28" />
                    </span>
                @endif

                <span class="line-clamp-2 text-sm leading-6 font-bold text-ink">{{ $partner->name }}</span>
            </{{ $tag }}>
        </li>
    @endforeach
</ul>
