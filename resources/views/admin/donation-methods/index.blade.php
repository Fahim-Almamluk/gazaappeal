@php
    use App\Enums\DonationMethodType;
@endphp

<x-layouts.admin :title="__('admin.donation_methods.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="hand-coins" :title="__('admin.donation_methods.title')" :subtitle="__('admin.donation_methods.subtitle')">
            <x-slot:actions>
                @can('donation_methods.create')
                    <x-ui.button :href="route('admin.donation-methods.create')" icon="plus">{{ __('admin.donation_methods.add') }}</x-ui.button>
                @endcan
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card :padding="false" class="overflow-hidden" x-data="bulkSelect" x-on:change="sync">
            <x-ui.filter-bar :action="route('admin.donation-methods.index')" :active="filled($search) || $type || request()->filled('visibility')">
                <x-ui.search-input :value="$search" :placeholder="__('admin.donation_methods.search')" />
                <div class="w-full sm:w-44">
                    <x-ui.select name="type" compact :options="DonationMethodType::options()" :selected="$type?->value" :placeholder="__('admin.donation_methods.all_types')" :aria-label="__('admin.donation_methods.columns.type')" />
                </div>
                @include('admin.partials.visibility-filter')
            </x-ui.filter-bar>

            <x-ui.results-count :paginator="$methods" />

            <x-ui.bulk-bar module="donation_methods" route="donation-methods" />

            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-sm">
                    <thead>
                        <tr class="border-b border-line bg-surface-2/60 text-xs font-semibold text-ink-subtle">
                            <th scope="col" class="w-10 ps-4 py-3 text-start"><x-ui.bulk-check module="donation_methods" /></th>
                            <th scope="col" class="w-14 px-4 py-3 text-start">#</th>
                            <th scope="col" class="px-6 py-3 text-start">{{ __('admin.donation_methods.columns.method') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.donation_methods.columns.type') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('ui.form.sort_order') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.common.visibility') }}</th>
                            <th scope="col" class="px-6 py-3 text-end">{{ __('ui.actions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($methods as $method)
                            <tr class="transition hover:bg-surface-hover">
                                <td class="ps-4 py-3.5"><x-ui.bulk-check module="donation_methods" :id="$method->id" /></td>
                                <td class="tabular px-4 py-3.5 text-xs font-semibold text-ink-subtle">{{ $methods->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink"><x-ui.icon :name="$method->type->icon()" :size="18" /></span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink">{{ $method->name }}</p>
                                            <p class="max-w-md truncate text-xs text-ink-subtle" @if (! $method->isBankTransfer()) dir="ltr" @endif>{{ $method->isBankTransfer() ? $method->bankDetail('bank_name') : $method->url }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3.5"><x-ui.badge tone="brand">{{ $method->type->label() }}</x-ui.badge></td>
                                <td class="tabular px-3 py-3.5 text-ink-muted">{{ $method->sort_order }}</td>
                                <td class="px-3 py-3.5">@include('admin.partials.visibility-badge', ['published' => $method->is_published])</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-0.5">
                                        @can('donation_methods.update')
                                            <x-ui.icon-button icon="pencil" tone="primary" :label="__('ui.actions.edit')" :href="route('admin.donation-methods.edit', $method)" />
                                        @endcan
                                        @can('donation_methods.delete')
                                            <x-ui.delete-button :action="route('admin.donation-methods.destroy', $method)" :name="$method->name" />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7"><x-ui.empty-state icon="hand-coins" :title="__('admin.donation_methods.empty')" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$methods" />
        </x-ui.card>
    </div>
</x-layouts.admin>
