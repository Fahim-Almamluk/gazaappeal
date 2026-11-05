<x-layouts.admin :title="__('admin.users.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="users" :title="__('admin.users.title')" :subtitle="__('admin.users.subtitle')">
            <x-slot:actions>
                @can('create', App\Models\User::class)
                    <x-ui.button :href="route('admin.users.create')" icon="plus">{{ __('admin.users.add') }}</x-ui.button>
                @endcan
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.card :padding="false" class="overflow-hidden">
            <x-ui.filter-bar :action="route('admin.users.index')" :active="filled($search)">
                <x-ui.search-input :value="$search" :placeholder="__('admin.users.search')" />
            </x-ui.filter-bar>

            <x-ui.results-count :paginator="$users" />

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-sm">
                    <thead>
                        <tr class="border-b border-line bg-surface-2/60 text-xs font-semibold text-ink-subtle">
                            <th scope="col" class="w-14 px-4 py-3 text-start">#</th>
                            <th scope="col" class="px-6 py-3 text-start">{{ __('admin.users.columns.user') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.users.columns.role') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.users.columns.status') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.users.columns.last_login') }}</th>
                            <th scope="col" class="px-6 py-3 text-end">{{ __('ui.actions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($users as $user)
                            <tr class="transition hover:bg-surface-hover">
                                <td class="tabular px-4 py-3.5 text-xs font-semibold text-ink-subtle">{{ $users->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary-soft text-sm font-bold text-primary-soft-ink">{{ $user->initial() }}</span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink">
                                                {{ $user->name }}
                                                @if ($user->is(auth()->user()))
                                                    <x-ui.badge tone="brand" class="ms-1">{{ __('admin.users.you') }}</x-ui.badge>
                                                @endif
                                            </p>
                                            <p class="truncate text-xs text-ink-subtle" dir="ltr">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3.5"><x-ui.badge :tone="$user->isAdmin() ? 'brand' : 'neutral'" :icon="$user->isAdmin() ? 'shield-check' : null">{{ $user->role?->name ?? __('ui.common.none') }}</x-ui.badge></td>
                                <td class="px-3 py-3.5">
                                    <x-ui.badge :tone="$user->is_active ? 'success' : 'danger'" :icon="$user->is_active ? 'circle-check' : 'ban'">{{ $user->is_active ? __('ui.status.active') : __('ui.status.inactive') }}</x-ui.badge>
                                </td>
                                <td class="px-3 py-3.5 text-xs text-ink-subtle">
                                    @if ($user->last_login_at)
                                        <time datetime="{{ $user->last_login_at->toIso8601String() }}">{{ $user->last_login_at->diffForHumans() }}</time>
                                    @else
                                        {{ __('admin.users.never') }}
                                    @endif
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-0.5">
                                        @can('permissions', $user)
                                            <x-ui.icon-button icon="shield-check" tone="primary" :label="__('admin.users.permissions.title')" :href="route('admin.users.permissions', $user)" />
                                        @endcan
                                        @can('update', $user)
                                            <x-ui.icon-button icon="pencil" tone="primary" :label="__('ui.actions.edit')" :href="route('admin.users.edit', $user)" />
                                        @endcan
                                        @can('toggle', $user)
                                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" @confirm($user->is_active ? 'danger' : 'success', $user->is_active ? 'deactivate' : 'activate', ['name' => $user->name])>
                                                @csrf
                                                @method('PATCH')
                                                @if ($user->is_active)
                                                    <x-ui.icon-button type="submit" icon="ban" tone="danger" :label="__('admin.users.deactivate')" />
                                                @else
                                                    <x-ui.icon-button type="submit" icon="rotate-ccw" tone="success" :label="__('admin.users.activate')" />
                                                @endif
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$users" />
        </x-ui.card>
    </div>
</x-layouts.admin>
