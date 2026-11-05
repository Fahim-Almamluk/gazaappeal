@props(['text'])

<div {{ $attributes->class('space-y-5 text-[17px] leading-9 text-ink-muted') }}>
    @foreach (preg_split('/\R{2,}/u', trim($text)) as $paragraph)
        <p>{!! nl2br(e($paragraph)) !!}</p>
    @endforeach
</div>
