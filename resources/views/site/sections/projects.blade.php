@if ($projects->isNotEmpty())
    <x-site.band>
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
