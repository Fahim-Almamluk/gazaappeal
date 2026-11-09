@php
    use App\Enums\LookupType;
    use App\Enums\Period;
    use App\Models\Lookup;
    use App\Enums\VolunteerStatus;
@endphp

<x-layouts.admin :title="__('admin.volunteers.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="user-plus" :title="__('admin.volunteers.title')" :subtitle="__('admin.volunteers.subtitle')">
            <x-slot:actions>
                <x-ui.button variant="excel" icon="download" :href="route('admin.volunteers.export', request()->query())">{{ __('admin.export.button') }}</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card :padding="false" class="overflow-hidden">
            <x-ui.filter-bar :action="route('admin.volunteers.index')" :active="filled($search) || $status || $expertise || $period">
                <x-ui.search-input :value="$search" :placeholder="__('admin.volunteers.search')" />
                <div class="w-full sm:w-44">
                    <x-ui.select name="status" compact :options="VolunteerStatus::options()" :selected="$status?->value" :placeholder="__('admin.common.all_statuses')" :aria-label="__('admin.volunteers.columns.status')" />
                </div>
                <div class="w-full sm:w-64">
                    <x-ui.select name="expertise" compact :options="Lookup::options(LookupType::VolunteerExpertise)" :selected="$expertise" :placeholder="__('admin.volunteers.all_expertise')" :aria-label="__('admin.volunteers.columns.expertise')" />
                </div>
                <div class="w-full sm:w-44">
                    <x-ui.select name="period" compact :options="Period::options()" :selected="$period?->value" :placeholder="__('admin.common.all_periods')" :aria-label="__('admin.common.received_at')" />
                </div>
            </x-ui.filter-bar>

            <x-ui.results-count :paginator="$volunteers" />

            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px] text-sm">
                    <thead>
                        <tr class="border-b border-line bg-surface-2/60 text-xs font-semibold text-ink-subtle">
                            <th scope="col" class="w-14 px-4 py-3 text-start">#</th>
                            <th scope="col" class="px-6 py-3 text-start"><x-ui.sort-link column="name">{{ __('admin.volunteers.columns.volunteer') }}</x-ui.sort-link></th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.volunteers.columns.expertise') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.volunteers.columns.country') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.volunteers.columns.status') }}</th>
                            <th scope="col" class="px-3 py-3 text-start"><x-ui.sort-link column="created_at" first="desc">{{ __('admin.common.received_at') }}</x-ui.sort-link></th>
                            <th scope="col" class="px-6 py-3 text-end">{{ __('ui.actions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($volunteers as $volunteer)
                            <tr class="transition hover:bg-surface-hover">
                                <td class="tabular px-4 py-3.5 text-xs font-semibold text-ink-subtle">{{ $volunteers->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3.5">
                                    <p class="font-semibold text-ink">{{ $volunteer->name }}</p>
                                    <p class="text-xs text-ink-subtle"><bdi dir="ltr">{{ $volunteer->email }}</bdi></p>
                                </td>
                                <td class="px-3 py-3.5">
                                    <span class="inline-flex items-center gap-2 text-ink-muted"><x-ui.icon :name="$volunteer->expertise->displayIcon()" :size="16" class="text-primary" />{{ $volunteer->expertise->name }}</span>
                                    <p class="tabular text-xs text-ink-subtle">{{ trans_choice('admin.volunteers.years', $volunteer->experience_years) }}</p>
                                </td>
                                <td class="px-3 py-3.5 text-ink-muted">{{ $volunteer->country }}</td>
                                <td class="px-3 py-3.5"><x-ui.badge :tone="$volunteer->status->tone()" :icon="$volunteer->status->icon()">{{ $volunteer->status->label() }}</x-ui.badge></td>
                                <td class="px-3 py-3.5 text-xs text-ink-subtle"><time datetime="{{ $volunteer->created_at->toIso8601String() }}">{{ $volunteer->created_at->diffForHumans() }}</time></td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end">
                                        <x-ui.icon-button icon="eye" tone="primary" :label="__('ui.actions.view')" :href="route('admin.volunteers.show', $volunteer)" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7"><x-ui.empty-state icon="user-plus" :title="__('admin.volunteers.empty')" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$volunteers" />
        </x-ui.card>
    </div>
</x-layouts.admin>
