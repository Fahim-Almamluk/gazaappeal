<x-layouts.site :title="__('site.gallery_section.title')" :description="__('site.gallery_section.subtitle')">
    <x-site.page-header :title="__('site.gallery_section.title')" :subtitle="__('site.gallery_section.subtitle')" />

    <section id="list" class="mx-auto mt-10 w-full max-w-[1280px] scroll-mt-24 px-5 sm:px-8">
        @if ($items->isEmpty())
            <x-ui.card>
                <x-ui.empty-state icon="images" :title="__('site.gallery_section.empty')" :body="__('site.gallery_section.empty_body')" />
            </x-ui.card>
        @else
            <x-site.gallery :items="$items" />

            <x-site.pagination :paginator="$items" />
        @endif
    </section>
</x-layouts.site>
