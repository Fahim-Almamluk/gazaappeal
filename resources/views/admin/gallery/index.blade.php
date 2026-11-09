<x-layouts.admin :title="__('admin.gallery.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="images" :title="__('admin.gallery.title')" :subtitle="__('admin.gallery.subtitle')">
            <x-slot:actions>
                @can('gallery.create')
                    <x-ui.button :href="route('admin.gallery.create')" icon="plus">{{ __('admin.gallery.add') }}</x-ui.button>
                @endcan
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card :padding="false" class="overflow-hidden" x-data="bulkSelect" x-on:change="sync">
            <x-ui.filter-bar :action="route('admin.gallery.index')" :active="filled($search) || request()->filled('visibility')">
                <x-ui.search-input :value="$search" :placeholder="__('admin.gallery.search')" />
                @include('admin.partials.visibility-filter')
            </x-ui.filter-bar>

            <x-ui.results-count :paginator="$items" />

            <x-ui.bulk-bar module="gallery" route="gallery" />

            @if ($items->isEmpty())
                <x-ui.empty-state icon="images" :title="__('admin.gallery.empty')" />
            @else
                @canany(['gallery.update', 'gallery.delete'])
                    <label class="flex items-center gap-2.5 border-b border-line px-6 py-2.5 text-sm text-ink-muted">
                        <x-ui.bulk-check module="gallery" />
                        {{ __('admin.bulk.select_all') }}
                    </label>
                @endcanany

                <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($items as $item)
                        <figure class="overflow-hidden rounded-card border border-line bg-surface">
                            <div class="relative aspect-[4/3] bg-surface-2">
                                <img src="{{ $item->imageUrl() }}" alt="{{ $item->caption }}" loading="lazy" class="size-full object-cover">
                                <span class="absolute top-2.5 start-2.5">@include('admin.partials.visibility-badge', ['published' => $item->is_published])</span>
                                <span class="absolute top-2.5 end-2.5 flex rounded-md bg-surface/90 p-1 shadow-sm"><x-ui.bulk-check module="gallery" :id="$item->id" /></span>
                            </div>
                            <figcaption class="flex items-center gap-2 px-4 py-3">
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-ink">{{ $item->caption }}</span>
                                    <span class="tabular block text-xs text-ink-subtle">{{ __('ui.form.sort_order') }}: {{ $item->sort_order }}</span>
                                </span>
                                @can('gallery.update')
                                    <x-ui.icon-button icon="pencil" tone="primary" :label="__('ui.actions.edit')" :href="route('admin.gallery.edit', $item)" />
                                @endcan
                                @can('gallery.delete')
                                    <x-ui.delete-button :action="route('admin.gallery.destroy', $item)" :name="$item->caption" />
                                @endcan
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            @endif

            <x-ui.pagination :paginator="$items" />
        </x-ui.card>
    </div>
</x-layouts.admin>
