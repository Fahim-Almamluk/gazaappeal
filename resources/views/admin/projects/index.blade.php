@php
    use App\Enums\ProjectStatus;
@endphp

<x-layouts.admin :title="__('admin.projects.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="solar-panel" :title="__('admin.projects.title')" :subtitle="__('admin.projects.subtitle')">
            <x-slot:actions>
                @can('projects.create')
                    <x-ui.button :href="route('admin.projects.create')" icon="plus">{{ __('admin.projects.add') }}</x-ui.button>
                @endcan
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card :padding="false" class="overflow-hidden">
            <x-ui.filter-bar :action="route('admin.projects.index')" :active="filled($search) || $status || request()->filled('visibility')">
                <x-ui.search-input :value="$search" :placeholder="__('admin.projects.search')" />
                <div class="w-full sm:w-44">
                    <x-ui.select name="status" compact :options="ProjectStatus::options()" :selected="$status?->value" :placeholder="__('admin.common.all_statuses')" :aria-label="__('admin.projects.columns.status')" />
                </div>
                @include('admin.partials.visibility-filter')
            </x-ui.filter-bar>

            <x-ui.results-count :paginator="$projects" />

            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px] text-sm">
                    <thead>
                        <tr class="border-b border-line bg-surface-2/60 text-xs font-semibold text-ink-subtle">
                            <th scope="col" class="w-14 px-4 py-3 text-start">#</th>
                            <th scope="col" class="px-6 py-3 text-start">{{ __('admin.projects.columns.project') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.projects.columns.status') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.projects.columns.funding') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.common.visibility') }}</th>
                            <th scope="col" class="px-6 py-3 text-end">{{ __('ui.actions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($projects as $project)
                            @php($progress = $project->progress())
                            <tr class="transition hover:bg-surface-hover">
                                <td class="tabular px-4 py-3.5 text-xs font-semibold text-ink-subtle">{{ $projects->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <x-ui.thumb :src="$project->imageUrl()" icon="solar-panel" />
                                        <div class="min-w-0">
                                            <p class="flex items-center gap-1.5 truncate font-semibold text-ink">
                                                {{ $project->title }}
                                                @if ($project->is_urgent)
                                                    <x-ui.badge tone="danger" icon="zap">{{ __('site.projects.urgent') }}</x-ui.badge>
                                                @endif
                                            </p>
                                            <p class="max-w-md truncate text-xs text-ink-subtle">{{ $project->summary }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3.5"><x-ui.badge :tone="$project->status->tone()" :icon="$project->status->icon()">{{ $project->status->label() }}</x-ui.badge></td>
                                <td class="px-3 py-3.5">
                                    @if ($progress !== null)
                                        <div class="flex w-40 items-center gap-2">
                                            <x-ui.progress :value="$progress" />
                                            <span class="tabular text-xs font-bold text-primary">{{ $progress }}%</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-ink-subtle">{{ __('admin.projects.no_goal') }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3.5">@include('admin.partials.visibility-badge', ['published' => $project->is_published])</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-0.5">
                                        @if ($project->is_published)
                                            <x-ui.icon-button icon="eye" :label="__('ui.actions.view')" :href="route('projects.show', $project)" target="_blank" />
                                        @endif
                                        @can('projects.update')
                                            <x-ui.icon-button icon="pencil" tone="primary" :label="__('ui.actions.edit')" :href="route('admin.projects.edit', $project)" />
                                        @endcan
                                        @can('projects.delete')
                                            <x-ui.delete-button :action="route('admin.projects.destroy', $project)" :name="$project->title" />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-ui.empty-state icon="solar-panel" :title="__('admin.projects.empty')" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$projects" />
        </x-ui.card>
    </div>
</x-layouts.admin>
