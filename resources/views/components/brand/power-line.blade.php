@php
    $poles = [[44, 104, 0.6], [140, 88, 0.75], [252, 66, 0.9], [366, 42, 1]];
    $arm = fn (array $pole): float => 15 * $pole[2];
    $sag = fn (array $from, array $to, float $drop): string => sprintf(
        'M%s %sQ%s %s %s %s',
        $from[0], $from[1] + $drop,
        ($from[0] + $to[0]) / 2, max($from[1], $to[1]) + 16 + $drop,
        $to[0], $to[1] + $drop * ($to[2] / $from[2]),
    );
    $wires = collect([0, 7])->flatMap(fn (int $drop): array => collect(range(0, count($poles) - 2))
        ->map(fn (int $i): string => $sag($poles[$i], $poles[$i + 1], $drop))
        ->all())
        ->push('M366 42Q398 78 424 112');
    $panels = [[176, 132], [222, 129]];
    $feed = 'M303 124Q290 94 264 64';
    $charge = 'M266 140H288';
    $stars = [[30, 22], [62, 70], [150, 18], [196, 44], [300, 16], [330, 52], [410, 26], [456, 60]];
@endphp

<svg viewBox="0 0 480 170" fill="none" {{ $attributes }} aria-hidden="true">
    <defs>
        <radialGradient id="power-line-sun" cx=".5" cy=".5" r=".5">
            <stop offset="0" stop-color="#FEF9C3"/>
            <stop offset=".28" stop-color="#FACC15"/>
            <stop offset=".55" stop-color="#FACC15" stop-opacity=".25"/>
            <stop offset="1" stop-color="#FACC15" stop-opacity="0"/>
        </radialGradient>
        <radialGradient id="power-line-warm" cx=".5" cy=".5" r=".5">
            <stop offset="0" stop-color="#FACC15" stop-opacity=".55"/>
            <stop offset="1" stop-color="#FACC15" stop-opacity="0"/>
        </radialGradient>
        <linearGradient id="power-line-panel" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#FFFFFF" stop-opacity=".45"/>
            <stop offset="1" stop-color="#FFFFFF" stop-opacity=".08"/>
        </linearGradient>
        <radialGradient id="power-line-moon" cx=".5" cy=".5" r=".5">
            <stop offset="0" stop-color="#E9ECF5" stop-opacity=".35"/>
            <stop offset="1" stop-color="#E9ECF5" stop-opacity="0"/>
        </radialGradient>
        <mask id="power-line-crescent">
            <rect x="60" y="6" width="72" height="72" fill="#FFFFFF"/>
            <circle cx="104" cy="35" r="14" fill="#000000"/>
        </mask>
    </defs>

    <g class="dark:hidden">
        <circle cx="96" cy="42" r="58" fill="url(#power-line-sun)"/>
        <circle cx="96" cy="42" r="16" fill="#FEF3C7"/>
        <path d="M110 52L196 124" stroke="#FDE68A" stroke-opacity=".55" stroke-width="1.4" stroke-dasharray="3 6" stroke-linecap="round"/>
    </g>

    <g class="hidden dark:inline">
        <circle cx="96" cy="42" r="40" fill="url(#power-line-moon)"/>
        <circle cx="96" cy="42" r="15" fill="#E9ECF5" mask="url(#power-line-crescent)"/>
        @foreach ($stars as [$x, $y])
            <circle cx="{{ $x }}" cy="{{ $y }}" r="1.2" fill="#FFFFFF" fill-opacity=".7"/>
        @endforeach
    </g>

    <path d="M0 150C70 128 150 126 230 140S390 132 480 142V170H0Z" fill="#FFFFFF" fill-opacity=".08"/>
    <path d="M0 160C110 146 210 144 300 152S420 150 480 156V170H0Z" fill="#FFFFFF" fill-opacity=".1"/>

    <g stroke="#FFFFFF" stroke-linecap="round">
        @foreach ($poles as $pole)
            <path d="M{{ $pole[0] }} {{ 152 - (1 - $pole[2]) * 6 }}V{{ $pole[1] - 5 }}" stroke-opacity="{{ 0.35 + $pole[2] * 0.35 }}" stroke-width="{{ 1.4 + $pole[2] * 1.4 }}"/>
            <path d="M{{ $pole[0] - $arm($pole) }} {{ $pole[1] }}H{{ $pole[0] + $arm($pole) }}" stroke-opacity="{{ 0.35 + $pole[2] * 0.35 }}" stroke-width="{{ 1.2 + $pole[2] }}"/>
            <circle cx="{{ $pole[0] - $arm($pole) + 2 }}" cy="{{ $pole[1] - 2 }}" r="{{ 1.2 + $pole[2] }}" fill="#FFFFFF" fill-opacity=".8" stroke="none"/>
            <circle cx="{{ $pole[0] + $arm($pole) - 2 }}" cy="{{ $pole[1] - 2 }}" r="{{ 1.2 + $pole[2] }}" fill="#FFFFFF" fill-opacity=".8" stroke="none"/>
        @endforeach
    </g>

    <g stroke-linecap="round">
        @foreach ($wires as $wire)
            <path d="{{ $wire }}" stroke="#FFFFFF" stroke-opacity=".3" stroke-width="1"/>
        @endforeach
        <path d="{{ $feed }}" stroke="#FFFFFF" stroke-opacity=".3" stroke-width="1"/>
        @foreach ($wires->push($feed) as $wire)
            <path d="{{ $wire }}" stroke="#FACC15" stroke-opacity=".35" stroke-width="6" stroke-dasharray="0.1 23.9" class="animate-flow motion-reduce:animate-none"/>
            <path d="{{ $wire }}" stroke="#FEF3C7" stroke-width="2.4" stroke-dasharray="0.1 23.9" class="animate-flow motion-reduce:animate-none"/>
        @endforeach
        <g class="dark:hidden">
            <path d="{{ $charge }}" stroke="#FFFFFF" stroke-opacity=".3" stroke-width="1"/>
            <path d="{{ $charge }}" stroke="#86EFAC" stroke-width="2.4" stroke-dasharray="0.1 7.9" class="animate-flow motion-reduce:animate-none"/>
        </g>
    </g>

    <g>
        <rect x="288" y="124" width="30" height="26" rx="4" fill="#FFFFFF" fill-opacity=".14" stroke="#FFFFFF" stroke-opacity=".75" stroke-width="1.4"/>
        <rect x="297" y="120" width="12" height="4" rx="1.5" fill="#FFFFFF" fill-opacity=".75"/>
        @foreach ([0, 1, 2] as $bar)
            <rect x="{{ 293 + $bar * 7 }}" y="130" width="5" height="14" rx="1" fill="#34D399" @class(['dark:opacity-35' => $bar === 2])/>
        @endforeach
    </g>

    @foreach ($panels as [$x, $y])
        <g>
            <path d="M{{ $x + 8 }} {{ $y + 12 }}V{{ $y + 20 }}M{{ $x + 30 }} {{ $y + 6 }}V{{ $y + 18 }}" stroke="#FFFFFF" stroke-opacity=".45" stroke-width="1.4"/>
            <path d="M{{ $x }} {{ $y + 14 }}L{{ $x + 10 }} {{ $y - 6 }}H{{ $x + 42 }}L{{ $x + 32 }} {{ $y + 14 }}Z" fill="url(#power-line-panel)" stroke="#FFFFFF" stroke-opacity=".7" stroke-width="1.2" stroke-linejoin="round"/>
            <path d="M{{ $x + 5 }} {{ $y + 4 }}H{{ $x + 37 }}M{{ $x + 18.5 }} {{ $y - 6 }}L{{ $x + 11 }} {{ $y + 14 }}M{{ $x + 29 }} {{ $y - 6 }}L{{ $x + 21 }} {{ $y + 14 }}" stroke="#FFFFFF" stroke-opacity=".45" stroke-width=".9"/>
        </g>
    @endforeach

    <ellipse cx="436" cy="128" rx="46" ry="22" fill="url(#power-line-warm)"/>
    <g stroke="#FFFFFF" stroke-linejoin="round">
        <path d="M444 152V122L460 110L476 122V152" fill="#FFFFFF" fill-opacity=".08" stroke-opacity=".45" stroke-width="1.3"/>
        <path d="M402 152V116L424 100L446 116V152Z" fill="#FFFFFF" fill-opacity=".14" stroke-opacity=".75" stroke-width="1.6"/>
        <path d="M398 119L424 97L450 119" stroke-opacity=".9" stroke-width="2" stroke-linecap="round"/>
    </g>
    <rect x="410" y="122" width="10" height="9" rx="1.5" fill="#FACC15"/>
    <rect x="428" y="122" width="10" height="9" rx="1.5" fill="#FACC15"/>
    <rect x="419" y="138" width="10" height="14" rx="1.5" fill="#FDE68A" fill-opacity=".85"/>
    <rect x="455" y="128" width="9" height="8" rx="1.5" fill="#FACC15" fill-opacity=".85"/>
</svg>
