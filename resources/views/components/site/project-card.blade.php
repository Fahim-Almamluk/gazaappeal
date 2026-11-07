@props(['project'])

@php
    $progress = $project->progress();
@endphp

<article {{ $attributes->class('group relative flex flex-col overflow-hidden rounded-card border border-line bg-surface shadow-elevated transition duration-200 hover:-translate-y-1 hover:border-primary/40 hover:shadow-overlay') }}>
    <x-site.cover :src="$project->imageUrl()" :alt="$project->title" icon="solar-panel" class="aspect-[16/10]" />

    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center gap-2">
            @if ($project->is_urgent)
                <x-ui.badge tone="danger" icon="zap">{{ __('site.projects.urgent') }}</x-ui.badge>
            @endif
            <x-ui.lookup-badge :lookup="$project->status" />
        </div>

        <h3 class="mt-3 text-lg leading-7 font-bold text-ink">
            <a href="{{ route('projects.show', $project) }}" class="after:absolute after:inset-0 focus-visible:outline-none">{{ $project->title }}</a>
        </h3>
        <p class="mt-1.5 line-clamp-2 flex-1 text-sm leading-6 text-ink-muted">{{ $project->summary }}</p>

        @if ($progress !== null)
            <div class="mt-5 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-semibold text-ink-muted">{{ __('site.projects.raised') }}</span>
                    <span class="tabular font-bold text-primary">{{ $progress }}%</span>
                </div>
                <x-ui.progress :value="$progress" />
                <p class="tabular text-xs text-ink-subtle">{{ __('site.projects.of_goal', ['raised' => number_format((float) $project->raised_amount), 'goal' => number_format((float) $project->goal_amount)]) }}</p>
            </div>
        @endif

        <span class="mt-5 inline-flex items-center gap-1.5 text-sm font-bold text-primary">
            {{ __('site.projects.details') }}
            <x-ui.icon name="arrow-left" :size="16" class="transition group-hover:-translate-x-1 ltr:group-hover:translate-x-1" />
        </span>
    </div>
</article>
