@php
    $editing = $item->exists;
    $title = $editing ? __('admin.gallery.edit') : __('admin.gallery.add');
@endphp

<x-layouts.admin :title="$title">
    <x-ui.form-page icon="images" :title="$title" :subtitle="$editing ? $item->caption : __('admin.gallery.form_subtitle')" :back="route('admin.gallery.index')" :parent="__('admin.gallery.title')" :parent-url="route('admin.gallery.index')">
        <form method="POST" action="{{ $editing ? route('admin.gallery.update', $item) : route('admin.gallery.store') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
            @csrf
            @if ($editing) @method('PUT') @endif

            <x-ui.form-section step="1" :title="__('admin.gallery.sections.image')" :description="__('admin.gallery.sections.image_hint')">
                <x-ui.image-input :label="__('admin.gallery.fields.image')" :current="$item->imageUrl()" :required="! $editing" />
                <div class="sm:col-span-2">
                    <x-ui.translatable name="caption" :label="__('admin.gallery.fields.caption')" :value="$item->translations('caption')" :maxlength="160" required />
                </div>
            </x-ui.form-section>

            @include('admin.partials.publishing', ['step' => 2, 'model' => $item])

            <x-ui.form-actions :hint="__('ui.form.required_hint')">
                <x-ui.button type="submit" icon="check">{{ $editing ? __('ui.actions.save_changes') : __('ui.actions.save') }}</x-ui.button>
                <x-ui.cancel-button :href="route('admin.gallery.index')">{{ __('ui.actions.cancel') }}</x-ui.cancel-button>
            </x-ui.form-actions>
        </form>
    </x-ui.form-page>
</x-layouts.admin>
