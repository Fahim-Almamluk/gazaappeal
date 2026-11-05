<?php

$item = fn (string $key, string $label, int $max, int $number = 0, bool $multiline = false): array => [
    'key' => $key,
    'label' => $label,
    'max' => $max,
    'number' => $number,
    'multiline' => $multiline,
];

$items = function (string $prefix, array $names, array $fields) use ($item): array {
    $result = [];

    foreach (array_values($names) as $index => $name) {
        foreach ($fields as $field => [$label, $max, $multiline]) {
            $result[str_replace('.', '_', "{$prefix}.{$name}.{$field}")] = $item("{$prefix}.items.{$name}.{$field}", $label, $max, $index + 1, $multiline);
        }
    }

    return $result;
};

return [
    'about' => [
        'who' => [
            'about_who_title' => $item('about.who.title', 'title', 120),
            'about_intro' => $item('about.intro', 'body', 700, multiline: true),
        ],
        'vision' => [
            'about_vision_body' => $item('about.vision.body', 'body', 300, multiline: true),
        ],
        'mission' => [
            'about_mission_body' => $item('about.mission.body', 'body', 300, multiline: true),
        ],
        'framework' => $items('about.highlights', ['framework', 'water', 'services'], [
            'title' => ['title', 120, false],
            'body' => ['body', 400, true],
        ]),
    ],

    'losses' => [
        'status' => $items('losses.status', ['plant', 'feeders', 'network', 'alternatives'], [
            'label' => ['label', 60, false],
            'state' => ['state', 60, false],
        ]),
        'timeline' => $items('losses.timeline', ['before', 'outbreak', 'collapse', 'now'], [
            'when' => ['when', 40, false],
            'title' => ['title', 120, false],
        ]),
        'impact' => $items('losses.impact', ['water', 'health', 'sanitation', 'education'], [
            'title' => ['title', 60, false],
            'body' => ['body', 200, true],
        ]),
        'cta' => [
            'losses_cta_title' => $item('losses.cta.title', 'title', 120),
            'losses_cta_body' => $item('losses.cta.body', 'body', 200, multiline: true),
        ],
    ],
];
