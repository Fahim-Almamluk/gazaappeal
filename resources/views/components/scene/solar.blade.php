@php
    $iso = new App\Support\Isometric();

    $panels = collect([-128, -98, -68])->crossJoin([-128, -80])->map(fn (array $at): array => [
        'face' => $iso->polygon([$at[0], $at[1], 34], [$at[0] + 26, $at[1], 34], [$at[0] + 26, $at[1] + 40, 16], [$at[0], $at[1] + 40, 16]),
        'cells' => collect([0.33, 0.66])
            ->map(fn (float $f): string => $iso->path([$at[0], $at[1] + 40 * $f, 34 - 18 * $f], [$at[0] + 26, $at[1] + 40 * $f, 34 - 18 * $f]))
            ->push($iso->path([$at[0] + 13, $at[1], 34], [$at[0] + 13, $at[1] + 40, 16]))
            ->implode(''),
        'legs' => $iso->path([$at[0] + 4, $at[1] + 4, 0], [$at[0] + 4, $at[1] + 4, 32]).$iso->path([$at[0] + 22, $at[1] + 36, 0], [$at[0] + 22, $at[1] + 36, 17]),
    ]);

    $station = $iso->box(52, -112, 48, 48, 10);
    $battery = [
        ...$iso->box(2, -120, 32, 24, 30),
        'cable' => $iso->path([18, -96, 0], [18, -40, 0]),
        'levels' => collect([[5, 10], [12, 17], [19, 24]])->map(fn (array $z): string => $iso->polygon([7, -96, $z[0]], [29, -96, $z[0]], [29, -96, $z[1]], [7, -96, $z[1]]))->all(),
        'bolt' => $iso->point(18, -108, 30),
        'glow' => $iso->point(18, -96, 0),
    ];
    $tank = $iso->point(76, -88, 10);
    $clinic = $iso->box(30, 28, 70, 52, 52);
    $windows = collect([42, 58, 74, 88])->map(fn (int $x): string => $iso->polygon([$x, 80, 16], [$x + 8, 80, 16], [$x + 8, 80, 34], [$x, 80, 34]))
        ->merge(collect([38, 56])->map(fn (int $y): string => $iso->polygon([100, $y, 16], [100, $y + 10, 16], [100, $y + 10, 34], [100, $y, 34])));
    $cross = [
        $iso->polygon([50, 48, 52.5], [80, 48, 52.5], [80, 60, 52.5], [50, 60, 52.5]),
        $iso->polygon([59, 39, 52.5], [71, 39, 52.5], [71, 69, 52.5], [59, 69, 52.5]),
    ];

    $tent = [
        'back' => $iso->polygon([-118, 22, 0], [-118, 74, 0], [-90, 74, 48], [-90, 22, 48]),
        'front' => $iso->polygon([-90, 22, 48], [-90, 74, 48], [-62, 74, 0], [-62, 22, 0]),
        'gable' => $iso->polygon([-118, 74, 0], [-62, 74, 0], [-90, 74, 48]),
        'door' => $iso->polygon([-99, 74, 0], [-81, 74, 0], [-90, 74, 26]),
        'glow' => $iso->point(-90, 74),
    ];

    $wires = [
        $iso->path([-50, -60, 0], [-50, -40, 0], [76, -40, 0], [76, -64, 0]),
        $iso->path([-60, -36, 0], [-90, -36, 0], [-90, 20, 0]),
        $iso->path([-40, -40, 0], [10, -40, 0], [10, 54, 0], [30, 54, 0]),
    ];

    $pins = [
        ['icon' => 'solar-panel', 'label' => __('site.scene.panels'), 'from' => [318, 150], 'to' => [318, 70], 'position' => 'left-[53%] top-[15.6%] -translate-y-full', 'class' => 'story-pin [animation-delay:1.58s]'],
        ['icon' => 'droplets', 'label' => __('site.scene.water'), 'from' => [439, 196], 'to' => [439, 120], 'position' => 'left-[73.2%] top-[26.7%] -translate-y-full', 'class' => 'story-pin [animation-delay:2.1s]'],
        ['icon' => 'battery-charging', 'label' => __('site.scene.battery'), 'from' => [409, 205], 'to' => [409, 170], 'position' => 'left-[68.2%] top-[37.8%] -translate-y-full', 'class' => 'story-pin [animation-delay:1.7s]'],
        ['icon' => 'tent', 'label' => __('site.scene.tent'), 'from' => [157, 262], 'to' => [157, 336], 'position' => 'left-[26.2%] top-[74.7%]', 'class' => 'story-pin [animation-delay:2.22s]'],
        ['icon' => 'heart-pulse', 'label' => __('site.scene.clinic'), 'from' => [287, 326], 'to' => [287, 405], 'position' => 'left-[47.8%] top-[90%]', 'class' => 'story-pin [animation-delay:2.42s]'],
    ];

    $shafts = collect(range(0, 11))->map(fn (int $i): array => ['angle' => $i * 30 + 8, 'length' => $i % 2 === 0 ? 118 : 78, 'width' => $i % 2 === 0 ? 9 : 6])->all();
    $flares = [[0.34, 5, 0.32], [0.52, 11, 0.12], [0.68, 3.5, 0.35], [0.86, 16, 0.07]];
    $beam = '96,74 300,116 342,150 252,186';
    $stars = [
        [30, 30, 1.6, '[animation-delay:0s]'], [160, 22, 1.2, '[animation-delay:-1s]'], [210, 54, 1.8, '[animation-delay:-2s]'],
        [270, 18, 1.2, '[animation-delay:-0.5s]'], [520, 40, 1.6, '[animation-delay:-1.5s]'], [570, 90, 1.2, '[animation-delay:-2.5s]'],
        [40, 140, 1.2, '[animation-delay:-3s]'], [480, 80, 1, '[animation-delay:-0.8s]'], [150, 120, 1, '[animation-delay:-1.8s]'],
    ];
