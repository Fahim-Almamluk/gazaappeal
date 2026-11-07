<section class="mx-auto w-full max-w-[1280px] px-5 sm:px-8">
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
