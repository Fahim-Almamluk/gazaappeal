@props(['title' => null])

<x-layouts.base :title="$title" class="bg-canvas">
    <div class="flex min-h-dvh items-center justify-center p-0 sm:p-5 lg:p-8">
        <div class="grid min-h-dvh w-full max-w-[1180px] bg-surface sm:min-h-0 sm:rounded-[32px] sm:p-3 sm:shadow-[0_40px_100px_-40px_rgb(20_30_100/0.28)] lg:h-[min(760px,calc(100dvh-4rem))] lg:grid-cols-2 lg:gap-3 dark:sm:shadow-none dark:sm:ring-1 dark:sm:ring-line">
            <section class="relative flex min-h-0 flex-col overflow-x-clip px-6 py-7 sm:px-10 lg:px-14 lg:py-9">
                <header class="flex items-center justify-end gap-2">
                    <x-ui.language-switch />
                    @include('partials.theme-toggle')
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