@endphp

<x-scene.frame :label="__('site.scene.label')" :pins="$pins" {{ $attributes }}>
    <x-slot:defs>
        <linearGradient id="solar-scene-panel" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#2F8FE0"/>
            <stop offset="1" stop-color="#0D4A8A"/>
        </linearGradient>
        <linearGradient id="solar-scene-tank" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#94BDF0"/>
            <stop offset=".55" stop-color="#2F8FE0"/>
            <stop offset="1" stop-color="#1058AD"/>
        </linearGradient>
        <radialGradient id="solar-scene-halo">
            <stop offset="0" stop-color="#FDE68A" stop-opacity=".55"/>
            <stop offset=".4" stop-color="#FBBF24" stop-opacity=".18"/>
            <stop offset=".75" stop-color="#F59E0B" stop-opacity="0"/>
        </radialGradient>
        <radialGradient id="solar-scene-corona">
            <stop offset="0" stop-color="#FFFBEB"/>
            <stop offset=".3" stop-color="#FEF08A" stop-opacity=".95"/>
            <stop offset=".55" stop-color="#FACC15" stop-opacity=".45"/>
            <stop offset=".8" stop-color="#F59E0B" stop-opacity=".12"/>
            <stop offset="1" stop-color="#F59E0B" stop-opacity="0"/>
        </radialGradient>
        <radialGradient id="solar-scene-core" cx=".42" cy=".38">
            <stop offset="0" stop-color="#FFFFFF"/>
            <stop offset=".6" stop-color="#FFFBEB"/>
            <stop offset="1" stop-color="#FEF3C7"/>
        </radialGradient>
        <linearGradient id="solar-scene-shaft" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#FFF7D6" stop-opacity=".75"/>
            <stop offset="1" stop-color="#FDE68A" stop-opacity="0"/>
        </linearGradient>
        <radialGradient id="solar-scene-warm">
            <stop offset="0" stop-color="#FACC15" stop-opacity=".75"/>
            <stop offset="1" stop-color="#FACC15" stop-opacity="0"/>
        </radialGradient>
        <radialGradient id="solar-scene-moon">
            <stop offset="0" stop-color="#C6DBF8" stop-opacity=".55"/>
            <stop offset="1" stop-color="#C6DBF8" stop-opacity="0"/>
        </radialGradient>
        <linearGradient id="solar-scene-sunbeam" gradientUnits="userSpaceOnUse" x1="96" y1="74" x2="300" y2="170">
            <stop offset="0" stop-color="#FEF08A" stop-opacity=".55"/>
            <stop offset=".6" stop-color="#FDE68A" stop-opacity=".18"/>
            <stop offset="1" stop-color="#FDE68A" stop-opacity="0"/>
        </linearGradient>
        <linearGradient id="solar-scene-dawn" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#1E2A4A"/>
            <stop offset=".42" stop-color="#4B5583"/>
            <stop offset=".72" stop-color="#C9879A"/>
            <stop offset="1" stop-color="#F7C59F"/>
        </linearGradient>
        <radialGradient id="solar-scene-horizon" cx=".16" cy=".72" r=".55">
            <stop offset="0" stop-color="#FDBA74" stop-opacity=".7"/>
            <stop offset="1" stop-color="#FDBA74" stop-opacity="0"/>
        </radialGradient>
        <radialGradient id="solar-scene-dawn-edge">
            <stop offset=".45" stop-color="#FFFFFF"/>
            <stop offset="1" stop-color="#000000"/>
        </radialGradient>
        <mask id="solar-scene-dawn-mask">
            <rect width="600" height="450" fill="url(#solar-scene-dawn-edge)"/>
        </mask>
        <mask id="solar-scene-crescent">
            <rect x="40" y="20" width="120" height="110" fill="#FFFFFF"/>
            <circle cx="107" cy="65" r="17" fill="#000000"/>
        </mask>
    </x-slot:defs>

    <x-slot:sky>
        <g class="dark:hidden" mask="url(#solar-scene-dawn-mask)">
            <g class="story-dawn">
                <rect width="600" height="450" fill="url(#solar-scene-dawn)"/>
                <rect width="600" height="450" fill="url(#solar-scene-horizon)" class="story-horizon"/>
                @foreach ($stars as $star)
                    <circle cx="{{ $star[0] + 60 }}" cy="{{ $star[1] + 40 }}" r="{{ $star[2] * 0.8 }}" fill="#FFFFFF" fill-opacity=".8"/>
                @endforeach
            </g>
        </g>

        <g class="celestial opacity-0 dark:opacity-100 dark:[transition-delay:350ms]">
            @foreach ($stars as $star)
                <circle cx="{{ $star[0] }}" cy="{{ $star[1] }}" r="{{ $star[2] }}" fill="#E9ECF5" class="animate-glow origin-center [transform-box:fill-box] motion-reduce:animate-none {{ $star[3] }}"/>
            @endforeach
        </g>

        <g class="celestial dark:opacity-0">
            <g class="story-light">
                <polygon points="{{ $beam }}" fill="url(#solar-scene-sunbeam)" class="animate-glow origin-[96px_74px] [transform-box:view-box] motion-reduce:animate-none"/>
                @foreach ($flares as [$at, $radius, $opacity])
                    <circle cx="{{ 96 + (330 - 96) * $at }}" cy="{{ 74 + (236 - 74) * $at }}" r="{{ $radius }}" fill="#FDE68A" fill-opacity="{{ $opacity }}"/>
                @endforeach
            </g>
        </g>
        <g class="celestial origin-[96px_330px] [transform-box:view-box] dark:-rotate-[38deg] dark:opacity-0">
            <g class="story-sun">
                <circle cx="96" cy="74" r="96" fill="url(#solar-scene-halo)"/>
                <g class="origin-[96px_74px] animate-[spin_90s_linear_infinite] [transform-box:view-box] motion-reduce:animate-none">
                    @foreach ($shafts as $shaft)
                        <polygon points="0,-1.6 {{ $shaft['length'] }},-{{ $shaft['width'] }} {{ $shaft['length'] }},{{ $shaft['width'] }} 0,1.6" transform="translate(96 74) rotate({{ $shaft['angle'] }})" fill="url(#solar-scene-shaft)"/>
                    @endforeach
                </g>
                <circle cx="96" cy="74" r="46" fill="url(#solar-scene-corona)" class="animate-glow origin-center [transform-box:fill-box] motion-reduce:animate-none"/>
                <circle cx="96" cy="74" r="20" fill="url(#solar-scene-core)"/>
            </g>
        </g>
        <g class="celestial origin-[96px_330px] rotate-[38deg] opacity-0 [transform-box:view-box] dark:rotate-0 dark:opacity-100 dark:[transition-delay:200ms]">
            <circle cx="96" cy="74" r="58" fill="url(#solar-scene-moon)" class="animate-glow origin-center [transform-box:fill-box] motion-reduce:animate-none"/>
            <g mask="url(#solar-scene-crescent)">
                <circle cx="96" cy="74" r="21" fill="#E9ECF5"/>
                <circle cx="88" cy="80" r="3.5" fill="#C6DBF8"/>
                <circle cx="92" cy="66" r="2" fill="#C6DBF8"/>
            </g>
        </g>
    </x-slot:sky>

    <g fill="none" stroke-linecap="round" stroke-linejoin="round">
        @foreach ($wires as $index => $wire)
            <path d="{{ $wire }}" class="stroke-brand-200 dark:stroke-brand-800" stroke-width="6"/>
            <g @class(['story-on', ['[animation-delay:1.66s]', '[animation-delay:1.82s]', '[animation-delay:1.98s]'][$index]])>
                <path d="{{ $wire }}" stroke="#2F8FE0" stroke-width="2.5" stroke-dasharray="5 9" class="animate-flow motion-reduce:animate-none"/>
            </g>
        @endforeach
    </g>

    @foreach ($panels as $index => $panel)
        <path d="{{ $panel['legs'] }}" class="stroke-ink-subtle" stroke-width="2" fill="none"/>
        <polygon points="{{ $panel['face'] }}" fill="url(#solar-scene-panel)" stroke="#94BDF0" stroke-width="1"/>
        <path d="{{ $panel['cells'] }}" stroke="#C6DBF8" stroke-opacity=".55" stroke-width=".8" fill="none"/>
        <polygon points="{{ $panel['face'] }}" fill="#FFF7D6" @class(['story-glint', ['[animation-delay:1.34s]', '[animation-delay:1.38s]', '[animation-delay:1.43s]', '[animation-delay:1.48s]', '[animation-delay:1.53s]', '[animation-delay:1.58s]'][$index]])/>
    @endforeach

    <g class="opacity-0 dark:opacity-100">
        <ellipse cx="{{ $battery['glow'][0] }}" cy="{{ $battery['glow'][1] - 6 }}" rx="46" ry="22" fill="url(#solar-scene-warm)" class="animate-glow origin-center [transform-box:fill-box] motion-reduce:animate-none"/>
    </g>
    <path d="{{ $battery['cable'] }}" class="stroke-brand-200 dark:stroke-brand-800" stroke-width="6" stroke-linecap="round" fill="none"/>
    <g class="story-on [animation-delay:1.38s]">
        <path d="{{ $battery['cable'] }}" stroke="#2F8FE0" stroke-width="2.5" stroke-dasharray="5 9" stroke-linecap="round" fill="none" class="animate-flow motion-reduce:animate-none"/>
    </g>
    <polygon points="{{ $battery['right'] }}" class="fill-slate-600 dark:fill-slate-700"/>
    <polygon points="{{ $battery['left'] }}" class="fill-slate-500 dark:fill-slate-600"/>
    <polygon points="{{ $battery['top'] }}" class="fill-slate-400 dark:fill-slate-500"/>
    @foreach ($battery['levels'] as $index => $level)
        <polygon points="{{ $level }}" class="fill-slate-700 dark:fill-slate-800"/>
        <polygon points="{{ $level }}" @class(['story-on fill-success', ['[animation-delay:1.47s]', '[animation-delay:1.59s]', '[animation-delay:1.7s]'][$index]])/>
    @endforeach
    <polygon points="{{ $battery['bolt'][0] + 1 }},{{ $battery['bolt'][1] - 6 }} {{ $battery['bolt'][0] - 3 }},{{ $battery['bolt'][1] + 1 }} {{ $battery['bolt'][0] - 0.5 }},{{ $battery['bolt'][1] + 1 }} {{ $battery['bolt'][0] - 1.5 }},{{ $battery['bolt'][1] + 6 }} {{ $battery['bolt'][0] + 3 }},{{ $battery['bolt'][1] - 1 }} {{ $battery['bolt'][0] + 0.5 }},{{ $battery['bolt'][1] - 1 }}" class="fill-sun"/>

    <polygon points="{{ $station['right'] }}" class="fill-line-strong dark:fill-surface-hover"/>
    <polygon points="{{ $station['left'] }}" class="fill-line dark:fill-surface-2"/>
    <polygon points="{{ $station['top'] }}" class="fill-surface-2 dark:fill-surface"/>
    <ellipse cx="{{ $tank[0] }}" cy="{{ $tank[1] }}" rx="24" ry="12" fill="#1058AD"/>
    <rect x="{{ $tank[0] - 24 }}" y="{{ $tank[1] - 62 }}" width="48" height="62" fill="#C6DBF8"/>
    <rect x="{{ $tank[0] - 24 }}" y="{{ $tank[1] - 62 }}" width="48" height="62" fill="url(#solar-scene-tank)" class="story-water"/>
    <ellipse cx="{{ $tank[0] }}" cy="{{ $tank[1] - 62 }}" rx="24" ry="12" fill="#C6DBF8"/>
    <path d="M{{ $tank[0] }} {{ $tank[1] - 44 }}c-6 8 -9 12 -9 16a9 9 0 0 0 18 0c0 -4 -3 -8 -9 -16z" fill="#FFFFFF" fill-opacity=".9" class="story-on [animation-delay:2.95s]"/>

    <g class="story-on [animation-delay:2.1s]">
        <ellipse cx="{{ $tent['glow'][0] }}" cy="{{ $tent['glow'][1] - 8 }}" rx="58" ry="28" fill="url(#solar-scene-warm)" class="animate-glow origin-center [transform-box:fill-box] motion-reduce:animate-none"/>
    </g>
    <polygon points="{{ $tent['back'] }}" class="fill-brand-100 dark:fill-brand-800"/>
    <polygon points="{{ $tent['front'] }}" class="fill-brand-200 dark:fill-brand-700"/>
    <polygon points="{{ $tent['gable'] }}" class="fill-surface dark:fill-brand-900"/>
    <polygon points="{{ $tent['door'] }}" class="fill-brand-300 dark:fill-brand-800"/>
    <polygon points="{{ $tent['door'] }}" class="story-on fill-sun [animation-delay:2.1s]"/>

    <polygon points="{{ $clinic['right'] }}" class="fill-brand-100 dark:fill-surface-hover"/>
    <polygon points="{{ $clinic['left'] }}" class="fill-surface dark:fill-surface-2"/>
    <polygon points="{{ $clinic['top'] }}" class="fill-brand-50 stroke-brand-200 dark:fill-surface dark:stroke-line" stroke-width="1"/>
    @foreach ($windows as $index => $window)
        <polygon points="{{ $window }}" class="fill-brand-200 dark:fill-brand-800"/>
        <polygon points="{{ $window }}" @class(['story-on fill-sun', ['[animation-delay:2.22s]', '[animation-delay:2.27s]', '[animation-delay:2.33s]', '[animation-delay:2.37s]', '[animation-delay:2.26s]', '[animation-delay:2.34s]'][$index]])/>
    @endforeach
    @foreach ($cross as $bar)
        <polygon points="{{ $bar }}" class="story-on fill-success [animation-delay:2.42s]"/>
    @endforeach
</x-scene.frame>
