@php
    $iso = new App\Support\Isometric();

    $delays = [
        '[animation-delay:0.3s]', '[animation-delay:0.7s]', '[animation-delay:1.1s]', '[animation-delay:1.5s]',
        '[animation-delay:1.9s]', '[animation-delay:2.3s]', '[animation-delay:2.7s]', '[animation-delay:3.1s]',
    ];
    $palettes = [
        ['top' => 'fill-brand-50 dark:fill-surface-hover', 'right' => 'fill-brand-200 dark:fill-surface-2', 'left' => 'fill-brand-100 dark:fill-surface'],
        ['top' => 'fill-surface dark:fill-brand-800', 'right' => 'fill-line-strong dark:fill-brand-900', 'left' => 'fill-line dark:fill-brand-950'],
        ['top' => 'fill-brand-100 dark:fill-surface', 'right' => 'fill-brand-300 dark:fill-surface-2', 'left' => 'fill-brand-200 dark:fill-surface-hover'],
    ];
    $profiles = [
        [1, 0.78, 0.92, 0.68, 0.84],
        [0.88, 1, 0.72, 0.9, 0.64],
        [0.74, 0.94, 0.82, 1, 0.76],
    ];

    $windowsFor = function (array $b, int $index, float $limit, bool $lit = true) use ($iso, $delays): array {
        [$x, $y, $w, $d, $h] = $b;
        $top = min($h, $limit);
        $bucket = min(count($delays) - 1, (int) floor(hypot($x + $w / 2, $y + $d / 2) / 22));
        $windows = [];

        for ($floor = 0, $z = 8; $z + 7 <= $top - 6; $floor++, $z += 13) {
            for ($column = 0, $along = 4; $along + 5 <= $d - 3; $column++, $along += 10) {
                $windows[] = [
                    'shape' => $iso->polygon([$x + $w, $y + $along, $z], [$x + $w, $y + $along + 5, $z], [$x + $w, $y + $along + 5, $z + 7], [$x + $w, $y + $along, $z + 7]),
                    'lit' => $lit && ($index * 7 + $floor * 3 + $column) % 3 === 0,
                    'delay' => $delays[min(count($delays) - 1, $bucket + $floor % 2)],
                ];
            }
            for ($column = 0, $along = 4; $along + 5 <= $w - 3; $column++, $along += 10) {
                $windows[] = [
                    'shape' => $iso->polygon([$x + $along, $y + $d, $z], [$x + $along + 5, $y + $d, $z], [$x + $along + 5, $y + $d, $z + 7], [$x + $along, $y + $d, $z + 7]),
                    'lit' => $lit && ($index * 5 + $floor * 2 + $column) % 3 === 0,
                    'delay' => $delays[min(count($delays) - 1, $bucket + ($floor + 1) % 2)],
                ];
            }
        }

        return $windows;
    };

    $whole = fn (array $b, int $index): array => [
        'kind' => 'whole',
        ...$iso->box($b[0], $b[1], $b[2], $b[3], $b[4]),
        'palette' => $palettes[$index % count($palettes)],
        'windows' => $windowsFor($b, $index, $b[4]),
        'tank' => $index % 3 === 0 ? $iso->point($b[0] + $b[2] / 2, $b[1] + $b[3] / 2, $b[4]) : null,
        'panel' => $index % 4 === 1 ? $iso->polygon([$b[0] + 8, $b[1] + 8, $b[4] + 10], [$b[0] + 26, $b[1] + 8, $b[4] + 10], [$b[0] + 26, $b[1] + 26, $b[4] + 2], [$b[0] + 8, $b[1] + 26, $b[4] + 2]) : null,
    ];

    $broken = function (array $b, int $index) use ($iso, $profiles, $palettes, $windowsFor): array {
        [$x, $y, $w, $d, $h] = $b;
        $standing = round($w * 0.58);
        $fallen = max(9, round($h * 0.3));
        $heights = fn (int $shift, float $scale = 1): array => array_map(fn (float|int $ratio): float => round($h * $ratio * $scale, 1), $profiles[($index + $shift) % count($profiles)]);
        $across = $heights(0);
        $along = $heights(1);
        $rebar = [];

        foreach ([1, 3] as $step) {
            $at = $y + $d * $step / 4;
            $rebar[] = $iso->path([$x + $standing, $at, $across[$step]], [$x + $standing, $at, $across[$step] + 8]);
        }

        return [
            'kind' => 'broken',
            'innerX' => $iso->wall('x', $x, $y, $y + $d, $heights(1, 0.8)),
            'innerY' => $iso->wall('y', $y, $x, $x + $standing, $heights(0, 0.8)),
            'right' => $iso->wall('x', $x + $standing, $y, $y + $d, $across),
            'left' => $iso->wall('y', $y + $d, $x, $x + $standing, $along),
            'windows' => $windowsFor([$x, $y, $standing, $d, $h], $index, $h * 0.46, false),
            'fallen' => $iso->box($x + $standing, $y, $w - $standing, $d, $fallen),
            'rubble' => [
                $iso->box($x + $standing + 3, $y + 5, 10, 9, 7, $fallen),
                $iso->box($x + $standing + 15, $y + $d - 17, 12, 11, 5, $fallen),
                $iso->box($x + $standing - 2, $y + $d + 2, 9, 8, 5),
                $iso->box($x + $w - 8, $y + $d - 12, 8, 9, 6),
            ],
            'rebar' => $rebar,
        ];
    };

    $fence = function (array $from, array $to) use ($iso): array {
        $posts = max(2, (int) round(hypot($to[0] - $from[0], $to[1] - $from[1]) / 10));
        $point = fn (int $step, float $z): array => [
            $from[0] + ($to[0] - $from[0]) * $step / $posts,
            $from[1] + ($to[1] - $from[1]) * $step / $posts,
            $z,
        ];
        $mesh = '';
        $poles = [];

        foreach (range(0, $posts) as $step) {
            $poles[] = $iso->path($point($step, 0), $point($step, 9));
        }
        foreach (range(0, $posts - 1) as $step) {
            $mesh .= $iso->path($point($step, 0), $point($step + 1, 9)).$iso->path($point($step, 9), $point($step + 1, 0));
        }

        return ['poles' => $poles, 'mesh' => $mesh, 'rails' => $iso->path($point(0, 9), $point($posts, 9)).$iso->path($point(0, 4.5), $point($posts, 4.5))];
    };

    $camp = function (array $b) use ($iso, $fence): array {
        [$x, $y, $w, $d] = $b;
        $at = fn (float $dx, float $dy, float $z = 0): array => $iso->point($x + $dx, $y + $dy, $z);
        $columns = [7, 35, 66, 94];
        $rows = [8, 30, 52, 74, 96];

        $tents = collect(array_keys($columns))
            ->crossJoin(array_keys($rows))
            ->reject(fn (array $cell): bool => in_array($cell[0], [1, 2], true) && in_array($cell[1], [1, 2, 3], true))
            ->values()
            ->map(fn (array $cell, int $i): array => [
                'type' => 'tent',
                'sort' => $columns[$cell[0]] + $rows[$cell[1]],
                ...$iso->tent($x + $columns[$cell[0]], $y + $rows[$cell[1]], 17, 11, 14),
                'lit' => $i % 3 === 0,
                'blue' => $i % 4 === 1,
            ]);

        $laundry = collect([29.5, 88.5])->map(fn (float $at, int $i): array => [
            'type' => 'laundry',
            'sort' => $at + 30,
            'line' => $iso->path([$x + $at, $y + 32, 13], [$x + $at, $y + 84, 13]),
            'poles' => [$iso->path([$x + $at, $y + 32, 0], [$x + $at, $y + 32, 14]), $iso->path([$x + $at, $y + 84, 0], [$x + $at, $y + 84, 14])],
            'clothes' => collect(range(0, 6))->map(fn (int $step): array => [
                'shape' => $iso->polygon([$x + $at, $y + 36 + $step * 7, 13], [$x + $at, $y + 39 + $step * 7, 13], [$x + $at, $y + 39 + $step * 7, 8.5], [$x + $at, $y + 36 + $step * 7, 8.5]),
                'tone' => ['fill-brand-400', 'fill-brand-100', 'fill-sun', 'fill-brand-300'][($step + $i) % 4],
            ])->all(),
        ]);

        $people = collect(range(0, 5))->map(function (int $step) use ($iso, $x, $y, $w, $d): array {
            $px = $w / 2 + 12 * cos(deg2rad($step * 60 + 20));
            $py = $d / 2 + 12 * sin(deg2rad($step * 60 + 20));

            return [
                'type' => 'person',
                'sort' => $px + $py,
                'body' => $iso->path([$x + $px, $y + $py, 0], [$x + $px, $y + $py, 6]),
                'head' => $iso->point($x + $px, $y + $py, 8),
                'bucket' => $iso->point($x + $px + 2, $y + $py + 2, 1.5),
            ];
        });

        $items = $tents->concat($laundry)->concat($people)->concat([
            ['type' => 'well', 'sort' => $w / 2 + $d / 2, 'at' => $iso->point($x + $w / 2, $y + $d / 2)],
            ['type' => 'tank', 'sort' => 118, 'at' => $iso->point($x + 74, $y + 44)],
            ['type' => 'crates', 'sort' => 116, 'boxes' => [$iso->box($x + 43, $y + 70, 6, 5, 4), $iso->box($x + 44, $y + 71, 4, 3, 4, 4)]],
        ])->sortBy('sort')->values()->all();

        return [
            'kind' => 'camp',
            'pad' => $iso->polygon([$x, $y, 0.2], [$x + $w, $y, 0.2], [$x + $w, $y + $d, 0.2], [$x, $y + $d, 0.2]),
            'back' => [$fence([$x, $y], [$x + $w, $y]), $fence([$x, $y], [$x, $y + $d])],
            'front' => [$fence([$x + $w, $y], [$x + $w, $y + $d]), $fence([$x, $y + $d], [$x + $w, $y + $d])],
            'items' => $items,
        ];
    };

    $yard = function (array $b) use ($iso, $fence): array {
        [$x, $y, $w, $d] = $b;
        $at = fn (float $dx, float $dy, float $z = 0): array => $iso->point($x + $dx, $y + $dy, $z);
        $ground = fn (array ...$corners): string => $iso->path(...array_map(fn (array $c): array => [$x + $c[0], $y + $c[1], 0], $corners));

        $generators = collect([6, 44, 82])->map(function (int $dx, int $i) use ($iso, $x, $y, $at): array {
            $gy = $y + 6;

            return [
                'type' => 'generator',
                'sort' => $dx + 6,
                ...$iso->box($x + $dx, $gy, 30, 12, 14),
                'louvers' => collect(range(0, 4))->map(fn (int $step): string => $iso->path([$x + $dx + 4 + $step * 3.4, $gy + 12, 3], [$x + $dx + 4 + $step * 3.4, $gy + 12, 11]))->implode(''),
                'stack' => $at($dx + 5, 9, 14),
                'bolt' => $at($dx + 24, 18, 7),
                'light' => $at($dx + 12, 18, 11),
                'delay' => ['[animation-delay:2.1s]', '[animation-delay:2.5s]', '[animation-delay:2.9s]'][$i],
            ];
        });

        $transformers = collect([15, 53, 91])->map(fn (int $dx): array => [
            'type' => 'transformer',
            'sort' => $dx + 42,
            ...$iso->box($x + $dx, $y + 42, 12, 9, 10),
            'tops' => collect([0, 1, 2])->map(fn (int $step): array => $at($dx + 3 + $step * 3, 46.5, 10))->all(),
        ]);

        $disc = fn (float $cx, float $cy, float $cz, float $radius): string => $iso->polygon(
            ...collect(range(0, 13))->map(fn (int $step): array => [$cx + $radius * cos($step * M_PI / 7), $cy, $cz + $radius * sin($step * M_PI / 7)])->all()
        );

        $trucks = collect([[66, true], [92, false]])->map(fn (array $t): array => [
            'type' => 'truck',
            'sort' => 6 + $t[0],
            'cab' => $iso->box($x + 6, $y + $t[0], 8, 12, 9),
            'bed' => $iso->box($x + 14, $y + $t[0], 20, 12, 4),
            'window' => $iso->polygon([$x + 7.5, $y + $t[0] + 12, 4.5], [$x + 12.5, $y + $t[0] + 12, 4.5], [$x + 12.5, $y + $t[0] + 12, 8], [$x + 7.5, $y + $t[0] + 12, 8]),
            'wheels' => collect([10, 22, 30])->map(fn (int $dx): array => ['tyre' => $disc($x + $dx, $y + $t[0] + 12, 3, 3), 'hub' => $disc($x + $dx, $y + $t[0] + 12, 3, 1.2)])->all(),
            'reel' => $t[1] ? ['rim' => $disc($x + 24, $y + $t[0] + 12.5, 11, 7), 'wound' => $disc($x + 24, $y + $t[0] + 12.6, 11, 4.6), 'hub' => $disc($x + 24, $y + $t[0] + 12.7, 11, 1.6), 'core' => $iso->box($x + 21, $y + $t[0] + 1, 6, 11, 4, 4), 'tail' => $iso->path([$x + 24, $y + $t[0] + 12.6, 4], [$x + 24, $y + $t[0] + 16, 0.4], [$x + 36, $y + $t[0] + 16, 0.4])] : null,
            'poles' => $t[1] ? [] : [$iso->box($x + 15, $y + $t[0] + 1.5, 18, 3, 3, 4), $iso->box($x + 15, $y + $t[0] + 6, 18, 3, 3, 4), $iso->box($x + 16, $y + $t[0] + 3.7, 16, 3, 3, 7)],
        ]);

        $towerAt = fn (float $dy): array => [
            'mast' => $iso->path([$x + 114, $y + $dy, 0], [$x + 114, $y + $dy, 36]),
            'arm' => $iso->path([$x + 108, $y + $dy, 33], [$x + 120, $y + $dy, 33]),
            'caps' => collect([108, 114, 120])->map(fn (int $dx): array => $iso->point($x + $dx, $y + $dy, 34.5))->all(),
        ];
        $sag = fn (float $dx): string => $iso->path(...collect(range(0, 8))->map(fn (int $step): array => [$x + $dx, $y + 58 + 42 * $step / 8, 34.5 - 5 * sin($step * M_PI / 8)])->all());
        $line = [
            'type' => 'line',
            'sort' => 215,
            'poles' => [$towerAt(58), $towerAt(100)],
            'wires' => $sag(108).$sag(114).$sag(120),
            'lineman' => ['body' => $iso->path([$x + 115.5, $y + 58, 20], [$x + 115.5, $y + 58, 26]), 'head' => $at(115.5, 58, 28)],
        ];

        $drums = collect([[54, 74], [68, 74], [61, 86]])->map(fn (array $p): array => [
            'type' => 'drum',
            'sort' => $p[0] + $p[1],
            'at' => $at($p[0], $p[1]),
        ]);

        $workers = collect([[30, 26], [72, 28], [36, 58], [40, 80], [44, 104], [106, 100]])->map(fn (array $p): array => [
            'type' => 'worker',
            'sort' => $p[0] + $p[1],
            'body' => $iso->path([$x + $p[0], $y + $p[1], 0], [$x + $p[0], $y + $p[1], 6]),
            'head' => $at($p[0], $p[1], 8),
        ]);

        $cables = collect([21, 59, 97])
            ->map(fn (int $dx): string => $ground([$dx, 20], [$dx, 42]))
            ->push($ground([21, 36], [97, 36]))
            ->push($ground([103, 47], [114, 47], [114, 58]))
            ->implode('');

        $items = $generators->concat($transformers)->concat($trucks)->concat($drums)->concat($workers)->concat([
            $line,
            ['type' => 'logs', 'sort' => 150, 'boxes' => [$iso->box($x + 50, $y + 98, 30, 3, 3), $iso->box($x + 50, $y + 102, 30, 3, 3), $iso->box($x + 52, $y + 100, 26, 3, 3, 3)]],
            ['type' => 'cabin', 'sort' => 158, ...$iso->box($x + 88, $y + 70, 22, 14, 10), 'window' => $iso->polygon([$x + 92, $y + 84, 3], [$x + 100, $y + 84, 3], [$x + 100, $y + 84, 8], [$x + 92, $y + 84, 8])],
        ])->sortBy('sort')->values()->all();

        return [
            'kind' => 'yard',
            'pad' => $iso->polygon([$x, $y, 0.2], [$x + $w, $y, 0.2], [$x + $w, $y + $d, 0.2], [$x, $y + $d, 0.2]),
            'cables' => $cables,
            'back' => [$fence([$x, $y], [$x + $w, $y]), $fence([$x, $y], [$x, $y + $d])],
            'front' => [$fence([$x + $w, $y], [$x + $w, $y + $d]), $fence([$x, $y + $d], [$x + $w, $y + $d])],
            'items' => $items,
        ];
    };

    $objects = collect([
        [-132, -132, 42, 42, 74, 'broken'], [-80, -132, 48, 32, 46, 'whole'], [-132, -80, 32, 48, 42, 'whole'], [-76, -84, 46, 54, 96, 'whole'],
        [30, -132, 44, 44, 56, 'broken'], [84, -132, 50, 40, 84, 'whole'], [30, -76, 40, 46, 38, 'broken'], [82, -80, 52, 50, 62, 'broken'],
        [22, 22, 118, 118, 0, 'camp'], [-140, 22, 118, 118, 0, 'yard'],
    ])
        ->sortBy(fn (array $b): float => $b[0] + $b[1] + $b[2] + $b[3])
        ->values()
        ->map(fn (array $b, int $index): array => match ($b[5]) {
            'broken' => $broken($b, $index),
            'camp' => $camp($b),
            'yard' => $yard($b),
            default => $whole($b, $index),
        });

    $roads = [
        $iso->polygon([-150, -12, 0], [150, -12, 0], [150, 12, 0], [-150, 12, 0]),
        $iso->polygon([-12, -150, 0], [12, -150, 0], [12, 150, 0], [-12, 150, 0]),
    ];
    $markings = $iso->path([-150, 0, 0], [150, 0, 0]).$iso->path([0, -150, 0], [0, 150, 0]);
    $current = [
        $iso->path([-150, -16, 0], [150, -16, 0]),
        $iso->path([16, -150, 0], [16, 150, 0]),
    ];
    $lamps = collect([[-18, -18], [18, -18], [-18, 18], [18, 18], [-90, -18], [90, 18], [-18, 90], [18, -90]])
        ->map(fn (array $at, int $index): array => [
            'pole' => $iso->path([$at[0], $at[1], 0], [$at[0], $at[1], 26]),
            'light' => $iso->point($at[0], $at[1], 27),
            'delay' => $delays[$index % 4],
        ]);

    $car = function (float $x, float $y, bool $alongX, bool $forward) use ($iso): array {
        $half = $forward ? 11 : -11;

        return [
            ...($alongX ? $iso->box($x - 11, $y - 5, 22, 10, 7) : $iso->box($x - 5, $y - 11, 10, 22, 7)),
            'glass' => $iso->polygon([$x - 4.5, $y - 3.5, 7.1], [$x + 4.5, $y - 3.5, 7.1], [$x + 4.5, $y + 3.5, 7.1], [$x - 4.5, $y + 3.5, 7.1]),
            'light' => $alongX ? $iso->point($x + $half, $y, 2.5) : $iso->point($x, $y + $half, 2.5),
        ];
    };
    $cars = [
        ['shape' => $car(-130, -6, true, true), 'paint' => 'fill-brand-400', 'motion' => 'animate-drive-x'],
        ['shape' => $car(6, -130, false, true), 'paint' => 'fill-surface', 'motion' => 'animate-drive-y [animation-delay:-3s]'],
    ];
