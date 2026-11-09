<x-layouts.admin :title="__('admin.home_sections.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="layout-list" :title="__('admin.home_sections.title')" :subtitle="__('admin.home_sections.subtitle')">
            <x-slot:actions>
                <form method="POST" action="{{ route('admin.home-sections.reset') }}" @confirm('warning', 'reset_home')>
                    @csrf
                    <x-ui.button type="submit" variant="secondary" icon="rotate-ccw">{{ __('admin.home_sections.restore') }}</x-ui.button>
                </form>
            </x-slot:actions>
        </x-ui.page-header>

        @include('admin.settings.partials.tabs')

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-start">
            <x-ui.card :padding="false" class="overflow-hidden">
                <div class="flex items-center gap-3 border-b border-line bg-surface-2/60 px-4 py-3 sm:px-5">
                    <x-ui.icon name="sparkles" :size="18" class="shrink-0 text-ink-subtle" />
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-ink">{{ __('admin.home_sections.hero') }}</p>
                        <p class="text-xs text-ink-subtle">{{ __('admin.home_sections.hero_hint') }}</p>
                    </div>
                </div>

                <ul
                    x-data="sortList"
                    x-on:dragstart="start"
                    x-on:dragover="over"
                    x-on:drop="drop"
                    x-on:dragend="end"
                    data-url="{{ route('admin.home-sections.update') }}"
                    data-group="home"
                    data-saved="{{ __('admin.home_sections.saved') }}"
                    data-failed="{{ __('admin.home_sections.failed') }}"
                    class="divide-y divide-line"
                >
                    @foreach ($sections as $section)
                        <li data-id="{{ $section['key'] }}" data-visible="{{ $section['visible'] ? 'true' : 'false' }}" class="group/item bg-surface data-[dragging=true]:opacity-40">
                            <div data-row class="flex items-center gap-2 px-3 py-3 transition hover:bg-surface-hover sm:px-4 [li[data-visible=false]>&]:opacity-60">
                                <x-ui.sort-handle />
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink">
                                    <x-ui.icon :name="$section['icon']" :size="20" />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-ink">{{ __('admin.home_sections.sections.'.$section['key'].'.name') }}</p>
                                    <p class="truncate text-xs text-ink-subtle">{{ __('admin.home_sections.sections.'.$section['key'].'.hint') }}</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-0.5">
                                    <x-ui.sort-actions />
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </x-ui.card>

            <x-ui.card class="space-y-3 lg:sticky lg:top-24">
                <p class="text-xs font-bold text-ink-subtle">{{ __('admin.home_sections.preview') }}</p>
                <div x-data="sortPreview" x-on:sorted.window="sync" data-group="home" class="space-y-2 rounded-2xl border border-line bg-surface-2 p-3">
                    <div class="flex h-12 items-center justify-center rounded-xl bg-primary-soft text-xs font-bold text-primary-soft-ink">{{ __('admin.home_sections.hero') }}</div>
                    <div x-ref="items" class="space-y-2">
                        @foreach ($sections as $section)
                            <div data-id="{{ $section['key'] }}" @if (! $section['visible']) hidden @endif class="flex h-11 items-center gap-2 rounded-xl border border-line bg-surface px-3 text-xs font-semibold text-ink-muted">
                                <x-ui.icon :name="$section['icon']" :size="15" class="shrink-0 text-ink-subtle" />
                                <span class="truncate">{{ __('admin.home_sections.sections.'.$section['key'].'.name') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-ui.card>
        </div>

        <p class="px-1 text-[13px] leading-6 text-ink-muted">{{ __('admin.home_sections.note') }}</p>
    </div>
</x-layouts.admin>
