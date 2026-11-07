@props(['title' => null])

@inject('impersonation', 'App\Support\Impersonation')

<x-layouts.base :title="$title" x-data="shell">
    <a href="#content" class="sr-only z-[60] rounded-lg bg-primary px-4 py-2 text-primary-fg focus:not-sr-only focus:fixed focus:top-3 focus:start-3">
        {{ __('ui.common.skip_to_content') }}
    </a>

    <div
        class="group/shell min-h-dvh lg:grid lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:data-[collapsed=true]:grid-cols-[84px_minmax(0,1fr)]"
        x-bind:data-collapsed="collapsed"
        x-bind:data-mobile-open="mobileOpen"
    >
        @include('admin.partials.sidebar')

        <div class="flex min-w-0 flex-col">
            <div class="sticky top-0 z-30">
                @if ($impersonation->active())
                    @include('admin.partials.impersonation-bar')
                @endif
                @include('admin.partials.header')
            </div>

            <main id="content" class="mx-auto w-full max-w-[1400px] flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                {{ $slot }}
            </main>

            <footer class="flex min-h-14 flex-wrap items-center justify-center gap-x-3 gap-y-1 border-t border-line px-4 py-2 text-center text-xs text-ink-subtle sm:px-6 lg:h-14 lg:px-8 lg:py-0">
                <span>{{ __('ui.footer.rights', ['year' => now()->year]) }}</span>
                <span class="hidden size-1 rounded-full bg-line-strong sm:block" aria-hidden="true"></span>
                <span>{{ __('ui.footer.version', ['version' => '1.0.0']) }}</span>
            </footer>
        </div>
    </div>

    <x-ui.scroll-top />

    @include('admin.partials.toasts')
</x-layouts.base>