@endphp

<x-scene.frame :label="__('auth.scene.label')" {{ $attributes }}>
    <x-slot:defs>
        <radialGradient id="neighborhood-lamp">
            <stop offset="0" stop-color="#FEF9C3"/>
            <stop offset=".4" stop-color="#FACC15" stop-opacity=".7"/>
            <stop offset="1" stop-color="#FACC15" stop-opacity="0"/>
        </radialGradient>
    </x-slot:defs>

    @foreach ($roads as $road)
        <polygon points="{{ $road }}" class="fill-line dark:fill-surface-2"/>
    @endforeach
    <path d="{{ $markings }}" class="stroke-surface dark:stroke-line-strong" stroke-width="1.5" stroke-dasharray="6 8" fill="none"/>
    @foreach ($current as $line)
        <path d="{{ $line }}" stroke="#FACC15" stroke-width="2" stroke-dasharray="4 10" stroke-linecap="round" fill="none" class="animate-flow motion-reduce:animate-none"/>
    @endforeach

    @foreach ($cars as $vehicle)
        <g class="{{ $vehicle['motion'] }} motion-reduce:hidden">
            <polygon points="{{ $vehicle['shape']['right'] }}" class="fill-brand-700"/>
            <polygon points="{{ $vehicle['shape']['left'] }}" class="fill-brand-600"/>
            <polygon points="{{ $vehicle['shape']['top'] }}" class="{{ $vehicle['paint'] }}"/>
            <polygon points="{{ $vehicle['shape']['glass'] }}" class="fill-brand-900/70"/>
            <circle cx="{{ $vehicle['shape']['light'][0] }}" cy="{{ $vehicle['shape']['light'][1] }}" r="9" fill="url(#neighborhood-lamp)"/>
            <circle cx="{{ $vehicle['shape']['light'][0] }}" cy="{{ $vehicle['shape']['light'][1] }}" r="2" fill="#FEF9C3"/>
        </g>
    @endforeach

    @foreach ($objects as $object)
        @if ($object['kind'] === 'camp')
            <polygon points="{{ $object['pad'] }}" class="fill-brand-50 dark:fill-surface-2"/>
            @foreach ($object['back'] as $side)
                @include('components.scene.partials.wire-fence', ['side' => $side])
            @endforeach

            @foreach ($object['items'] as $item)
                @switch($item['type'])
                    @case('tent')
                        <polygon points="{{ $item['gable'] }}" class="{{ $item['blue'] ? 'fill-brand-500 dark:fill-brand-600' : 'fill-brand-300 dark:fill-brand-700' }}"/>
                        <polygon points="{{ $item['slope'] }}" class="{{ $item['blue'] ? 'fill-brand-400 dark:fill-brand-500' : 'fill-brand-100 dark:fill-brand-300' }} stroke-brand-400 dark:stroke-brand-800" stroke-width=".8"/>
                        @if ($item['lit'])
                            <circle cx="{{ $item['lantern'][0] }}" cy="{{ $item['lantern'][1] }}" r="6" fill="url(#neighborhood-lamp)" class="animate-light-on motion-reduce:animate-none [animation-delay:2.1s]"/>
                            <circle cx="{{ $item['lantern'][0] }}" cy="{{ $item['lantern'][1] }}" r="1.6" class="fill-sun animate-light-on motion-reduce:animate-none [animation-delay:2.1s]"/>
                        @endif
                        @break

                    @case('well')
                        @php([$cx, $cy] = $item['at'])
                        <ellipse cx="{{ $cx }}" cy="{{ $cy }}" rx="7" ry="3.5" class="fill-slate-500"/>
                        <rect x="{{ $cx - 7 }}" y="{{ $cy - 5 }}" width="14" height="5" class="fill-slate-400"/>
                        <ellipse cx="{{ $cx }}" cy="{{ $cy - 5 }}" rx="7" ry="3.5" class="fill-slate-300"/>
                        <ellipse cx="{{ $cx }}" cy="{{ $cy - 5 }}" rx="4.8" ry="2.4" class="fill-brand-500"/>
                        <path d="M{{ $cx - 6.5 }} {{ $cy - 5 }}V{{ $cy - 19 }}M{{ $cx + 6.5 }} {{ $cy - 5 }}V{{ $cy - 19 }}" class="stroke-slate-600" stroke-width="1.6" stroke-linecap="round"/>
                        <polygon points="{{ $cx - 9.5 }},{{ $cy - 19 }} {{ $cx + 9.5 }},{{ $cy - 19 }} {{ $cx }},{{ $cy - 26 }}" class="fill-brand-600"/>
                        <path d="M{{ $cx }} {{ $cy - 19 }}V{{ $cy - 10 }}" class="stroke-slate-600" stroke-width=".8"/>
                        <rect x="{{ $cx - 1.8 }}" y="{{ $cy - 10 }}" width="3.6" height="3" rx=".6" class="fill-brand-300"/>
                        @break

                    @case('tank')
                        @php([$cx, $cy] = $item['at'])
                        <path d="M{{ $cx - 5 }} {{ $cy }}V{{ $cy - 6 }}M{{ $cx + 5 }} {{ $cy }}V{{ $cy - 6 }}" class="stroke-slate-500" stroke-width="1.4"/>
                        <rect x="{{ $cx - 6 }}" y="{{ $cy - 16 }}" width="12" height="10" class="fill-brand-500"/>
                        <ellipse cx="{{ $cx }}" cy="{{ $cy - 6 }}" rx="6" ry="3" class="fill-brand-600"/>
                        <ellipse cx="{{ $cx }}" cy="{{ $cy - 16 }}" rx="6" ry="3" class="fill-brand-300"/>
                        @break

                    @case('laundry')
                        @foreach ($item['poles'] as $pole)
                            <path d="{{ $pole }}" class="stroke-slate-500" stroke-width="1.2" stroke-linecap="round"/>
                        @endforeach
                        <path d="{{ $item['line'] }}" class="stroke-slate-500" stroke-width=".8" fill="none"/>
                        @foreach ($item['clothes'] as $cloth)
                            <polygon points="{{ $cloth['shape'] }}" class="{{ $cloth['tone'] }}"/>
                        @endforeach
                        @break

                    @case('crates')
                        @foreach ($item['boxes'] as $box)
                            <polygon points="{{ $box['right'] }}" class="fill-brand-600"/>
                            <polygon points="{{ $box['left'] }}" class="fill-brand-500"/>
                            <polygon points="{{ $box['top'] }}" class="fill-brand-200"/>
                        @endforeach
                        @break

                    @case('person')
                        <path d="{{ $item['body'] }}" class="stroke-slate-700" stroke-width="2.4" stroke-linecap="round"/>
                        <circle cx="{{ $item['head'][0] }}" cy="{{ $item['head'][1] }}" r="1.8" class="fill-slate-800"/>
                        <ellipse cx="{{ $item['bucket'][0] }}" cy="{{ $item['bucket'][1] }}" rx="1.8" ry="1.1" class="fill-brand-400"/>
                        @break
                @endswitch
            @endforeach

            @foreach ($object['front'] as $side)
                @include('components.scene.partials.wire-fence', ['side' => $side])
            @endforeach
        @elseif ($object['kind'] === 'yard')
            @include('components.scene.partials.power-yard', ['object' => $object])
        @elseif ($object['kind'] === 'broken')
            <polygon points="{{ $object['innerX'] }}" class="fill-slate-800"/>
            <polygon points="{{ $object['innerY'] }}" class="fill-slate-700"/>
            <polygon points="{{ $object['right'] }}" class="fill-slate-600"/>
            <polygon points="{{ $object['left'] }}" class="fill-slate-500"/>

            @foreach ($object['windows'] as $window)
                <polygon points="{{ $window['shape'] }}" class="fill-slate-900"/>
            @endforeach

            @foreach ($object['rebar'] as $bar)
                <path d="{{ $bar }}" class="stroke-slate-400" stroke-width="1.2" stroke-linecap="round"/>
            @endforeach

            <polygon points="{{ $object['fallen']['right'] }}" class="fill-slate-600"/>
            <polygon points="{{ $object['fallen']['left'] }}" class="fill-slate-500"/>
            <polygon points="{{ $object['fallen']['top'] }}" class="fill-slate-400"/>

            @foreach ($object['rubble'] as $block)
                <polygon points="{{ $block['right'] }}" class="fill-slate-700"/>
                <polygon points="{{ $block['left'] }}" class="fill-slate-600"/>
                <polygon points="{{ $block['top'] }}" class="fill-slate-500"/>
            @endforeach
        @else
            <polygon points="{{ $object['right'] }}" class="{{ $object['palette']['right'] }}"/>
            <polygon points="{{ $object['left'] }}" class="{{ $object['palette']['left'] }}"/>
            <polygon points="{{ $object['top'] }}" class="{{ $object['palette']['top'] }} stroke-line dark:stroke-surface-hover" stroke-width=".8"/>

            @foreach ($object['windows'] as $window)
                <polygon points="{{ $window['shape'] }}" class="fill-brand-800/70 dark:fill-brand-950"/>
                @if ($window['lit'])
                    <polygon points="{{ $window['shape'] }}" class="fill-sun animate-light-on motion-reduce:animate-none {{ $window['delay'] }}"/>
                @endif
            @endforeach

            @if ($object['panel'])
                <polygon points="{{ $object['panel'] }}" class="fill-brand-500 stroke-brand-200" stroke-width=".8"/>
            @endif

            @if ($object['tank'])
                <ellipse cx="{{ $object['tank'][0] }}" cy="{{ $object['tank'][1] }}" rx="7" ry="3.5" class="fill-ink-muted"/>
                <rect x="{{ $object['tank'][0] - 7 }}" y="{{ $object['tank'][1] - 10 }}" width="14" height="10" class="fill-ink-muted"/>
                <ellipse cx="{{ $object['tank'][0] }}" cy="{{ $object['tank'][1] - 10 }}" rx="7" ry="3.5" class="fill-ink-subtle"/>
            @endif
        @endif
    @endforeach

    @foreach ($lamps as $lamp)
        <path d="{{ $lamp['pole'] }}" class="stroke-ink-subtle" stroke-width="1.8" stroke-linecap="round"/>
        <circle cx="{{ $lamp['light'][0] }}" cy="{{ $lamp['light'][1] }}" r="2" class="fill-slate-300 dark:hidden"/>
        <circle cx="{{ $lamp['light'][0] }}" cy="{{ $lamp['light'][1] }}" r="9" fill="url(#neighborhood-lamp)" class="hidden animate-light-on motion-reduce:animate-none dark:inline {{ $lamp['delay'] }}"/>
        <circle cx="{{ $lamp['light'][0] }}" cy="{{ $lamp['light'][1] }}" r="2" class="fill-sun hidden dark:inline"/>
    @endforeach
</x-scene.frame>
