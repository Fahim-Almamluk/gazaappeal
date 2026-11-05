@php
    $principles = ['framework' => 'heart-handshake', 'water' => 'droplets', 'services' => 'solar-panel'];
    $figureIcons = ['projects' => 'solar-panel', 'volunteers' => 'heart-handshake', 'partners' => 'handshake', 'stories' => 'award'];
@endphp

<x-layouts.site :title="__('site.about.title')" :description="__('site.about.intro')">
    <x-site.page-header :title="__('site.about.title')" :subtitle="__('site.about.subtitle')" />

    <section class="mx-auto mt-10 grid w-full max-w-[1280px] gap-10 px-5 sm:px-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-start">
        <div class="reveal space-y-5">
            <x-site.section-heading :eyebrow="__('site.about.who.eyebrow')" :title="__('site.about.who.title')" />
            <p class="text-[17px] leading-9 text-ink-muted">{{ __('site.about.intro') }}</p>
        </div>

        <div class="reveal-stagger grid gap-4">
            @foreach (['vision' => 'target', 'mission' => 'flag'] as $key => $icon)
                <article class="reveal flex gap-4 rounded-card border border-t-[3px] border-line border-t-primary bg-surface p-6 shadow-elevated">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink"><x-ui.icon :name="$icon" :size="22" /></span>
                    <div>
                        <h3 class="text-lg font-bold text-ink">{{ __("site.about.{$key}.title") }}</h3>
                        <p class="mt-1.5 text-sm leading-7 text-ink-muted">{{ __("site.about.{$key}.body") }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="mx-auto mt-24 w-full max-w-[1280px] space-y-8 px-5 sm:px-8">
        <x-site.section-heading class="reveal" :eyebrow="__('site.about.highlights.eyebrow')" :title="__('site.about.highlights.title')" />

        <div class="reveal-stagger grid gap-5 md:grid-cols-3">
            @foreach ($principles as $key => $icon)
                <article class="reveal group rounded-card border border-line bg-surface p-6 transition duration-200 hover:-translate-y-0.5 hover:border-primary/40 hover:shadow-overlay">
                    <span class="flex size-12 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink transition group-hover:bg-primary group-hover:text-primary-fg">
                        <x-ui.icon :name="$icon" :size="22" />
                    </span>
                    <h3 class="mt-5 text-lg font-bold text-ink">{{ __("site.about.highlights.items.{$key}.title") }}</h3>
                    <p class="mt-2 text-sm leading-7 text-ink-muted">{{ __("site.about.highlights.items.{$key}.body") }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="mx-auto mt-24 w-full max-w-[1280px] px-5 sm:px-8">
        <dl class="reveal-pop grid grid-cols-2 gap-px overflow-hidden rounded-card border border-line bg-line shadow-elevated lg:grid-cols-4">
            @foreach ($figures as $key => $value)
                <div class="flex items-center gap-4 bg-surface p-6">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink"><x-ui.icon :name="$figureIcons[$key]" :size="22" /></span>
                    <div class="flex flex-col-reverse gap-1.5">
                        <dt class="text-sm font-semibold text-ink-muted">{{ __("site.about.figures.{$key}") }}</dt>
                        <dd class="tabular text-[30px] leading-none font-extrabold text-ink"><span data-count>{{ number_format($value) }}</span></dd>
                    </div>
                </div>
            @endforeach
        </dl>
    </section>

    @if ($gallery->isNotEmpty())
        <section class="mx-auto mt-24 w-full max-w-[1280px] space-y-8 px-5 sm:px-8">
            <x-site.section-heading class="reveal" :eyebrow="__('site.about.gallery.eyebrow')" :title="__('site.about.gallery.title')" />

            <div class="reveal-stagger grid grid-cols-2 gap-4 lg:grid-cols-4">
                @foreach ($gallery as $item)
                    <figure class="reveal group overflow-hidden rounded-card border border-line bg-surface shadow-elevated">
                        <div class="aspect-square overflow-hidden">
                            <img src="{{ $item->imageUrl() }}" alt="{{ $item->caption }}" loading="lazy" decoding="async" class="size-full object-cover transition duration-500 group-hover:scale-105">
                        </div>
                        <figcaption class="px-4 py-3 text-sm font-semibold text-ink-muted">{{ $item->caption }}</figcaption>
                    </figure>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.site>
