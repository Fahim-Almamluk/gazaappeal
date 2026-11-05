<x-layouts.site :title="$story->title" :description="$story->summary">
    <x-site.page-header :title="$story->title" :subtitle="$story->summary" :parent="__('site.stories.title')" :parent-url="route('stories.index')" />

    <article class="mx-auto mt-10 w-full max-w-[860px] space-y-6 px-5 sm:px-8">
        <x-site.reading-card :text="$story->body" :image="$story->imageUrl()" :alt="$story->title" :date="$story->created_at" />

        @if ($story->project)
            <a href="{{ route('projects.show', $story->project) }}" class="group flex items-center gap-4 rounded-card border border-line bg-surface p-5 shadow-elevated transition hover:border-primary/40">
                <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink"><x-ui.icon name="solar-panel" :size="22" /></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-xs font-semibold text-ink-subtle">{{ __('site.stories.project') }}</span>
                    <span class="block truncate font-bold text-ink">{{ $story->project->title }}</span>
                </span>
                <x-ui.icon name="arrow-left" :size="18" class="text-primary transition group-hover:-translate-x-1 ltr:group-hover:translate-x-1" />
            </a>
        @endif
    </article>
</x-layouts.site>
