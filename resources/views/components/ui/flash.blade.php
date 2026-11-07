@php
    $texts = collect([
        'error' => session('error'),
        'warning' => session('warning'),
        'success' => session('success') ?? session('status'),
        'info' => session('info'),
    ])->filter(fn (mixed $text): bool => filled($text));

    $message = $texts->isEmpty() ? null : ['type' => $texts->keys()->first(), 'text' => (string) $texts->first()];

    $fields = collect($errors->getBags())
        ->flatMap(fn ($bag): array => collect($bag->messages())->map(fn (array $messages, string $field): array => ['field' => $field, 'text' => $messages[0]])->values()->all())
        ->unique('text')
        ->values();

    $payload = match (true) {
        $fields->isNotEmpty() => ['type' => 'error', 'title' => __('ui.flash.invalid'), 'details' => $fields->all()],
        $message !== null => ['type' => $message['type'], 'title' => $message['text'], 'details' => []],
        default => null,
    };
@endphp

@if ($payload)
    <script type="application/json" data-flash>{!! json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endif
