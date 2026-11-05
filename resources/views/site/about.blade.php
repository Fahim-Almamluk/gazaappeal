@inject('pages', 'App\Support\PageContent')

@php
    $principles = ['framework' => 'heart-handshake', 'water' => 'droplets', 'services' => 'solar-panel'];
    $cover = $gallery->first();
@endphp

<x-layouts.site :title="__('site.about.title')" :description="$pages->text('about.intro')">
    <x-site.page-header :title="__('site.about.title')" :subtitle="__('site.about.subtitle')" />

    <section @class(['mx-auto mt-14 grid w-full max-w-[1280px] gap-12 px-5 sm:px-8 lg:items-center', 'lg:grid-cols-2 lg:gap-16' => $cover])>
        <div @class(['reveal', 'mx-auto max-w-3xl text-center' => ! $cover])>
            <h2 class="text-[28px] leading-tight font-extrabold tracking-tight text-balance text-ink sm:text-[36px]">{{ $pages->text('about.who.title') }}</h2>
            <span @class(['mt-5 block h-1 w-16 rounded-full bg-primary', 'mx-auto' => ! $cover])></span>
            <p class="mt-6 text-[17px] leading-9 text-ink-muted">{{ $pages->text('about.intro') }}</p>

            <div @class(['mt-8 flex flex-wrap gap-3', 'justify-center' => ! $cover])>
                <x-ui.button :href="route('projects.index')" size="xl" icon="solar-panel">{{ __('site.hero.support') }}</x-ui.button>
                <x-ui.button :href="route('volunteer.create')" variant="secondary" size="xl" icon="heart-handshake">{{ __('site.hero.volunteer') }}</x-ui.button>
            </div>
        </div>

        @if ($cover)
            <figure class="reveal relative pb-6">
                <img src="{{ $cover->imageUrl() }}" alt="{{ $cover->caption }}" decoding="async" class="aspect-[4/3] w-full rounded-[28px] object-cover shadow-elevated">

                <figcaption class="absolute start-6 bottom-0 flex items-center gap-3 rounded-2xl border border-line bg-surface px-5 py-4 shadow-overlay">
                    <span class="flex size-11 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink">
                        <x-ui.icon name="solar-panel" :size="22" />
                    </span>
                    <span class="flex flex-col">
                        <span class="tabular text-2xl leading-none font-extrabold text-ink"><span data-count>{{ number_format($figures['projects']) }}</span></span>
                        <span class="mt-1 text-xs font-semibold text-ink-muted">{{ __('site.about.figures.projects') }}</span>
                    </span>
                </figcaption>
            </figure>
        @endif
    </section>

    <section class="mx-auto mt-20 w-full max-w-[1280px] px-5 sm:px-8">
        <dl class="reveal grid grid-cols-2 overflow-hidden rounded-card border border-line bg-surface shadow-elevated lg:grid-cols-4">
            @foreach ($figures as $key => $value)
                <div @class([
                    'flex flex-col-reverse items-center gap-2 px-4 py-7 text-center',
                    'border-line odd:border-e lg:border-e lg:last:border-e-0',
                    'border-b lg:border-b-0' => $loop->index < 2,
                ])>
                    <dt class="text-sm font-semibold text-ink-muted">{{ __("site.about.figures.{$key}") }}</dt>
                    <dd class="tabular text-[34px] leading-none font-extrabold text-ink"><span data-count>{{ number_format($value) }}</span></dd>
                </div>
            @endforeach
        </dl>
    </section>

    <x-site.band class="mt-24">
        <section class="mx-auto grid w-full max-w-[1280px] gap-5 px-5 sm:px-8 md:grid-cols-2">
            @foreach (['vision' => 'target', 'mission' => 'flag'] as $key => $icon)
                <article class="reveal relative overflow-hidden rounded-card border border-line bg-bg p-8 sm:p-10">
                    <span class="flex size-12 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink">
                        <x-ui.icon :name="$icon" :size="22" />
                    </span>
                    <h3 class="mt-6 text-[22px] font-extrabold text-ink">{{ __("site.about.{$key}.title") }}</h3>
                    <p class="mt-3 text-[16px] leading-8 text-ink-muted">{{ $pages->text("about.{$key}.body") }}</p>
                </article>
            @endforeach
        </section>
    </x-site.band>

    <section class="mx-auto mt-24 w-full max-w-[1280px] px-5 sm:px-8">
        <x-site.section-title class="reveal" :title="__('site.about.highlights.title')" />

        <div class="reveal-stagger mt-12 grid gap-5 md:grid-cols-3">
            @foreach ($principles as $key => $icon)
                <article class="reveal group relative overflow-hidden rounded-card border border-line bg-surface p-7 shadow-elevated transition duration-200 hover:border-primary/40">
                    <span class="tabular pointer-events-none absolute end-6 top-5 text-[44px] leading-none font-extrabold text-line-strong/60" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="flex size-12 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink transition group-hover:bg-primary group-hover:text-primary-fg">
                        <x-ui.icon :name="$icon" :size="22" />
                    </span>
                    <h3 class="mt-6 text-lg font-bold text-ink">{{ $pages->text("about.highlights.items.{$key}.title") }}</h3>
                    <p class="mt-2 text-sm leading-7 text-ink-muted">{{ $pages->text("about.highlights.items.{$key}.body") }}</p>
                </article>
            @endforeach
        </div>
    </section>

    @if ($gallery->isNotEmpty())
        <x-site.band class="mt-24">
            <section class="mx-auto w-full max-w-[1280px] px-5 sm:px-8">
                <x-site.section-title class="reveal" :title="__('site.about.gallery.title')" />

                <x-site.gallery :items="$gallery" class="mt-12" />

                <div class="mt-10 flex justify-center">
                    <x-ui.button variant="secondary" :href="route('gallery')" icon="arrow-left" class="flex-row-reverse">{{ __('site.gallery_section.all') }}</x-ui.button>
                </div>
            </section>
        </x-site.band>
    @endif
</x-layouts.site>
