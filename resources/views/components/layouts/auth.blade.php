@props(['title' => null])

<x-layouts.base :title="$title" class="bg-canvas">
    <div class="flex min-h-dvh items-center justify-center p-0 sm:p-5 lg:p-8">
        <div class="grid min-h-dvh w-full max-w-[1180px] bg-surface sm:min-h-0 sm:rounded-[32px] sm:p-3 sm:shadow-[0_40px_100px_-40px_rgb(20_30_100/0.28)] lg:h-[min(760px,calc(100dvh-4rem))] lg:grid-cols-2 lg:gap-3 dark:sm:shadow-none dark:sm:ring-1 dark:sm:ring-line">
            <section class="relative flex min-h-0 flex-col overflow-x-clip px-6 py-7 sm:px-10 lg:px-14 lg:py-9">
                @if (session('status'))
                    <div
                        x-data="toast"
                        data-timeout="5000"
                        x-show="shown"
                        x-bind:class="fade"
                        role="status"
                        class="absolute start-6 end-[4.5rem] top-5 z-20 flex items-center gap-2.5 rounded-xl border border-success/25 bg-success-soft px-3.5 py-2.5 text-sm font-semibold text-success shadow-sm transition-opacity duration-300 sm:start-10 lg:start-14"
                    >
                        <x-ui.icon name="circle-check" :size="18" />
                        <span class="flex-1">{{ session('status') }}</span>
                        <button type="button" x-on:click="dismiss" class="rounded-md p-0.5 opacity-70 transition hover:opacity-100" aria-label="{{ __('ui.common.close') }}">
                            <x-ui.icon name="x" :size="15" />
                        </button>
                    </div>
                @endif

                <header class="flex items-center justify-end gap-2">
                    <x-ui.language-switch />
                    @include('partials.theme-menu')
                </header>

                <main class="flex flex-1 items-center py-6">
                    <div class="mx-auto w-full max-w-[400px]">
                        {{ $slot }}
                    </div>
                </main>
            </section>

            <x-brand.login-panel class="hidden lg:flex" />
        </div>
    </div>
</x-layouts.base>
