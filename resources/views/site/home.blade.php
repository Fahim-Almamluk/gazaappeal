@inject('settings', 'App\Support\SiteSettings')

@php
    $impact = [
        ['value' => $settings->get('impact_people'), 'label' => __('site.hero.stats.people')],
        ['value' => $settings->get('impact_duration'), 'label' => __('site.hero.stats.duration')],
        ['value' => number_format($projectsCount), 'label' => __('site.hero.stats.projects')],
    ];
@endphp

<x-layouts.site>
    @include('site.partials.hero')

    @if ($projects->isNotEmpty())
        <x-site.band class="border-t-0">
            <section class="mx-auto w-full max-w-[1280px] px-5 sm:px-8">
                <x-site.section-title class="reveal" :title="__('site.projects_section.title')" :subtitle="__('site.projects_section.subtitle')" />

                <div class="reveal-stagger mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <x-site.project-card class="reveal" :project="$project" />
                    @endforeach
                </div>

                <div class="mt-10 flex justify-center">
                    <x-ui.button variant="secondary" :href="route('projects.index')" icon="arrow-left" class="flex-row-reverse">{{ __('site.projects_section.all') }}</x-ui.button>
                </div>
            </section>
        </x-site.band>
    @endif

    <x-site.damage-map :damages="$damages" class="mt-24" />

    <section class="mx-auto mt-24 w-full max-w-[1280px] px-5 sm:px-8">
        <div class="reveal-pop relative isolate overflow-clip rounded-[32px] bg-linear-to-l from-brand-700 via-brand-600 to-brand-500 p-8 text-white sm:p-14">
            <div class="scroll-drift pointer-events-none absolute -top-24 -end-10 -z-10 size-80 rounded-full bg-white/15 blur-3xl"></div>
            <div class="grain pointer-events-none absolute inset-0 -z-10 opacity-[0.15] mix-blend-overlay"></div>

            <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl space-y-3">
                    <h2 class="text-[30px] leading-tight font-extrabold tracking-tight sm:text-[38px]">{{ __('site.volunteer_cta.title') }}</h2>
                    <p class="text-lg leading-8 text-white/80">{{ __('site.volunteer_cta.body') }}</p>
                </div>

                <a href="{{ route('volunteer.create') }}" class="inline-flex h-[52px] shrink-0 items-center justify-center gap-2 self-start rounded-2xl bg-white px-7 text-base font-bold text-brand-700 shadow-[0_14px_30px_-12px_rgb(0_0_0/0.45)] transition hover:-translate-y-0.5 lg:self-center">
                    <x-ui.icon name="heart-handshake" :size="20" />
                    {{ __('site.volunteer_cta.action') }}
                </a>
            </div>
        </div>
    </section>

    @if ($methods->isNotEmpty())
        <x-site.band class="mt-24">
            <x-site.giving :methods="$methods" />
        </x-site.band>
    @endif

    <section class="mx-auto mt-24 w-full max-w-[1280px] px-5 sm:px-8">
        <x-site.section-title class="reveal" :title="__('site.stories_section.title')" :subtitle="__('site.stories_section.subtitle')" />

        @if ($stories->isNotEmpty())
            <div class="reveal-stagger mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($stories as $story)
                    <x-site.story-card class="reveal" :story="$story" />
                @endforeach
            </div>

            <div class="mt-10 flex justify-center">
                <x-ui.button variant="secondary" :href="route('stories.index')" icon="arrow-left" class="flex-row-reverse">{{ __('site.stories_section.all') }}</x-ui.button>
            </div>
        @else
            <x-ui.card class="reveal mt-12">
                <x-ui.empty-state icon="award" :title="__('site.stories_section.empty')" :body="__('site.stories_section.empty_body')" />
            </x-ui.card>
        @endif
    </section>

    @if ($gallery->isNotEmpty())
        <x-site.band class="mt-24">
            <section class="mx-auto w-full max-w-[1280px] px-5 sm:px-8">
                <x-site.section-title class="reveal" :title="__('site.gallery_section.title')" :subtitle="__('site.gallery_section.subtitle')" />

                <x-site.gallery :items="$gallery" class="mt-12" />

                <div class="mt-10 flex justify-center">
                    <x-ui.button variant="secondary" :href="route('gallery')" icon="arrow-left" class="flex-row-reverse">{{ __('site.gallery_section.all') }}</x-ui.button>
                </div>
            </section>
        </x-site.band>
    @endif

    @if ($partners->isNotEmpty())
        <section class="mx-auto mt-24 w-full max-w-[1280px] px-5 sm:px-8">
            <x-site.section-title class="reveal" :title="__('site.partners_section.title')" :subtitle="__('site.partners_section.subtitle')" />

            <x-site.partners :partners="$partners" class="mt-12" />
        </section>
    @endif
</x-layouts.site>
