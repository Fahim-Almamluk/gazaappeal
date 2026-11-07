@if ($gallery->isNotEmpty())
    <x-site.band>
        <section class="mx-auto w-full max-w-[1280px] px-5 sm:px-8">
            <x-site.section-title class="reveal" :title="__('site.gallery_section.title')" :subtitle="__('site.gallery_section.subtitle')" />

            <x-site.gallery :items="$gallery" class="mt-12" />

            <div class="mt-10 flex justify-center">
                <x-ui.button variant="secondary" :href="route('gallery')" icon="arrow-left" class="flex-row-reverse">{{ __('site.gallery_section.all') }}</x-ui.button>
            </div>
        </section>
    </x-site.band>
@endif
