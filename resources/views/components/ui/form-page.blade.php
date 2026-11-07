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
            <x-ui.back-button :href="$back">{{ __('ui.common.back') }}</x-ui.back-button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px] xl:items-start">
        <div class="space-y-6">{{ $slot }}</div>

        @isset($aside)
            <aside class="space-y-4 xl:sticky xl:top-24">{{ $aside }}</aside>
        @endisset
    </div>
</div>
