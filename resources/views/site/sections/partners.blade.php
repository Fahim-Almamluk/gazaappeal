@if ($partners->isNotEmpty())
    <section class="mx-auto w-full max-w-[1280px] px-5 sm:px-8">
        <x-site.section-title class="reveal" :title="__('site.partners_section.title')" :subtitle="__('site.partners_section.subtitle')" />

        <x-site.partners :partners="$partners" class="mt-12" />
    </section>
@endif
