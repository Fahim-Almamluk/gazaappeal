<x-layouts.admin :title="__('admin.roles.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="shield-check" :title="__('admin.roles.title')" :subtitle="__('admin.roles.subtitle')">
            <x-slot:actions>
                @can('create', App\Models\Role::class)
                    <x-ui.button :href="route('admin.roles.create')" icon="plus">{{ __('admin.roles.add') }}</x-ui.button>
                @endcan
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card :padding="false" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-sm">
                    <thead>
                        <tr class="border-b border-line bg-surface-2/60 text-xs font-semibold text-ink-subtle">
                            <th scope="col" class="w-14 px-4 py-3 text-start">#</th>
                            <th scope="col" class="px-6 py-3 text-start">{{ __('admin.roles.columns.name') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.roles.columns.permissions') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.roles.columns.users') }}</th>
                            <th scope="col" class="px-6 py-3 text-end">{{ __('ui.actions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($roles as $role)
                            @php
                                $count = $role->permissionCount();
                                $share = $total > 0 ? (int) round($count / $total * 100) : 0;
                            @endphp
                            <tr class="transition hover:bg-surface-hover">
                                <td class="tabular px-4 py-3.5 text-xs font-semibold text-ink-subtle">{{ $loop->iteration }}</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span @class([
                                            'flex size-10 shrink-0 items-center justify-center rounded-xl',
                                            'bg-primary text-primary-fg' => $role->is_system,
                                            'bg-primary-soft text-primary-soft-ink' => ! $role->is_system,
                                        ])>
                                            <x-ui.icon :name="$role->is_system ? 'shield-check' : 'users'" :size="18" />
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink">{{ $role->name }}</p>
                                            @if ($role->is_system)
                                                <p class="truncate text-xs text-ink-subtle">{{ __('admin.roles.system_hint') }}</p>
                                            @endif
                                        </div>
                                        @if ($role->is_system)
                                            <x-ui.badge tone="brand" icon="lock" class="ms-1">{{ __('admin.roles.system') }}</x-ui.badge>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3.5">
                                    <div class="flex w-44 items-center gap-3">
                                        <x-ui.progress :value="$share" class="h-1.5 flex-1" />
                                        <span class="tabular shrink-0 text-xs font-semibold text-ink" dir="ltr">{{ $count }} / {{ $total }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-3.5 text-ink-muted">{{ trans_choice('admin.roles.users_count', $role->users_count, ['count' => $role->users_count]) }}</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-0.5">
                                        @can('update', $role)
                                            <x-ui.icon-button icon="pencil" tone="primary" :label="__('ui.actions.edit')" :href="route('admin.roles.edit', $role)" />
                                        @endcan
                                        @can('delete', $role)
                                            <x-ui.delete-button :action="route('admin.roles.destroy', $role)" :name="$role->name" />
                                        @elseif (! $role->is_system && $role->users_count > 0 && auth()->user()->can('roles.delete'))
                                            <x-ui.icon-button icon="lock" :label="__('admin.roles.delete_blocked')" />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</x-layouts.admin>
