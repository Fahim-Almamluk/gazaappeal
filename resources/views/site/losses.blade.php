@php
    $states = [
        'danger' => ['dot' => 'bg-danger-solid', 'text' => 'text-danger'],
        'warning' => ['dot' => 'bg-warning', 'text' => 'text-warning'],
    ];
    $impacts = [
        'water' => ['icon' => 'droplets', 'tone' => 'bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-300'],
        'health' => ['icon' => 'heart-pulse', 'tone' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300'],
        'sanitation' => ['icon' => 'waves', 'tone' => 'bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-300'],
        'education' => ['icon' => 'school', 'tone' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300'],
    ];
@endphp

<x-layouts.site :title="__('site.losses.title')" :description="__('site.losses_intro.body')">
    <x-site.page-header :title="__('site.losses.title')" :subtitle="__('site.losses.subtitle')" />

    <section class="mx-auto mt-8 w-full max-w-[1280px] px-5 sm:px-8">
        <div class="reveal overflow-hidden rounded-[28px] border border-line bg-surface shadow-elevated">
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 pt-7 sm:px-9 sm:pt-9">
                <div>
                    <p class="text-sm font-bold text-primary">{{ __('site.losses.overview.eyebrow') }}</p>
                    <h2 class="mt-1 text-2xl font-extrabold text-ink sm:text-[30px]">{{ __('site.losses.overview.title') }}</h2>
                </div>
                <span class="inline-flex items-center gap-2 rounded-full bg-danger-soft py-1.5 ps-2.5 pe-3.5 text-xs font-bold text-danger">
                    <span class="relative flex size-2.5 items-center justify-center">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-danger-solid/60 motion-reduce:animate-none"></span>
                        <span class="relative size-2 rounded-full bg-danger-solid"></span>
                    </span>
                    {{ __('site.damage.status') }}
                </span>
            </div>

            @if ($losses->isEmpty())
                <x-ui.empty-state icon="zap-off" :title="__('site.losses.empty')" />
            @else
                <ul class="reveal-stagger grid gap-4 p-5 sm:grid-cols-2 sm:p-7 lg:grid-cols-4">
                    @foreach ($losses as $loss)
                        <li class="reveal group relative overflow-hidden rounded-2xl border border-line bg-surface-2/60 p-5 transition hover:-translate-y-0.5 hover:border-primary/30 hover:bg-surface hover:shadow-overlay sm:p-6">
                            <span class="absolute inset-x-0 top-0 h-1 bg-linear-to-l from-brand-600 to-brand-300"></span>
                            <span class="flex size-10 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink">
                                <x-ui.icon :name="$loss->icon->value" :size="19" />
                            </span>
                            <p class="mt-6 flex items-baseline gap-2">
                                <span class="tabular text-[40px] leading-none font-extrabold text-ink"><span data-count>{{ $loss->formattedFigure() }}</span></span>
                                <span class="text-sm font-bold text-primary">{{ $loss->unit }}</span>
                            </p>
                            <p class="mt-3 text-sm font-semibold text-ink-muted">{{ $loss->title }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif

            <ul class="flex flex-wrap gap-x-8 gap-y-3 border-t border-line bg-surface-2/50 px-6 py-5 text-sm sm:px-9">
                @foreach (__('site.losses.status.items') as $item)
                    <li class="flex items-center gap-2">
                        <span class="size-1.5 rounded-full {{ $states[$item['tone']]['dot'] }}"></span>
                        <span class="text-ink-muted">{{ $item['label'] }}</span>
                        <span class="font-bold {{ $states[$item['tone']]['text'] }}">{{ $item['state'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="mx-auto mt-20 grid w-full max-w-[1280px] gap-14 px-5 sm:px-8 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] lg:gap-16">
        <div class="reveal">
            <p class="text-sm font-bold text-primary">{{ __('site.losses.timeline.eyebrow') }}</p>
            <h2 class="mt-2 text-[26px] leading-tight font-extrabold text-ink sm:text-[30px]">{{ __('site.losses.timeline.title') }}</h2>

            <ol class="mt-8">
                @foreach (__('site.losses.timeline.items') as $item)
                    <li class="relative flex gap-4 pb-7 last:pb-0">
                        @unless ($loop->last)
                            <span class="absolute top-6 bottom-0 start-[9px] w-0.5 bg-line" aria-hidden="true"></span>
                        @endunless
                        <span @class([
                            'relative mt-1 size-5 shrink-0 rounded-full border-4 border-bg ring-2',
                            'bg-danger-solid ring-danger-solid/25' => ! $loop->last,
                            'bg-success ring-success/25' => $loop->last,
                        ])></span>
                        <div class="flex flex-1 flex-wrap items-baseline justify-between gap-x-4 gap-y-1 rounded-2xl border border-line bg-surface px-5 py-4 shadow-elevated">
                            <p class="font-extrabold text-ink">{{ $item['title'] }}</p>
                            <p @class(['text-xs font-bold', 'text-danger' => ! $loop->last, 'text-success' => $loop->last])>{{ $item['when'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>

        <div class="reveal">
            <p class="text-sm font-bold text-primary">{{ __('site.losses.impact.eyebrow') }}</p>
            <h2 class="mt-2 text-[26px] leading-tight font-extrabold text-ink sm:text-[30px]">{{ __('site.losses.impact.title') }}</h2>

            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                @foreach (__('site.losses.impact.items') as $key => $item)
                    <article class="flex items-start gap-4 rounded-[22px] border border-line bg-surface p-5 shadow-elevated transition hover:-translate-y-0.5 hover:shadow-overlay">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl {{ $impacts[$key]['tone'] }}">
                            <x-ui.icon :name="$impacts[$key]['icon']" :size="22" />
                        </span>
                        <div class="min-w-0">
                            <h3 class="font-extrabold text-ink">{{ $item['title'] }}</h3>
                            <p class="mt-1 text-sm leading-6 text-ink-muted">{{ $item['body'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto mt-20 w-full max-w-[1280px] px-5 sm:px-8">
        <div class="reveal flex flex-col gap-6 rounded-[28px] border border-line bg-surface p-7 shadow-elevated sm:p-10 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-5">
                <span class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-brand-400 to-brand-700 text-white shadow-brand">
                    <x-ui.icon name="solar-panel" :size="26" />
                </span>
                <div>
                    <h2 class="text-xl font-extrabold text-ink sm:text-2xl">{{ __('site.losses.cta.title') }}</h2>
                    <p class="mt-1.5 text-ink-muted">{{ __('site.losses.cta.body') }}</p>
                </div>
            </div>
            <div class="flex shrink-0 flex-wrap gap-3">
                <x-ui.button :href="route('donate')" size="lg" icon="heart">{{ __('site.nav.donate') }}</x-ui.button>
                <x-ui.button variant="secondary" :href="route('projects.index')" size="lg" icon="solar-panel">{{ __('site.losses.cta.projects') }}</x-ui.button>
            </div>
        </div>
    </section>
</x-layouts.site>
