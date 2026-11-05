<x-layouts.admin :title="__('admin.users.permissions.title')">
    <x-ui.form-page icon="shield-check" :title="__('admin.users.permissions.title')" :subtitle="$user->name" :back="route('admin.users.index')" :parent="__('admin.users.title')" :parent-url="route('admin.users.index')">
        <form method="POST" action="{{ route('admin.users.permissions.update', $user) }}" class="space-y-6" novalidate>
            @csrf
            @method('PUT')

            <div class="flex flex-wrap items-center gap-4 rounded-2xl border border-line bg-surface p-5 shadow-card">
                <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-primary-soft text-base font-bold text-primary-soft-ink">{{ $user->initial() }}</span>
                <div class="min-w-0 flex-1">
                    <p class="truncate font-bold text-ink">{{ $user->name }}</p>
                    <p class="truncate text-xs text-ink-subtle" dir="ltr">{{ $user->email }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.badge tone="brand" icon="users">{{ $user->role?->name ?? __('ui.common.none') }}</x-ui.badge>
                    @if ($granted > 0)
                        <x-ui.badge tone="success" icon="plus">{{ trans_choice('admin.users.permissions.granted_count', $granted, ['count' => $granted]) }}</x-ui.badge>
                    @endif
                    @if ($revoked > 0)
                        <x-ui.badge tone="danger" icon="x">{{ trans_choice('admin.users.permissions.revoked_count', $revoked, ['count' => $revoked]) }}</x-ui.badge>
                    @endif
                </div>
            </div>

            <x-ui.alert tone="info">{{ __('admin.users.permissions.hint') }}</x-ui.alert>

            <x-ui.permission-tree :selected="$user->permissions()" :inherited="$inherited" />

            @error('permissions.*')
                <p class="flex items-center gap-1.5 text-[13px] font-medium text-danger" role="alert"><x-ui.icon name="circle-alert" :size="15" />{{ $message }}</p>
            @enderror

            <x-ui.form-actions>
                <x-ui.button type="submit" icon="check">{{ __('ui.actions.save_changes') }}</x-ui.button>
                <x-ui.cancel-button :href="route('admin.users.index')">{{ __('ui.actions.cancel') }}</x-ui.cancel-button>
            </x-ui.form-actions>
        </form>

        <x-slot:aside>
            <x-ui.form-help :title="__('admin.users.permissions.help_title')" :items="__('admin.users.permissions.help')" />
        </x-slot:aside>
    </x-ui.form-page>
</x-layouts.admin>
