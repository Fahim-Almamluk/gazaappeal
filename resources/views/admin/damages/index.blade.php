@php
    use App\Enums\Governorate;
    use App\Enums\LookupType;
    use App\Models\Lookup;
    use App\Support\Money;
@endphp

<x-layouts.admin :title="__('admin.damages.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="map-pin" :title="__('admin.damages.title')" :subtitle="__('admin.damages.subtitle')">
            <x-slot:actions>
                <x-ui.button variant="excel" icon="download" :href="route('admin.damages.export', request()->query())">{{ __('admin.export.button') }}</x-ui.button>
                @can('damages.create')
                    <x-ui.button :href="route('admin.damages.create')" icon="plus">{{ __('admin.damages.add') }}</x-ui.button>
                @endcan
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card :padding="false" class="overflow-hidden" x-data="bulkSelect" x-on:change="sync">
            <x-ui.filter-bar :action="route('admin.damages.index')" :active="filled($search) || $governorate || $category">
                <x-ui.search-input :value="$search" :placeholder="__('admin.damages.search')" />
                <div class="w-full sm:w-48">
                    <x-ui.select name="governorate" compact :options="Governorate::options()" :selected="$governorate?->value" :placeholder="__('admin.damages.all_governorates')" :aria-label="__('admin.damages.columns.governorate')" />
                </div>
                <div class="w-full sm:w-56">
                    <x-ui.select name="category" compact :options="Lookup::options(LookupType::DamageCategory)" :selected="$category" :placeholder="__('admin.damages.all_categories')" :aria-label="__('admin.damages.columns.category')" />
                </div>
            </x-ui.filter-bar>

            <x-ui.results-count :paginator="$damages" />

            <x-ui.bulk-bar module="damages" route="damages" :publishable="false" />

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-sm">
                    <thead>
                        <tr class="border-b border-line bg-surface-2/60 text-xs font-semibold text-ink-subtle">
                            <th scope="col" class="w-10 ps-4 py-3 text-start"><x-ui.bulk-check module="damages" /></th>
                            <th scope="col" class="w-14 px-4 py-3 text-start">#</th>
                            <th scope="col" class="px-6 py-3 text-start">{{ __('admin.damages.columns.item') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.damages.columns.governorate') }}</th>
                            <th scope="col" class="px-3 py-3 text-start"><x-ui.sort-link column="amount" first="desc">{{ __('admin.damages.columns.amount') }}</x-ui.sort-link></th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('ui.form.sort_order') }}</th>
                            <th scope="col" class="px-6 py-3 text-end">{{ __('ui.actions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($damages as $damage)
                            <tr class="transition hover:bg-surface-hover">
                                <td class="ps-4 py-3.5"><x-ui.bulk-check module="damages" :id="$damage->id" /></td>
                                <td class="tabular px-4 py-3.5 text-xs font-semibold text-ink-subtle">{{ $damages->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-danger-soft text-danger"><x-ui.icon :name="$damage->category->displayIcon()" :size="18" /></span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink">{{ $damage->item }}</p>
                                            <p class="truncate text-xs text-ink-subtle">{{ $damage->category->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3.5 font-medium text-ink-muted">{{ $damage->governorate->label() }}</td>
                                <td class="px-3 py-3.5"><span class="tabular font-bold text-ink" dir="ltr">{{ Money::short($damage->amount) }}</span></td>
                                <td class="tabular px-3 py-3.5 text-ink-muted">{{ $damage->sort_order }}</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-0.5">
                                        @can('damages.update')
                                            <x-ui.icon-button icon="pencil" tone="primary" :label="__('ui.actions.edit')" :href="route('admin.damages.edit', $damage)" />
                                        @endcan
                                        @can('damages.delete')
                                            <x-ui.delete-button :action="route('admin.damages.destroy', $damage)" :name="$damage->item" />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7"><x-ui.empty-state icon="map-pin" :title="__('admin.damages.empty')" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$damages" />
        </x-ui.card>
    </div>
</x-layouts.admin>
