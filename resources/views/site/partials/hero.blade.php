@php
    $words = preg_split('/\s+/u', trim((string) $settings->localized('hero_tagline')));
@endphp

<section class="hero relative isolate flex min-h-[calc(100svh-73px)] flex-col">
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-linear-to-b from-brand-100/70 via-brand-50/50 to-bg dark:from-brand-950/70 dark:via-brand-950/30"></div>
        <div class="hero-dots absolute inset-0"></div>
    </div>

    <div class="mx-auto grid w-full max-w-[1280px] flex-1 content-center items-center gap-6 px-5 pt-10 pb-12 sm:px-8 lg:grid-cols-[1fr_1.15fr] lg:pt-6">
        <div class="hero-leave text-center lg:text-start">
            <h1 class="text-[38px] leading-[1.3] font-extrabold tracking-tight text-brand-900 sm:text-[52px] xl:text-[60px] dark:text-white">
                @foreach ($words as $word)
                    <span class="hero-word inline-block overflow-hidden pb-1 align-bottom"><span class="inline-block">{{ $word }}</span></span>
                @endforeach
            </h1>

            <p class="hero-fade mx-auto mt-5 max-w-md text-lg leading-8 text-ink-muted lg:mx-0">{{ $settings->localized('hero_subtitle') }}</p>

            <div class="hero-fade hero-fade-late mt-8 flex flex-wrap justify-center gap-3 lg:justify-start">
                <a href="{{ route('projects.index') }}" class="group relative inline-flex h-14 items-center gap-4 overflow-hidden rounded-2xl bg-linear-to-l from-brand-500 to-brand-700 ps-6 pe-2 text-base font-bold text-white shadow-[0_18px_36px_-14px_rgb(21_104_201/0.7)] ring-1 ring-white/15 ring-inset transition duration-300 ease-out-soft hover:-translate-y-0.5 hover:shadow-[0_24px_44px_-14px_rgb(21_104_201/0.8)] dark:shadow-none">
                    <span class="pointer-events-none absolute inset-x-0 top-0 h-1/2 bg-linear-to-b from-white/20 to-transparent"></span>
                    <span class="pointer-events-none absolute inset-y-0 -start-1/2 w-1/3 -skew-x-12 bg-white/25 transition-all duration-700 ease-out-soft group-hover:start-[130%] motion-reduce:hidden"></span>
                    <span class="relative">{{ __('site.hero.support') }}</span>
                    <span class="relative flex size-10 items-center justify-center rounded-xl bg-white/15 transition duration-300 group-hover:bg-white group-hover:text-brand-700">
                        <x-ui.icon name="arrow-left" :size="18" class="transition duration-300 group-hover:-translate-x-0.5 ltr:group-hover:translate-x-0.5" />
                    </span>
                </a>

                <a href="{{ route('volunteer.create') }}" class="group inline-flex h-14 items-center gap-3 rounded-2xl border border-brand-200/80 bg-white/75 ps-2 pe-6 text-base font-bold text-brand-900 shadow-card backdrop-blur transition duration-300 ease-out-soft hover:-translate-y-0.5 hover:border-brand-300 hover:bg-white hover:shadow-float dark:border-line dark:bg-surface/70 dark:text-ink">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-rose-50 text-rose-500 transition duration-300 group-hover:bg-rose-500 group-hover:text-white dark:bg-rose-500/15 dark:text-rose-300">
                        <x-ui.icon name="heart-handshake" :size="18" />
                    </span>
                    {{ __('site.hero.volunteer') }}
                </a>
            </div>

            <div class="hero-fade hero-fade-last mx-auto mt-9 max-w-xl lg:mx-0">
                @include('site.partials.impact-bar')
            </div>
        </div>

        <div class="hero-leave">
            <x-scene.solar class="hero-stage mx-auto max-w-[600px]" />
        </div>
    </div>

    <span class="hero-line pointer-events-none absolute inset-x-0 bottom-0 block h-px bg-linear-to-l from-transparent via-brand-400 to-transparent dark:via-brand-400/70" aria-hidden="true"></span>
</section>
