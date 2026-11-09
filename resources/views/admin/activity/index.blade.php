@php
    use App\Models\ActivityLog;

    $subjects = collect(ActivityLog::SUBJECTS)->mapWithKeys(fn (string $key): array => [$key => __("admin.activity.subjects.{$key}")])->all();
    $events = collect(ActivityLog::EVENTS)->mapWithKeys(fn (string $key): array => [$key => __("admin.activity.events.{$key}")])->all();
    $tones = ['created' => 'success', 'updated' => 'info', 'deleted' => 'danger'];
@endphp

<x-layouts.admin :title="__('admin.activity.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="clipboard-check" :title="__('admin.activity.title')" :subtitle="__('admin.activity.subtitle')" />

        <x-ui.card :padding="false" class="overflow-hidden">
            <x-ui.filter-bar :action="route('admin.activity.index')" :active="filled($search) || $user || $subject || $event">
                <x-ui.search-input :value="$search" :placeholder="__('admin.activity.search')" />
                <div class="w-full sm:w-48">
                    <x-ui.select name="user" compact :options="$users" :selected="$user" :placeholder="__('admin.activity.all_users')" :aria-label="__('admin.activity.columns.user')" />
                </div>
                <div class="w-full sm:w-48">
                    <x-ui.select name="subject" compact :options="$subjects" :selected="$subject" :placeholder="__('admin.activity.all_subjects')" :aria-label="__('admin.activity.columns.subject')" />
                </div>
                <div class="w-full sm:w-40">
                    <x-ui.select name="event" compact :options="$events" :selected="$event" :placeholder="__('admin.activity.all_events')" :aria-label="__('admin.activity.columns.event')" />
                </div>
            </x-ui.filter-bar>

            <x-ui.results-count :paginator="$logs" />

            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-sm">
                    <thead>
                        <tr class="border-b border-line bg-surface-2/60 text-xs font-semibold text-ink-subtle">
                            <th scope="col" class="w-14 px-4 py-3 text-start">#</th>
                            <th scope="col" class="px-6 py-3 text-start">{{ __('admin.activity.columns.user') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.activity.columns.event') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.activity.columns.subject') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.activity.columns.changes') }}</th>
                            <th scope="col" class="px-6 py-3 text-start">{{ __('admin.activity.columns.when') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($logs as $log)
                            <tr class="transition hover:bg-surface-hover">
                                <td class="tabular px-4 py-3.5 text-xs font-semibold text-ink-subtle">{{ $logs->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3.5 font-semibold text-ink">{{ $log->user?->name ?? __('admin.activity.unknown_user') }}</td>
                                <td class="px-3 py-3.5"><x-ui.badge :tone="$tones[$log->event] ?? 'neutral'">{{ $events[$log->event] ?? $log->event }}</x-ui.badge></td>
                                <td class="px-3 py-3.5">
                                    <p class="text-xs text-ink-subtle">{{ $subjects[$log->subject_type] ?? $log->subject_type }}</p>
                                    <p class="max-w-xs truncate font-medium text-ink">{{ $log->subject_label ?? '#'.$log->subject_id }}</p>
                                </td>
                                <td class="px-3 py-3.5 text-xs text-ink-muted">
                                    @if ($log->event === 'updated' && $log->changes)
                                        {{ collect($log->changes)->map(fn (string $field): string => trans()->has("admin.activity.fields.{$field}") ? __("admin.activity.fields.{$field}") : $field)->implode(' · ') }}
                                    @else
                                        <span class="text-ink-subtle">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-xs text-ink-subtle"><time datetime="{{ $log->created_at->toIso8601String() }}" title="{{ $log->created_at->format('Y-m-d H:i') }}">{{ $log->created_at->diffForHumans() }}</time></td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-ui.empty-state icon="clipboard-check" :title="__('admin.activity.empty')" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$logs" />
        </x-ui.card>
    </div>
</x-layouts.admin>
