<x-layouts.site :title="__('site.projects.title')" :description="__('site.projects_section.subtitle')">
    <x-site.page-header :title="__('site.projects.title')" :subtitle="__('site.projects_section.subtitle')" />

    <section id="list" class="mx-auto mt-10 w-full max-w-[1280px] scroll-mt-24 px-5 sm:px-8">
        @if ($projects->isEmpty())
            <x-ui.card>
                <x-ui.empty-state icon="solar-panel" :title="__('site.projects.empty')" />
            </x-ui.card>
        @else
            <div class="reveal-stagger grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <x-site.project-card class="reveal" :project="$project" />
                @endforeach
            </div>

            <x-site.pagination :paginator="$projects" />
        @endif
    </section>
</x-layouts.site>
