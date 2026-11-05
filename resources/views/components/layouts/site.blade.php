@props(['title' => null, 'description' => null])

<x-layouts.base :title="$title" :description="$description ?? __('site.meta.description')" index class="bg-bg">
    <a href="#content" class="sr-only z-[60] rounded-lg bg-primary px-4 py-2 text-primary-fg focus:not-sr-only focus:fixed focus:top-3 focus:start-3">
        {{ __('ui.common.skip_to_content') }}
    </a>

    @include('site.partials.header')

    <main id="content">
        @if (session('success'))
            <div class="mx-auto mt-6 w-full max-w-[1280px] px-5 sm:px-8">
                <x-ui.alert tone="success">{{ session('success') }}</x-ui.alert>
            </div>
        @endif

        {{ $slot }}
    </main>

    @include('site.partials.footer')

    <x-ui.scroll-top />
</x-layouts.base>
