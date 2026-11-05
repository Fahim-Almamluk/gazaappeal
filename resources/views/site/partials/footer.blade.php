@inject('settings', 'App\Support\SiteSettings')

@php
    $hover = [
        'facebook' => ['tile' => 'hover:bg-social-facebook hover:text-white', 'motion' => 'group-hover/social:animate-social-bounce'],
        'instagram' => ['tile' => 'hover:bg-instagram hover:text-white', 'motion' => 'group-hover/social:animate-social-pop'],
        'x' => ['tile' => 'hover:bg-ink hover:text-bg', 'motion' => 'group-hover/social:animate-social-flip'],
        'tiktok' => ['tile' => 'hover:bg-ink hover:text-bg', 'motion' => 'group-hover/social:animate-social-wiggle'],
        'youtube' => ['tile' => 'hover:bg-social-youtube hover:text-white', 'motion' => 'group-hover/social:animate-social-pulse'],
        'telegram' => ['tile' => 'hover:bg-social-telegram hover:text-white', 'motion' => 'group-hover/social:animate-social-fly'],
    ];
    $socials = $settings->socials();
@endphp

<footer class="border-t border-line bg-surface">
    <div class="mx-auto flex w-full max-w-[1280px] flex-col items-center gap-5 px-5 py-5 sm:px-8 lg:flex-row lg:items-end lg:justify-between lg:gap-8">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 lg:items-start">
            <x-brand.logo class="h-12" />
            <span class="text-xs text-ink-subtle">{{ __('ui.brand.owner') }}</span>
        </a>

        <p class="flex flex-col items-center text-center text-xs leading-5 text-ink-subtle">
            <span class="flex items-center gap-2">
                <a href="{{ route('faq') }}" class="transition hover:text-primary">{{ __('site.nav.faq') }}</a>
                <span class="size-1 rounded-full bg-line-strong" aria-hidden="true"></span>
                <a href="{{ route('privacy') }}" class="transition hover:text-primary">{{ __('site.nav.privacy') }}</a>
            </span>
            <span class="inline-flex items-center gap-1.5 font-bold text-brand-700 dark:text-primary">
                <x-brand.mark :size="15" />
                {{ __('site.footer.rights', ['year' => now()->year]) }}
            </span>
        </p>

        @if ($socials)
            <div class="flex flex-col items-center gap-2 lg:items-start">
                <p class="text-xs font-bold text-ink-muted">{{ __('site.footer.social_title') }}</p>
                <ul class="flex items-center gap-2">
                    @foreach ($socials as $name => $url)
                        <li>
                            <a
                                href="{{ $url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="{{ __("site.social.{$name}") }}"
                                aria-label="{{ __("site.social.{$name}") }}"
                                class="group/social flex size-10 items-center justify-center rounded-xl border border-line bg-surface-2 text-ink-muted transition duration-200 hover:-translate-y-0.5 hover:border-transparent hover:shadow-card focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none {{ $hover[$name]['tile'] }}"
                            >
                                <x-brand.social :name="$name" :size="18" class="{{ $hover[$name]['motion'] }} motion-reduce:animate-none" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</footer>
