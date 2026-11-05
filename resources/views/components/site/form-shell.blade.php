@props([
    'title',
    'body' => null,
])

<section {{ $attributes->class('mx-auto w-full max-w-[1280px] px-5 sm:px-8') }}>
    <div class="reveal grid overflow-hidden rounded-[28px] border border-line bg-surface shadow-elevated lg:grid-cols-[minmax(0,380px)_minmax(0,1fr)]">
        <aside class="flex flex-col border-b border-line bg-surface-2/60 p-7 sm:p-9 lg:border-e lg:border-b-0">
            <h2 class="text-xl leading-tight font-extrabold text-ink">{{ $title }}</h2>
            @if ($body)
                <p class="mt-2 text-sm leading-7 text-ink-muted">{{ $body }}</p>
            @endif

            <div class="mt-7 flex-1">{{ $aside }}</div>
        </aside>

        <div class="p-6 sm:p-9">{{ $slot }}</div>
    </div>
</section>
