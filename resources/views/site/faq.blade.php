<x-layouts.site :title="__('site.faq.title')" :description="__('site.faq.subtitle')">
    <x-site.page-header :title="__('site.faq.title')" :subtitle="__('site.faq.subtitle')" />

    <section class="mx-auto mt-10 w-full max-w-[900px] px-5 sm:px-8">
        @if ($faqs->isEmpty())
            <x-ui.card>
                <x-ui.empty-state icon="circle-help" :title="__('site.faq.empty')" />
            </x-ui.card>
        @else
            <div class="reveal-stagger space-y-3">
                @foreach ($faqs as $faq)
                    <details class="reveal group rounded-card border border-line bg-surface shadow-elevated open:border-primary/40" @if ($loop->first) open @endif>
                        <summary class="flex cursor-pointer list-none items-center gap-4 px-6 py-5 [&::-webkit-details-marker]:hidden">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary-soft-ink"><x-ui.icon name="circle-help" :size="18" /></span>
                            <span class="flex-1 font-bold text-ink">{{ $faq->question }}</span>
                            <x-ui.icon name="chevron-down" :size="18" class="text-ink-subtle transition-transform duration-200 group-open:rotate-180" />
                        </summary>
                        <div class="border-t border-line px-6 py-5 ps-[4.75rem]">
                            <x-site.prose :text="$faq->answer" class="text-[15px] leading-8" />
                        </div>
                    </details>
                @endforeach
            </div>
        @endif

        <div class="reveal mt-10 flex flex-col items-center gap-3 text-center">
            <p class="text-ink-muted">{{ __('site.faq.more') }}</p>
            <x-ui.button variant="soft" :href="route('contact.create')" icon="send">{{ __('site.nav.contact') }}</x-ui.button>
        </div>
    </section>
</x-layouts.site>
