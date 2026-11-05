<x-layouts.admin :title="__('admin.faqs.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="circle-help" :title="__('admin.faqs.title')" :subtitle="__('admin.faqs.subtitle')">
            <x-slot:actions>
                @can('faqs.create')
                    <x-ui.button :href="route('admin.faqs.create')" icon="plus">{{ __('admin.faqs.add') }}</x-ui.button>
                @endcan
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card :padding="false" class="overflow-hidden">
            <x-ui.filter-bar :action="route('admin.faqs.index')" :active="filled($search) || request()->filled('visibility')">
                <x-ui.search-input :value="$search" :placeholder="__('admin.faqs.search')" />
                @include('admin.partials.visibility-filter')
            </x-ui.filter-bar>

            <x-ui.results-count :paginator="$faqs" />

            <div class="overflow-x-auto">
                <table class="w-full min-w-[680px] text-sm">
                    <thead>
                        <tr class="border-b border-line bg-surface-2/60 text-xs font-semibold text-ink-subtle">
                            <th scope="col" class="w-14 px-4 py-3 text-start">#</th>
                            <th scope="col" class="px-6 py-3 text-start">{{ __('admin.faqs.columns.question') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('ui.form.sort_order') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.common.visibility') }}</th>
                            <th scope="col" class="px-6 py-3 text-end">{{ __('ui.actions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($faqs as $faq)
                            <tr class="transition hover:bg-surface-hover">
                                <td class="tabular px-4 py-3.5 text-xs font-semibold text-ink-subtle">{{ $faqs->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3.5">
                                    <p class="font-semibold text-ink">{{ $faq->question }}</p>
                                    <p class="max-w-xl truncate text-xs text-ink-subtle">{{ $faq->answer }}</p>
                                </td>
                                <td class="tabular px-3 py-3.5 text-ink-muted">{{ $faq->sort_order }}</td>
                                <td class="px-3 py-3.5">@include('admin.partials.visibility-badge', ['published' => $faq->is_published])</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-0.5">
                                        @can('faqs.update')
                                            <x-ui.icon-button icon="pencil" tone="primary" :label="__('ui.actions.edit')" :href="route('admin.faqs.edit', $faq)" />
                                        @endcan
                                        @can('faqs.delete')
                                            <x-ui.delete-button :action="route('admin.faqs.destroy', $faq)" :name="$faq->question" />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><x-ui.empty-state icon="circle-help" :title="__('admin.faqs.empty')" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$faqs" />
        </x-ui.card>
    </div>
</x-layouts.admin>
