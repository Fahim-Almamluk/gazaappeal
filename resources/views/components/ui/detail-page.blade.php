@props([
    'icon',
    'title',
    'subtitle' => null,
    'back',
    'parent' => null,
    'parentUrl' => null,
])

<div class="mx-auto max-w-6xl space-y-6">
    <x-ui.page-header :icon="$icon" :title="$title" :subtitle="$subtitle" :parent="$parent" :parent-url="$parentUrl">
        <x-slot:actions>
            @isset($actions){{ $actions }}@endisset
            <x-ui.back-button :href="$back">{{ __('ui.common.back') }}</x-ui.back-button>
        </x-slot:actions>
    </x-ui.page-header>

    @isset($summary)
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">{{ $summary }}</div>
    @endisset

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px] xl:items-start">
        <div class="space-y-6">{{ $slot }}</div>

        @isset($aside)
            <aside class="space-y-6">{{ $aside }}</aside>
        @endisset
    </div>
</div>
