@props([
    'title',
    'subtitle' => null,
    'parent' => null,
    'parentUrl' => null,
])

<section class="mx-auto w-full max-w-[1280px] px-5 pt-6 sm:px-8">
    <div class="animate-rise relative isolate overflow-hidden rounded-[28px] bg-linear-to-l from-brand-700 via-brand-600 to-brand-500 px-6 py-7 text-white sm:px-10 sm:py-9 dark:from-brand-800 dark:via-brand-700 dark:to-brand-600">
        <div class="pointer-events-none absolute -top-24 start-10 -z-10 size-72 rounded-full bg-brand-300/30 blur-3xl"></div>
        <div class="grain pointer-events-none absolute inset-0 -z-10 opacity-[0.15] mix-blend-overlay"></div>
        <div class="dot-grid pointer-events-none absolute inset-0 -z-10 opacity-30 [mask-image:linear-gradient(to_left,transparent_35%,#000)] ltr:[mask-image:linear-gradient(to_right,transparent_35%,#000)]"></div>

        <x-brand.power-line class="pointer-events-none absolute bottom-0 end-4 -z-10 hidden h-auto w-[34%] max-w-[440px] md:block lg:end-10" />

        <div class="max-w-2xl md:max-w-[58%]">
            <nav aria-label="breadcrumb" class="flex items-center gap-1.5 text-[13px] text-white/70">
                <a href="{{ route('home') }}" class="font-medium transition hover:text-white">{{ __('site.nav.home') }}</a>
                @if ($parent)
                    <x-ui.icon name="chevron-left" :size="14" />
                    <a href="{{ $parentUrl }}" class="font-medium transition hover:text-white">{{ $parent }}</a>
                @endif
                <x-ui.icon name="chevron-left" :size="14" />
                <span class="line-clamp-1 font-semibold text-white">{{ $title }}</span>
            </nav>

            <h1 class="mt-3 text-[28px] leading-tight font-extrabold sm:text-[36px]">{{ $title }}</h1>

            @if ($subtitle)
                <p class="mt-2 line-clamp-2 text-[15px] leading-7 text-white/80">{{ $subtitle }}</p>
            @endif

            @if (! $slot->isEmpty())
                <div class="mt-4">{{ $slot }}</div>
            @endif
        </div>
    </div>
</section>
