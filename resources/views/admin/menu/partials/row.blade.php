@php
    $limit = config('navigation.limits.children');
    $target = match ($item->type()) {
        'page' => __('admin.menu.type.page').': '.$item->pageLabel(),
        'url' => __('admin.menu.type.url').': '.parse_url($item->url, PHP_URL_HOST),
        default => __('admin.menu.type.group'),
    };
@endphp

<li data-id="{{ $item->id }}" data-visible="{{ $item->is_visible ? 'true' : 'false' }}" class="group/item bg-surface data-[dragging=true]:opacity-40">
    <div data-row @class([
        'flex items-center gap-2 px-3 py-3 transition hover:bg-surface-hover sm:px-4 [li[data-visible=false]>&]:opacity-60',
        'ps-8 sm:ps-12' => $nested,
    ])>
        <x-ui.sort-handle />

        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-bold text-ink">{{ $item->title() }}</p>
            <p class="truncate text-xs text-ink-subtle">
                {{ $target }}
                @if (! $nested && $item->children->isNotEmpty())
                    <span class="mx-1 text-line-strong" aria-hidden="true">•</span>{{ __('admin.menu.children_count', ['count' => $item->children->count()]) }}
                @endif
            </p>
        </div>

        <div class="flex shrink-0 items-center gap-0.5">
            <x-ui.sort-actions />
            @unless ($nested)
                @if ($item->children->count() < $limit)
                    <x-ui.icon-button icon="plus" tone="primary" :label="__('admin.menu.add_child')" :href="route('admin.menu.create', ['parent' => $item->id])" />
                @endif
            @endunless
            <x-ui.icon-button icon="pencil" tone="primary" :label="__('ui.actions.edit')" :href="route('admin.menu.edit', $item)" />
            <x-ui.delete-button :action="route('admin.menu.destroy', $item)" :name="$item->title()" />
        </div>
    </div>

    @unless ($nested)
        @if ($item->children->isNotEmpty())
            <ul
                x-data="sortList"
                x-on:dragstart="start"
                x-on:dragover="over"
                x-on:drop="drop"
                x-on:dragend="end"
                data-url="{{ route('admin.menu.reorder') }}"
                data-group="menu-{{ $item->id }}"
                data-saved="{{ __('admin.menu.order_saved') }}"
                data-failed="{{ __('admin.menu.order_failed') }}"
                class="divide-y divide-line border-t border-line bg-surface-2/50"
            >
                @foreach ($item->children as $child)
                    @include('admin.menu.partials.row', ['item' => $child, 'nested' => true])
                @endforeach
            </ul>
        @endif
    @endunless
</li>
