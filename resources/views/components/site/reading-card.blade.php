@props([
    'text',
    'image' => null,
    'alt' => '',
    'icon' => 'award',
    'date' => null,
])

@php
    $paragraphs = preg_split('/\R{2,}/u', trim((string) $text));
    $minutes = max(1, (int) ceil(count(preg_split('/\s+/u', trim((string) $text), -1, PREG_SPLIT_NO_EMPTY)) / 180));
@endphp

<article {{ $attributes->class('overflow-hidden rounded-[28px] border border-line bg-surface shadow-elevated') }}>
    <x-site.cover :src="$image" :alt="$alt" :icon="$icon" class="aspect-[16/8]" />

    <div class="px-6 pt-9 pb-8 sm:px-10 sm:pt-12 sm:pb-10">
        <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-ink-subtle">
            @if ($date)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-soft px-3 py-1.5 text-primary-soft-ink">
                    <x-ui.icon name="calendar-days" :size="14" />
                    <time datetime="{{ $date->toDateString() }}">{{ $date->translatedFormat('j F Y') }}</time>
                </span>
            @endif
            <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-2 px-3 py-1.5">
                <x-ui.icon name="clock" :size="14" />
                {{ __('site.reading.minutes', ['count' => $minutes]) }}
            </span>
        </div>

        <div class="relative mt-7 overflow-hidden rounded-2xl border border-primary/15 bg-primary-soft/50 px-6 py-6 sm:px-8 sm:py-7">
            <span class="absolute inset-y-0 start-0 w-1.5 bg-linear-to-b from-brand-400 to-brand-700"></span>
            <x-ui.icon name="quote" :size="26" class="mb-3 text-primary/40" />
            <div class="space-y-5 text-[15.5px] leading-[2.1] text-ink [overflow-wrap:anywhere]">
                @foreach ($paragraphs as $paragraph)
                    <p>{!! nl2br(e($paragraph)) !!}</p>
                @endforeach
            </div>
        </div>

        <div class="mt-10 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-6">
            <span class="flex items-center gap-2 text-sm font-semibold text-ink-subtle">
                <span class="h-1 w-8 rounded-full bg-primary"></span>
                {{ __('ui.brand.name') }}
            </span>

            <span x-data="copyText" data-text="{{ url()->current() }}" data-copy="{{ __('site.reading.share') }}" data-copied="{{ __('site.donate.copied') }}">
                <button type="button" x-on:click="copy" class="inline-flex h-10 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-semibold text-ink transition hover:border-primary hover:text-primary">
                    <span x-show="idle"><x-ui.icon name="link" :size="16" /></span>
                    <span x-show="copied" x-cloak class="text-success"><x-ui.icon name="check" :size="16" /></span>
                    <span x-text="label">{{ __('site.reading.share') }}</span>
                </button>
            </span>
        </div>
    </div>
</article>
