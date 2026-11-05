@props([
    'icon',
    'title',
    'subtitle' => null,
    'back',
    'parent' => null,
    'parentUrl' => null,
])

<div class="mx-auto max-w-6xl space-y-6">
    @if ($parent)
        <nav aria-label="breadcrumb" class="flex items-center gap-2 text-sm text-ink-subtle">
            <a href="{{ $parentUrl }}" class="font-medium text-ink-muted transition hover:text-primary">{{ $parent }}</a>
            <x-ui.icon name="chevron-left" :size="14" />
            <span class="font-semibold text-ink">{{ $title }}</span>
        </nav>
    @endif

    <x-ui.page-header :icon="$icon" :title="$title" :subtitle="$subtitle">
        <x-slot:actions>
            @isset($actions){{ $actions }}@endisset
            <x-ui.back-button :href="$back">{{ __('ui.common.back') }}</x-ui.back-button>
        </x-slot:actions>
    </x-ui.page-header>

    @if ($errors->any())
        <x-ui.alert tone="danger">{{ $errors->first() }}</x-ui.alert>
    @endif

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
