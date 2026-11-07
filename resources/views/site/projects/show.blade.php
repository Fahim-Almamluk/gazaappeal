@php
    $progress = $project->progress();
@endphp

<x-layouts.site :title="$project->title" :description="$project->summary">
    <x-site.page-header :title="$project->title" :subtitle="$project->summary" :parent="__('site.projects.title')" :parent-url="route('projects.index')">
        <div class="flex flex-wrap gap-2">
            @if ($project->is_urgent)
                <x-ui.badge tone="danger" icon="zap">{{ __('site.projects.urgent') }}</x-ui.badge>
            @endif
            <x-ui.lookup-badge :lookup="$project->status" />
        </div>
    </x-site.page-header>

    <section class="mx-auto mt-10 grid w-full max-w-[1280px] gap-8 px-5 sm:px-8 lg:grid-cols-[minmax(0,1fr)_380px] lg:items-start">
        <x-site.reading-card :text="$project->body" :image="$project->imageUrl()" :alt="$project->title" icon="solar-panel" />

        <aside class="space-y-5 lg:sticky lg:top-24">
            <div class="relative isolate overflow-hidden rounded-card bg-linear-to-b from-brand-500 via-brand-600 to-brand-800 p-6 text-white">
                <div class="grain pointer-events-none absolute inset-0 -z-10 opacity-[0.15] mix-blend-overlay"></div>
                <p class="text-sm font-medium text-white/75">{{ __('site.projects.support_title') }}</p>

                @if ($progress !== null)
                    <p class="tabular mt-3 text-[34px] leading-none font-extrabold">{{ $progress }}%</p>
                    <x-ui.progress :value="$progress" tone="white" class="mt-4" />
                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <dt class="text-white/70">{{ __('site.projects.raised') }}</dt>
                            <dd class="tabular mt-0.5 font-bold">{{ \App\Support\Money::full($project->raised_amount) }}</dd>
                        </div>
                        <div>
                            <dt class="text-white/70">{{ __('site.projects.goal') }}</dt>
                            <dd class="tabular mt-0.5 font-bold">{{ \App\Support\Money::full($project->goal_amount) }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="mt-2 text-sm leading-7 text-white/85">{{ __('site.projects.support_body') }}</p>
                @endif

                <a href="{{ route('donate') }}" class="mt-6 inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-white text-[15px] font-bold text-brand-700 shadow-[0_14px_30px_-12px_rgb(0_0_0/0.45)] transition hover:-translate-y-0.5">
                    <x-ui.icon name="heart" :size="18" />
                    {{ __('site.projects.donate') }}
                </a>
            </div>

            <x-ui.card class="flex items-start gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink"><x-ui.icon name="heart-handshake" :size="19" /></span>
                <div>
                    <p class="font-bold text-ink">{{ __('site.projects.volunteer_title') }}</p>
                    <p class="mt-1 text-sm leading-6 text-ink-muted">{{ __('site.projects.volunteer_body') }}</p>
                    <a href="{{ route('volunteer.create') }}" class="mt-2 inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline">{{ __('site.hero.volunteer') }}<x-ui.icon name="arrow-left" :size="15" /></a>
                </div>
            </x-ui.card>
        </aside>
    </section>

    @if ($stories->isNotEmpty())
        <section class="mx-auto mt-20 w-full max-w-[1280px] space-y-8 px-5 sm:px-8">
            <x-site.section-heading class="reveal" :title="__('site.projects.stories')" />
            <div class="reveal-stagger grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($stories as $story)
                    <x-site.story-card class="reveal" :story="$story" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($related->isNotEmpty())
        <section class="mx-auto mt-20 w-full max-w-[1280px] space-y-8 px-5 sm:px-8">
            <x-site.section-heading class="reveal" :title="__('site.projects.related')">
                <x-slot:action>
                    <x-ui.button variant="secondary" :href="route('projects.index')" icon="arrow-left" class="flex-row-reverse">{{ __('site.projects_section.all') }}</x-ui.button>
                </x-slot:action>
            </x-site.section-heading>
            <div class="reveal-stagger grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($related as $item)
                    <x-site.project-card class="reveal" :project="$item" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.site>
