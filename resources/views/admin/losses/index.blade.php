<x-layouts.admin :title="__('admin.losses.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="zap-off" :title="__('admin.losses.title')" :subtitle="__('admin.losses.subtitle')">
            <x-slot:actions>
                @can('losses.create')
                    <x-ui.button :href="route('admin.losses.create')" icon="plus">{{ __('admin.losses.add') }}</x-ui.button>
                @endcan
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card :padding="false" class="overflow-hidden">
            <x-ui.filter-bar :action="route('admin.losses.index')" :active="filled($search) || request()->filled('visibility')">
                <x-ui.search-input :value="$search" :placeholder="__('admin.losses.search')" />
                @include('admin.partials.visibility-filter')
            </x-ui.filter-bar>

            <x-ui.results-count :paginator="$losses" />

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-sm">
                    <thead>
                        <tr class="border-b border-line bg-surface-2/60 text-xs font-semibold text-ink-subtle">
                            <th scope="col" class="w-14 px-4 py-3 text-start">#</th>
                            <th scope="col" class="px-6 py-3 text-start">{{ __('admin.losses.columns.loss') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.losses.columns.figure') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('ui.form.sort_order') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.common.visibility') }}</th>
                            <th scope="col" class="px-6 py-3 text-end">{{ __('ui.actions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($losses as $loss)
                            <tr class="transition hover:bg-surface-hover">
                                <td class="tabular px-4 py-3.5 text-xs font-semibold text-ink-subtle">{{ $losses->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-danger-soft text-danger"><x-ui.icon :name="$loss->icon->value" :size="18" /></span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink">{{ $loss->title }}</p>
                                            <p class="max-w-md truncate text-xs text-ink-subtle">{{ $loss->description }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3.5"><span class="tabular font-bold text-ink">{{ $loss->formattedFigure() }}</span> <span class="text-xs text-ink-muted">{{ $loss->unit }}</span></td>
                                <td class="tabular px-3 py-3.5 text-ink-muted">{{ $loss->sort_order }}</td>
                                <td class="px-3 py-3.5">@include('admin.partials.visibility-badge', ['published' => $loss->is_published])</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-0.5">
                                        @can('losses.update')
                                            <x-ui.icon-button icon="pencil" tone="primary" :label="__('ui.actions.edit')" :href="route('admin.losses.edit', $loss)" />
                                        @endcan
                                        @can('losses.delete')
                                            <x-ui.delete-button :action="route('admin.losses.destroy', $loss)" :name="$loss->title" />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-ui.empty-state icon="zap-off" :title="__('admin.losses.empty')" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$losses" />
        </x-ui.card>
    </div>
</x-layouts.admin>
