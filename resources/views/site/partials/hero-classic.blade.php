<section class="relative isolate overflow-clip">
    <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(ellipse_55%_70%_at_24%_42%,rgb(21_104_201/0.10),transparent_70%)] dark:bg-[radial-gradient(ellipse_55%_70%_at_24%_42%,rgb(47_143_224/0.18),transparent_70%)]"></div>
    <div class="hero-dots pointer-events-none absolute inset-0 -z-10"></div>

    <div class="mx-auto grid w-full max-w-[1280px] items-center gap-8 px-5 pt-10 pb-4 sm:px-8 lg:grid-cols-[1fr_1.1fr] lg:pt-8">
        <div class="hero-leave space-y-8 [--lift:18px]">
            <div class="animate-rise space-y-6">
                <h1 class="text-[34px] leading-[1.35] font-bold text-ink sm:text-[46px] lg:text-[44px] xl:text-[54px] [&_mark]:animate-shimmer [&_mark]:bg-linear-to-l [&_mark]:from-brand-700 [&_mark]:via-brand-400 [&_mark]:to-brand-700 [&_mark]:bg-[length:200%_auto] [&_mark]:bg-clip-text [&_mark]:text-transparent motion-reduce:[&_mark]:animate-none dark:[&_mark]:from-brand-300 dark:[&_mark]:via-brand-100 dark:[&_mark]:to-brand-300">
                    {!! __('site.hero.title') !!}
                </h1>
                <p class="max-w-xl text-lg leading-8 text-ink-muted">{{ __('site.hero.body') }}</p>
            </div>

            <div class="animate-rise flex flex-wrap gap-3 [animation-delay:120ms]">
                <x-ui.button :href="route('donate')" size="xl" icon="heart">{{ __('site.nav.donate') }}</x-ui.button>
                <x-ui.button :href="route('volunteer.create')" variant="secondary" size="xl" icon="heart-handshake">{{ __('site.hero.volunteer') }}</x-ui.button>
            </div>
        </div>

        <div class="hero-leave">
            <x-scene.solar class="animate-rise mx-auto max-w-[560px] [animation-delay:160ms]" />
        </div>
    </div>

    <div class="mx-auto w-full max-w-3xl px-5 pt-10 pb-16 sm:px-8 lg:pt-14">
        @include('site.partials.impact-bar')
    </div>
</section>
