@php
    $editing = $partner->exists;
    $title = $editing ? __('admin.partners.edit') : __('admin.partners.add');
@endphp

<x-layouts.admin :title="$title">
    <x-ui.form-page icon="handshake" :title="$title" :subtitle="$editing ? $partner->name : __('admin.partners.form_subtitle')" :back="route('admin.partners.index')" :parent="__('admin.partners.title')" :parent-url="route('admin.partners.index')">
        <form method="POST" action="{{ $editing ? route('admin.partners.update', $partner) : route('admin.partners.store') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
            @csrf
            @if ($editing) @method('PUT') @endif

            <x-ui.form-section step="1" :title="__('admin.partners.sections.identity')" :description="__('admin.partners.sections.identity_hint')">
                <x-ui.translatable name="name" :label="__('admin.partners.fields.name')" :value="$partner->translations('name')" :maxlength="120" required />
                <x-ui.input name="website" type="url" dir="ltr" :label="__('admin.partners.fields.website')" placeholder="https://" :value="old('website', $partner->website)" maxlength="255" />
                <x-ui.image-input name="logo" :label="__('admin.partners.fields.logo')" :current="$partner->imageUrl()" />
            </x-ui.form-section>

            @include('admin.partials.publishing', ['step' => 2, 'model' => $partner])

            <x-ui.form-actions :hint="__('ui.form.required_hint')">
                <x-ui.button type="submit" icon="check">{{ $editing ? __('ui.actions.save_changes') : __('ui.actions.save') }}</x-ui.button>
                <x-ui.cancel-button :href="route('admin.partners.index')">{{ __('ui.actions.cancel') }}</x-ui.cancel-button>
            </x-ui.form-actions>
        </form>
    </x-ui.form-page>
</x-layouts.admin>
