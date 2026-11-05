<x-layouts.admin :title="__('admin.stories.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="award" :title="__('admin.stories.title')" :subtitle="__('admin.stories.subtitle')">
            <x-slot:actions>
                @can('stories.create')
                    <x-ui.button :href="route('admin.stories.create')" icon="plus">{{ __('admin.stories.add') }}</x-ui.button>
                @endcan
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card :padding="false" class="overflow-hidden">
            <x-ui.filter-bar :action="route('admin.stories.index')" :active="filled($search) || request()->filled('visibility')">
                <x-ui.search-input :value="$search" :placeholder="__('admin.stories.search')" />
                @include('admin.partials.visibility-filter')
            </x-ui.filter-bar>

            <x-ui.results-count :paginator="$stories" />

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-sm">
                    <thead>
                        <tr class="border-b border-line bg-surface-2/60 text-xs font-semibold text-ink-subtle">
                            <th scope="col" class="w-14 px-4 py-3 text-start">#</th>
                            <th scope="col" class="px-6 py-3 text-start">{{ __('admin.stories.columns.story') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.stories.columns.project') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.common.created_at') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.common.visibility') }}</th>
                            <th scope="col" class="px-6 py-3 text-end">{{ __('ui.actions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($stories as $story)
                            <tr class="transition hover:bg-surface-hover">
                                <td class="tabular px-4 py-3.5 text-xs font-semibold text-ink-subtle">{{ $stories->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <x-ui.thumb :src="$story->imageUrl()" icon="award" />
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink">{{ $story->title }}</p>
                                            <p class="max-w-md truncate text-xs text-ink-subtle">{{ $story->summary }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3.5 text-ink-muted">{{ $story->project?->title ?? __('ui.common.none') }}</td>
                                <td class="tabular px-3 py-3.5 text-xs text-ink-subtle">{{ $story->created_at->format('Y-m-d') }}</td>
                                <td class="px-3 py-3.5">@include('admin.partials.visibility-badge', ['published' => $story->is_published])</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-0.5">
                                        @if ($story->is_published)
                                            <x-ui.icon-button icon="eye" :label="__('ui.actions.view')" :href="route('stories.show', $story)" target="_blank" />
                                        @endif
                                        @can('stories.update')
                                            <x-ui.icon-button icon="pencil" tone="primary" :label="__('ui.actions.edit')" :href="route('admin.stories.edit', $story)" />
                                        @endcan
                                        @can('stories.delete')
                                            <x-ui.delete-button :action="route('admin.stories.destroy', $story)" :name="$story->title" />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-ui.empty-state icon="award" :title="__('admin.stories.empty')" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$stories" />
        </x-ui.card>
    </div>
</x-layouts.admin>
