<x-layouts.admin :title="__('admin.partners.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="handshake" :title="__('admin.partners.title')" :subtitle="__('admin.partners.subtitle')">
            <x-slot:actions>
                @can('partners.create')
                    <x-ui.button :href="route('admin.partners.create')" icon="plus">{{ __('admin.partners.add') }}</x-ui.button>
                @endcan
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card :padding="false" class="overflow-hidden" x-data="bulkSelect" x-on:change="sync">
            <x-ui.filter-bar :action="route('admin.partners.index')" :active="filled($search) || request()->filled('visibility')">
                <x-ui.search-input :value="$search" :placeholder="__('admin.partners.search')" />
                @include('admin.partials.visibility-filter')
            </x-ui.filter-bar>

            <x-ui.results-count :paginator="$partners" />

            <x-ui.bulk-bar module="partners" route="partners" />

            <div class="overflow-x-auto">
                <table class="w-full min-w-[680px] text-sm">
                    <thead>
                        <tr class="border-b border-line bg-surface-2/60 text-xs font-semibold text-ink-subtle">
                            <th scope="col" class="w-10 ps-4 py-3 text-start"><x-ui.bulk-check module="partners" /></th>
                            <th scope="col" class="w-14 px-4 py-3 text-start">#</th>
                            <th scope="col" class="px-6 py-3 text-start">{{ __('admin.partners.columns.partner') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('ui.form.sort_order') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.common.visibility') }}</th>
                            <th scope="col" class="px-6 py-3 text-end">{{ __('ui.actions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($partners as $partner)
                            <tr class="transition hover:bg-surface-hover">
                                <td class="ps-4 py-3.5"><x-ui.bulk-check module="partners" :id="$partner->id" /></td>
                                <td class="tabular px-4 py-3.5 text-xs font-semibold text-ink-subtle">{{ $partners->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <x-ui.thumb :src="$partner->imageUrl()" icon="handshake" class="object-contain p-1" />
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink">{{ $partner->name }}</p>
                                            @if ($partner->website)
                                                <p class="max-w-xs truncate text-xs text-ink-subtle" dir="ltr">{{ $partner->website }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="tabular px-3 py-3.5 text-ink-muted">{{ $partner->sort_order }}</td>
                                <td class="px-3 py-3.5">@include('admin.partials.visibility-badge', ['published' => $partner->is_published])</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-0.5">
                                        @can('partners.update')
                                            <x-ui.icon-button icon="pencil" tone="primary" :label="__('ui.actions.edit')" :href="route('admin.partners.edit', $partner)" />
                                        @endcan
                                        @can('partners.delete')
                                            <x-ui.delete-button :action="route('admin.partners.destroy', $partner)" :name="$partner->name" />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-ui.empty-state icon="handshake" :title="__('admin.partners.empty')" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$partners" />
        </x-ui.card>
    </div>
</x-layouts.admin>
