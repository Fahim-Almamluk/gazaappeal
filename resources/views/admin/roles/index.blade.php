<x-layouts.admin :title="__('admin.roles.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="shield-check" :title="__('admin.roles.title')" :subtitle="__('admin.roles.subtitle')">
            <x-slot:actions>
                @can('create', App\Models\Role::class)
                    <x-ui.button :href="route('admin.roles.create')" icon="plus">{{ __('admin.roles.add') }}</x-ui.button>
                @endcan
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($roles as $role)
                @php
                    $count = $role->permissionCount();
                    $share = $total > 0 ? (int) round($count / $total * 100) : 0;
                @endphp

                <article @class([
                    'relative flex flex-col overflow-hidden rounded-card border bg-surface shadow-card transition hover:shadow-float',
                    'border-primary/30' => $role->is_system,
                    'border-line' => ! $role->is_system,
                ])>
                    @if ($role->is_system)
                        <span class="absolute inset-x-0 top-0 h-1 bg-linear-to-l from-brand-400 to-brand-700"></span>
                    @endif

                    <div class="flex items-start gap-3 p-5">
                        <span @class([
                            'flex size-11 shrink-0 items-center justify-center rounded-xl',
                            'bg-primary text-primary-fg' => $role->is_system,
                            'bg-primary-soft text-primary-soft-ink' => ! $role->is_system,
                        ])>
                            <x-ui.icon :name="$role->is_system ? 'shield-check' : 'users'" :size="20" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <h2 class="truncate text-base font-bold text-ink">{{ $role->name }}</h2>
                            <p class="mt-0.5 text-xs text-ink-subtle">{{ trans_choice('admin.roles.users_count', $role->users_count, ['count' => $role->users_count]) }}</p>
                        </div>
                        @if ($role->is_system)
                            <x-ui.badge tone="brand" icon="lock">{{ __('admin.roles.system') }}</x-ui.badge>
                        @endif
                    </div>

                    <div class="mt-auto space-y-2 border-t border-line px-5 py-4">
                        <div class="flex items-center justify-between text-xs font-semibold">
                            <span class="text-ink-subtle">{{ __('admin.roles.columns.permissions') }}</span>
                            <span class="tabular text-ink" dir="ltr">{{ $count }} / {{ $total }}</span>
                        </div>
                        <x-ui.progress :value="$share" />
                    </div>

                    <div class="flex items-center justify-end gap-0.5 border-t border-line bg-surface-2/50 px-3 py-2">
                        @can('update', $role)
                            <x-ui.icon-button icon="pencil" tone="primary" :label="__('ui.actions.edit')" :href="route('admin.roles.edit', $role)" />
                        @endcan
                        @can('delete', $role)
                            <x-ui.delete-button :action="route('admin.roles.destroy', $role)" :name="$role->name" />
                        @endcan
                        @if ($role->is_system)
                            <span class="px-2 py-1.5 text-xs text-ink-subtle">{{ __('admin.roles.system_hint') }}</span>
                        @elseif ($role->users_count > 0 && auth()->user()->can('roles.delete'))
                            <span class="px-2 py-1.5 text-xs text-ink-subtle">{{ __('admin.roles.delete_blocked') }}</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
