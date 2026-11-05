@php
    $words = preg_split('/\s+/u', trim((string) $settings->localized('hero_tagline')));
    $accent = count($words) > 1 ? array_pop($words) : null;
@endphp

<section class="hero relative isolate flex min-h-[calc(100svh-73px)] flex-col">
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-linear-to-b from-brand-100/70 via-brand-50/50 to-bg dark:from-brand-950/70 dark:via-brand-950/30"></div>
        <div class="hero-dots absolute inset-0"></div>
    </div>

    <div class="mx-auto grid w-full max-w-[1280px] flex-1 content-center items-center gap-6 px-5 pt-10 pb-12 sm:px-8 lg:grid-cols-[1fr_1.15fr] lg:pt-6">
        <div class="hero-leave text-center lg:text-start">
            <h1 class="text-[38px] leading-[1.3] font-extrabold tracking-tight text-ink sm:text-[52px] xl:text-[60px]">
                @foreach ($words as $word)
                    <span class="hero-word inline-block overflow-hidden pb-1 align-bottom"><span class="inline-block">{{ $word }}</span></span>
                @endforeach
                @if ($accent)
                    <span class="hero-word inline-block overflow-hidden pb-1 align-bottom"><span class="inline-block"><span class="animate-shimmer bg-linear-to-l from-brand-700 via-brand-400 to-brand-700 bg-[length:200%_auto] bg-clip-text text-transparent motion-reduce:animate-none dark:from-brand-300 dark:via-brand-100 dark:to-brand-300">{{ $accent }}</span></span></span>
                @endif
            </h1>

            <p class="hero-fade mx-auto mt-5 max-w-md text-lg leading-8 text-ink-muted lg:mx-0">{{ $settings->localized('hero_subtitle') }}</p>

            <div class="hero-fade hero-fade-late mt-8 flex flex-wrap justify-center gap-3 lg:justify-start">
                <x-ui.button :href="route('projects.index')" size="xl" icon="solar-panel">{{ __('site.hero.support') }}</x-ui.button>
                <x-ui.button :href="route('volunteer.create')" variant="secondary" size="xl" icon="heart-handshake">{{ __('site.hero.volunteer') }}</x-ui.button>
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
