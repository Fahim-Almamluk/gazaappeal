@props(['partners'])

<ul {{ $attributes->class('grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4') }}>
    @foreach ($partners as $partner)
        @php
            $tag = $partner->website ? 'a' : 'div';
        @endphp

        <li>
            <{{ $tag }}
                @if ($partner->website) href="{{ $partner->website }}" target="_blank" rel="noopener noreferrer" @endif
                @class([
                    'flex h-24 items-center justify-center rounded-2xl border border-line bg-surface px-4 text-center sm:h-28',
                    'transition-colors hover:border-primary/40 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary' => $partner->website,
                ])
            >
                @if ($partner->logo)
                    <img src="{{ $partner->imageUrl() }}" alt="{{ $partner->name }}" loading="lazy" decoding="async" class="max-h-14 max-w-full object-contain">
                @else
                    <span class="text-sm leading-6 font-bold text-ink-muted sm:text-[15px]">{{ $partner->name }}</span>
                @endif
            </{{ $tag }}>
        </li>
    @endforeach
</ul>
