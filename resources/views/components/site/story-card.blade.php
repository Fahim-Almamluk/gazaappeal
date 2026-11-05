@props(['story'])

<article {{ $attributes->class('group relative flex flex-col overflow-hidden rounded-card border border-line bg-surface shadow-elevated transition duration-200 hover:-translate-y-1 hover:border-primary/40 hover:shadow-overlay') }}>
    <x-site.cover :src="$story->imageUrl()" :alt="$story->title" icon="award" class="aspect-[16/10]" />

    <div class="flex flex-1 flex-col p-5">
        <time datetime="{{ $story->created_at->toDateString() }}" class="flex items-center gap-1.5 text-xs text-ink-subtle">
            <x-ui.icon name="calendar-days" :size="14" />
            {{ $story->created_at->translatedFormat('j F Y') }}
        </time>
        <h3 class="mt-2 text-lg leading-7 font-bold text-ink">
            <a href="{{ route('stories.show', $story) }}" class="after:absolute after:inset-0 focus-visible:outline-none">{{ $story->title }}</a>
        </h3>
        <p class="mt-1.5 line-clamp-3 flex-1 text-sm leading-6 text-ink-muted">{{ $story->summary }}</p>
    </div>
</article>
