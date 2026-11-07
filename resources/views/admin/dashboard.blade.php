@php
    $now = now();
    $canEditProjects = $user->hasPermission('projects.update');
    $fundingRate = $stats['goal'] > 0 ? (int) min(100, round($stats['raised'] / $stats['goal'] * 100)) : null;
@endphp

<x-layouts.admin :title="__('admin.dashboard.title')">
    <div class="space-y-8">
        <section class="relative isolate overflow-hidden rounded-panel bg-linear-to-l from-brand-700 via-brand-600 to-brand-500 p-7 text-white sm:p-9">
            <div class="pointer-events-none absolute -top-24 start-10 -z-10 size-72 rounded-full bg-brand-300/30 blur-3xl"></div>
            <div class="grain pointer-events-none absolute inset-0 -z-10 opacity-[0.15] mix-blend-overlay"></div>

            <div class="dot-grid pointer-events-none absolute inset-0 -z-10 opacity-30 [mask-image:linear-gradient(to_left,transparent_35%,#000)] ltr:[mask-image:linear-gradient(to_right,transparent_35%,#000)]"></div>

            <x-brand.power-line class="pointer-events-none absolute bottom-0 end-4 -z-10 hidden h-auto w-[36%] max-w-[460px] md:block lg:end-8" />

            <div class="max-w-xl space-y-4 md:max-w-[58%]">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <x-ui.icon name="calendar-days" :size="15" />
                    {{ $now->translatedFormat(__('admin.dashboard.date_format')) }}
                </span>

                <h1 class="text-[28px] leading-tight font-extrabold tracking-tight sm:text-[32px]">
                    {{ __($now->hour < 12 ? 'admin.dashboard.greeting.morning' : 'admin.dashboard.greeting.evening', ['name' => $user->name]) }}
                </h1>

                <p class="text-white/70">{{ __('admin.dashboard.intro') }}</p>

                <span class="inline-flex items-center gap-1.5 rounded-full bg-white py-1.5 ps-2.5 pe-3.5 text-xs font-bold text-brand-700 shadow-[0_8px_20px_-8px_rgb(0_0_0/0.4)]">
                    <x-ui.icon name="shield-check" :size="15" />
                    {{ $user->role?->name }}
                </span>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @can('projects.view')
                <x-ui.kpi :label="__('admin.dashboard.stats.projects')" :value="number_format($stats['projects'])" icon="solar-panel" />
                <x-ui.kpi :label="__('admin.dashboard.stats.raised')" :value="\App\Support\Money::full($stats['raised'])" icon="hand-coins" tone="success" :source="$fundingRate !== null ? __('admin.dashboard.stats.of_goal', ['rate' => $fundingRate]) : null" />
            @endcan
            @can('volunteers.view')
                <x-ui.kpi :label="__('admin.dashboard.stats.volunteers')" :value="number_format($stats['volunteers'])" icon="user-plus" :tone="$stats['volunteers'] > 0 ? 'warning' : 'brand'" />
            @endcan
            @can('messages.view')
                <x-ui.kpi :label="__('admin.dashboard.stats.messages')" :value="number_format($stats['messages'])" icon="mail" :tone="$stats['messages'] > 0 ? 'warning' : 'brand'" />
            @endcan
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
            @can('projects.view')
                <x-ui.panel :title="__('admin.dashboard.urgent')" icon="zap" flush>
                    <x-slot:action>
                        <x-ui.button variant="soft" size="sm" :href="route('admin.projects.index')">{{ __('admin.dashboard.all') }}</x-ui.button>
                    </x-slot:action>

                    @forelse ($projects as $project)
                        @php($progress = $project->progress())
                        <a href="{{ $canEditProjects ? route('admin.projects.edit', $project) : route('projects.show', $project) }}" class="flex items-center gap-4 border-b border-line px-5 py-4 transition last:border-b-0 hover:bg-surface-hover">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink"><x-ui.icon name="solar-panel" :size="18" /></span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate font-semibold text-ink">{{ $project->title }}</span>
                                @if ($progress !== null)
                                    <span class="mt-2 flex items-center gap-3">
                                        <x-ui.progress :value="$progress" class="max-w-60" />
                                        <span class="tabular text-xs font-bold text-primary">{{ $progress }}%</span>
                                    </span>
                                @else
                                    <span class="block text-xs text-ink-subtle">{{ __('admin.projects.no_goal') }}</span>
                                @endif
                            </span>
                            <x-ui.lookup-badge :lookup="$project->status" />
                        </a>
                    @empty
                        <x-ui.empty-state icon="solar-panel" :title="__('admin.dashboard.no_urgent')" />
                    @endforelse
                </x-ui.panel>
            @endcan

            <div class="space-y-6">
                @can('volunteers.view')
                    <x-ui.panel :title="__('admin.dashboard.latest_volunteers')" icon="user-plus" flush>
                        <x-slot:action>
                            <x-ui.button variant="soft" size="sm" :href="route('admin.volunteers.index')">{{ __('admin.dashboard.all') }}</x-ui.button>
                        </x-slot:action>

                        @forelse ($volunteers as $volunteer)
                            <a href="{{ route('admin.volunteers.show', $volunteer) }}" class="flex items-center gap-3 border-b border-line px-5 py-3 transition last:border-b-0 hover:bg-surface-hover">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary-soft-ink"><x-ui.icon :name="$volunteer->expertise->displayIcon()" :size="16" /></span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-ink">{{ $volunteer->name }}</span>
                                    <span class="block truncate text-xs text-ink-subtle">{{ $volunteer->expertise->name }}</span>
                                </span>
                                <x-ui.badge :tone="$volunteer->status->tone()">{{ $volunteer->status->label() }}</x-ui.badge>
                            </a>
                        @empty
                            <x-ui.empty-state icon="user-plus" :title="__('admin.volunteers.empty')" class="py-8" />
                        @endforelse
                    </x-ui.panel>
                @endcan

                @can('messages.view')
                    <x-ui.panel :title="__('admin.dashboard.unread_messages')" icon="mail" flush>
                        <x-slot:action>
                            <x-ui.button variant="soft" size="sm" :href="route('admin.messages.index')">{{ __('admin.dashboard.all') }}</x-ui.button>
                        </x-slot:action>

                        @forelse ($messages as $message)
                            <a href="{{ route('admin.messages.show', $message) }}" class="block border-b border-line px-5 py-3 transition last:border-b-0 hover:bg-surface-hover">
                                <span class="flex items-center justify-between gap-3">
                                    <span class="truncate text-sm font-semibold text-ink">{{ $message->name }}</span>
                                    <time class="shrink-0 text-xs text-ink-subtle" datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->diffForHumans() }}</time>
                                </span>
                                <span class="mt-0.5 block truncate text-xs text-ink-muted">{{ $message->body }}</span>
                            </a>
                        @empty
                            <x-ui.empty-state icon="mail-open" :title="__('admin.messages.inbox_zero')" class="py-8" />
                        @endforelse
                    </x-ui.panel>
                @endcan
            </div>
        </section>
    </div>
</x-layouts.admin>
