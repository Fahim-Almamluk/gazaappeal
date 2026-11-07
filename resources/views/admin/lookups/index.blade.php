@php
    use App\Enums\LookupType;
@endphp

<x-layouts.admin :title="__('admin.lookups.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="tags" :title="__('admin.lookups.title')" :subtitle="__('admin.lookups.subtitle')">
            <x-slot:actions>
                @can('lookups.create')
                    <x-ui.button :href="route('admin.lookups.create', ['type' => $type->value])" icon="plus">{{ __('admin.lookups.add') }}</x-ui.button>
                @endcan
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card :padding="false" class="overflow-hidden">
            <nav class="flex gap-1 overflow-x-auto border-b border-line px-3 sm:px-4" aria-label="{{ __('admin.lookups.fields.type') }}">
                @foreach (LookupType::cases() as $tab)
                    <a
                        href="{{ route('admin.lookups.index', ['type' => $tab->value]) }}"
                        @if ($tab === $type) aria-current="page" @endif
                        @class([
                            '-mb-px inline-flex h-14 shrink-0 items-center gap-2 border-b-2 px-3 text-sm font-semibold transition',
                            'border-primary text-ink' => $tab === $type,
                            'border-transparent text-ink-muted hover:border-line-strong hover:text-ink' => $tab !== $type,
                        ])
                    >
                        <x-ui.icon :name="$tab->icon()" :size="17" @class(['text-primary' => $tab === $type]) />
                        {{ $tab->label() }}
                        <span class="tabular rounded-full bg-surface-2 px-2 py-0.5 text-xs text-ink-subtle">{{ $counts->get($tab->value, 0) }}</span>
                    </a>
                @endforeach
            </nav>

            <x-ui.filter-bar :action="route('admin.lookups.index', ['type' => $type->value])" :active="filled($search)">
                <input type="hidden" name="type" value="{{ $type->value }}">
                <x-ui.search-input :value="$search" :placeholder="__('admin.lookups.search')" />
            </x-ui.filter-bar>

            <x-ui.results-count :paginator="$lookups" />

            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-sm">
                    <thead>
                        <tr class="border-b border-line bg-surface-2/60 text-xs font-semibold text-ink-subtle">
                            <th scope="col" class="w-14 px-4 py-3 text-start">#</th>
                            <th scope="col" class="px-6 py-3 text-start">{{ __('admin.lookups.columns.name') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.lookups.columns.usage') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.users.columns.status') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('ui.form.sort_order') }}</th>
                            <th scope="col" class="px-6 py-3 text-end">{{ __('ui.actions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($lookups as $lookup)
                            <tr class="transition hover:bg-surface-hover">
                                <td class="tabular px-4 py-3.5 text-xs font-semibold text-ink-subtle">{{ $lookups->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink"><x-ui.icon :name="$lookup->displayIcon()" :size="18" /></span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink">{{ $lookup->name }}</p>
                                            <p class="truncate text-xs text-ink-subtle" dir="ltr">{{ $lookup->translations('name')['en'] ?? '' }}</p>
                                        </div>
                                        @if ($lookup->tone)
                                            <x-ui.badge :tone="$lookup->badgeTone()" class="ms-1">{{ __("admin.lookups.tones.{$lookup->tone}") }}</x-ui.badge>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3.5 text-ink-muted">{{ trans_choice('admin.lookups.usage_count', $lookup->usage_count, ['count' => $lookup->usage_count]) }}</td>
                                <td class="px-3 py-3.5">
                                    <x-ui.badge :tone="$lookup->is_active ? 'success' : 'neutral'" :icon="$lookup->is_active ? 'circle-check' : 'ban'">{{ $lookup->is_active ? __('ui.status.active') : __('ui.status.inactive') }}</x-ui.badge>
                                </td>
                                <td class="tabular px-3 py-3.5 text-ink-muted">{{ $lookup->sort_order }}</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-0.5">
                                        @can('lookups.update')
                                            <x-ui.icon-button icon="pencil" tone="primary" :label="__('ui.actions.edit')" :href="route('admin.lookups.edit', $lookup)" />
                                        @endcan
                                        @can('lookups.delete')
                                            @if ($lookup->usage_count > 0)
                                                <x-ui.icon-button icon="lock" :label="__('admin.lookups.delete_blocked')" />
                                            @else
                                                <x-ui.delete-button :action="route('admin.lookups.destroy', $lookup)" :name="$lookup->name" />
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-ui.empty-state :icon="$type->icon()" :title="__('admin.lookups.empty')" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$lookups" />
        </x-ui.card>
    </div>
</x-layouts.admin>
