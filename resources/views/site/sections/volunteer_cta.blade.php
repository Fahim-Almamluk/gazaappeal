<section class="mx-auto w-full max-w-[1280px] px-5 sm:px-8">
    <div class="reveal-pop relative isolate overflow-clip rounded-[32px] bg-linear-to-l from-brand-700 via-brand-600 to-brand-500 p-8 text-white sm:p-14">
        <div class="scroll-drift pointer-events-none absolute -top-24 -end-10 -z-10 size-80 rounded-full bg-white/15 blur-3xl"></div>
        <div class="grain pointer-events-none absolute inset-0 -z-10 opacity-[0.15] mix-blend-overlay"></div>

        <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl space-y-3">
                <h2 class="text-[30px] leading-tight font-extrabold tracking-tight sm:text-[38px]">{{ __('site.volunteer_cta.title') }}</h2>
                <p class="text-lg leading-8 text-white/80">{{ __('site.volunteer_cta.body') }}</p>
            </div>

            <a href="{{ route('volunteer.create') }}" class="inline-flex h-[52px] shrink-0 items-center justify-center gap-2 self-start rounded-2xl bg-white px-7 text-base font-bold text-brand-700 shadow-[0_14px_30px_-12px_rgb(0_0_0/0.45)] transition hover:-translate-y-0.5 lg:self-center">
                <x-ui.icon name="heart-handshake" :size="20" />
                {{ __('site.volunteer_cta.action') }}
            </a>
        </div>
    </div>
</section>
