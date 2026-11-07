<x-layouts.admin :title="__('admin.menu.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="list-tree" :title="__('admin.menu.title')" :subtitle="__('admin.menu.subtitle')">
            <x-slot:actions>
                <form method="POST" action="{{ route('admin.menu.reset') }}" @confirm('warning', 'reset_menu')>
                    @csrf
                    <x-ui.button type="submit" variant="secondary" icon="rotate-ccw">{{ __('admin.menu.restore') }}</x-ui.button>
                </form>
                <x-ui.button :href="route('admin.menu.create')" icon="plus">{{ __('admin.menu.add') }}</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card class="space-y-3">
            <p class="text-xs font-bold text-ink-subtle">{{ __('admin.menu.preview') }}</p>
            <div x-data="sortPreview" x-on:sorted.window="sync" data-group="menu-root" class="overflow-x-auto pb-1">
                <div x-ref="items" class="flex w-fit min-w-full items-center justify-center gap-1 rounded-full border border-line bg-surface-2 p-1">
                    @forelse ($items as $item)
                        <span data-id="{{ $item->id }}" @if (! $item->is_visible) hidden @endif class="inline-flex items-center gap-1 rounded-full px-3.5 py-2 text-sm font-semibold whitespace-nowrap text-ink-muted">
                            {{ $item->title() }}
                            @if ($item->children->isNotEmpty())
                                <x-ui.icon name="chevron-down" :size="14" />
                            @endif
                        </span>
                    @empty
                        <span class="px-3.5 py-2 text-sm text-ink-subtle">{{ __('admin.menu.empty') }}</span>
                    @endforelse
                </div>
            </div>
        </x-ui.card>

        <x-ui.card :padding="false" class="overflow-hidden">
            @if ($items->isEmpty())
                <x-ui.empty-state icon="list-tree" :title="__('admin.menu.empty')" :body="__('admin.menu.empty_body')" />
            @else
                <ul
                    x-data="sortList"
                    x-on:dragstart="start"
                    x-on:dragover="over"
                    x-on:drop="drop"
                    x-on:dragend="end"
                    data-url="{{ route('admin.menu.reorder') }}"
                    data-group="menu-root"
                    data-saved="{{ __('admin.menu.order_saved') }}"
                    data-failed="{{ __('admin.menu.order_failed') }}"
                    class="divide-y divide-line"
                >
                    @foreach ($items as $item)
                        @include('admin.menu.partials.row', ['item' => $item, 'nested' => false])
                    @endforeach
                </ul>
            @endif
        </x-ui.card>

        <p class="px-1 text-[13px] leading-6 text-ink-muted">{{ __('admin.menu.limits', ['roots' => config('navigation.limits.roots'), 'children' => config('navigation.limits.children')]) }}</p>
    </div>
</x-layouts.admin>
