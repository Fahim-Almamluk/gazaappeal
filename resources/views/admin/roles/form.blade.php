@php
    $editing = $role->exists;
    $title = $editing ? __('admin.roles.edit') : __('admin.roles.add');
@endphp

<x-layouts.admin :title="$title">
    <x-ui.form-page icon="shield-check" :title="$title" :subtitle="$editing ? $role->name : __('admin.roles.form_subtitle')" :back="route('admin.roles.index')" :parent="__('admin.roles.title')" :parent-url="route('admin.roles.index')">
        <form method="POST" action="{{ $editing ? route('admin.roles.update', $role) : route('admin.roles.store') }}" class="space-y-6" novalidate>
            @csrf
            @if ($editing) @method('PUT') @endif

            <x-ui.form-section step="1" :title="__('admin.roles.sections.identity')" :description="__('admin.roles.sections.identity_hint')">
                <div class="sm:col-span-2">
                    <x-ui.translatable name="name" :label="__('admin.roles.fields.name')" :value="$role->translations('name')" :maxlength="60" required />
                </div>
            </x-ui.form-section>

            <x-ui.form-section step="2" :title="__('admin.roles.sections.permissions')" :description="__('admin.roles.sections.permissions_hint')">
                <div class="sm:col-span-2">
                    <x-ui.permission-tree :selected="$role->exists ? $role->permissions() : []" />
                    @error('permissions.*')
                        <p class="mt-2 flex items-center gap-1.5 text-[13px] font-medium text-danger" role="alert"><x-ui.icon name="circle-alert" :size="15" />{{ $message }}</p>
                    @enderror
                </div>
            </x-ui.form-section>

            <x-ui.form-actions :hint="__('ui.form.required_hint')">
                <x-ui.button type="submit" icon="check">{{ $editing ? __('ui.actions.save_changes') : __('ui.actions.save') }}</x-ui.button>
                <x-ui.cancel-button :href="route('admin.roles.index')">{{ __('ui.actions.cancel') }}</x-ui.cancel-button>
            </x-ui.form-actions>
        </form>

        <x-slot:aside>
            <x-ui.form-help :title="__('admin.common.help_title')" :items="__('admin.roles.help')" />
        </x-slot:aside>
    </x-ui.form-page>
</x-layouts.admin>
