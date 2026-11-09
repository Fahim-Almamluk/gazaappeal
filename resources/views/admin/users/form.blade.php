@php
    use App\Models\Role;

    $editing = $user->exists;
    $editingSelf = $editing && $user->is(auth()->user());
    $title = $editing ? __('admin.users.edit') : __('admin.users.add');
@endphp

<x-layouts.admin :title="$title">
    <x-ui.form-page icon="users" :title="$title" :subtitle="$editing ? $user->email : __('admin.users.form_subtitle')" :back="route('admin.users.index')" :parent="__('admin.users.title')" :parent-url="route('admin.users.index')">
        <form method="POST" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" class="space-y-6" novalidate>
            @csrf
            @if ($editing) @method('PUT') @endif

            <x-ui.form-section step="1" :title="__('admin.users.sections.identity')" :description="__('admin.users.sections.identity_hint')">
                <x-ui.input name="name" :label="__('admin.users.fields.name')" :value="old('name', $user->name)" maxlength="120" autocomplete="off" required />
                <x-ui.input name="email" type="email" dir="ltr" :label="__('admin.users.fields.email')" :value="old('email', $user->email)" maxlength="190" autocomplete="off" required />
                @if ($editingSelf)
                    <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                    <x-ui.field name="role_id" :label="__('admin.users.fields.role')" :hint="__('admin.users.fields.role_locked')">
                        <p class="flex h-12 items-center rounded-xl border border-line bg-surface-2 px-4 text-[15px] text-ink-muted">{{ $user->role?->name }}</p>
                    </x-ui.field>
                @else
                    <x-ui.select name="role_id" :label="__('admin.users.fields.role')" :hint="__('admin.users.fields.role_hint')" :options="Role::assignableBy(auth()->user())->mapWithKeys(fn (Role $role): array => [$role->id => $role->name])->all()" :selected="$user->role_id" :placeholder="__('admin.common.choose')" required />
                @endif
            </x-ui.form-section>

            <x-ui.form-section step="2" :title="__('admin.users.sections.password')" :description="$editingSelf ? null : ($editing ? __('admin.users.sections.password_edit_hint') : __('admin.users.sections.password_hint'))">
                @if ($editingSelf)
                    <p class="rounded-xl border border-line bg-surface-2 px-4 py-3 text-sm text-ink-muted">
                        {{ __('admin.users.sections.password_self') }}
                        <a href="{{ route('admin.profile.edit') }}" class="font-semibold text-primary hover:underline">{{ __('admin.users.sections.password_self_link') }}</a>
                    </p>
                @elseif ($editing)
                    <x-ui.password name="password" :label="__('admin.users.fields.password')" autocomplete="new-password" strength />
                    <x-ui.password name="password_confirmation" :label="__('admin.users.fields.password_confirmation')" autocomplete="new-password" />
                @else
                    <x-ui.password name="password" :label="__('admin.users.fields.password')" autocomplete="new-password" required strength />
                    <x-ui.password name="password_confirmation" :label="__('admin.users.fields.password_confirmation')" autocomplete="new-password" required />
                @endif
            </x-ui.form-section>

            <x-ui.form-actions :hint="__('ui.form.required_hint')">
                <x-ui.button type="submit" icon="check">{{ $editing ? __('ui.actions.save_changes') : __('ui.actions.save') }}</x-ui.button>
                <x-ui.cancel-button :href="route('admin.users.index')">{{ __('ui.actions.cancel') }}</x-ui.cancel-button>
            </x-ui.form-actions>
        </form>

        <x-slot:aside>
            <x-ui.form-help :title="__('admin.users.help_title')" :items="__('admin.users.help')" />
        </x-slot:aside>
    </x-ui.form-page>
</x-layouts.admin>
