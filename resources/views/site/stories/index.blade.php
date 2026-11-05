<x-layouts.site :title="__('site.stories.title')" :description="__('site.stories.subtitle')">
    <x-site.page-header :title="__('site.stories.title')" :subtitle="__('site.stories.subtitle')" />

    <section id="list" class="mx-auto mt-10 w-full max-w-[1280px] scroll-mt-24 px-5 sm:px-8">
        @if ($stories->isEmpty())
            <x-ui.card>
                <x-ui.empty-state icon="award" :title="__('site.stories_section.empty')" :body="__('site.stories_section.empty_body')" />
            </x-ui.card>
        @else
            <div class="reveal-stagger grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($stories as $story)
                    <x-site.story-card class="reveal" :story="$story" />
                @endforeach
            </div>

            <x-site.pagination :paginator="$stories" />
        @endif
    </section>
</x-layouts.site>
