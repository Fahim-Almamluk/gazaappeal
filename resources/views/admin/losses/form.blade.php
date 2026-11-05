@php
    use App\Enums\LossIcon;

    $editing = $loss->exists;
    $title = $editing ? __('admin.losses.edit') : __('admin.losses.add');
@endphp

<x-layouts.admin :title="$title">
    <x-ui.form-page icon="zap-off" :title="$title" :subtitle="$editing ? $loss->title : __('admin.losses.form_subtitle')" :back="route('admin.losses.index')" :parent="__('admin.losses.title')" :parent-url="route('admin.losses.index')">
        <form method="POST" action="{{ $editing ? route('admin.losses.update', $loss) : route('admin.losses.store') }}" class="space-y-6" novalidate>
            @csrf
            @if ($editing) @method('PUT') @endif

            <x-ui.form-section step="1" :title="__('admin.losses.sections.content')" :description="__('admin.losses.sections.content_hint')">
                <div class="sm:col-span-2">
                    <x-ui.translatable name="title" :label="__('admin.losses.fields.title')" :value="$loss->translations('title')" :maxlength="160" required />
                </div>
                <x-ui.input name="figure" type="number" min="0" step="0.01" dir="ltr" :label="__('admin.losses.fields.figure')" :value="old('figure', $loss->figure)" required />
                <x-ui.translatable name="unit" :label="__('admin.losses.fields.unit')" :hint="__('admin.losses.fields.unit_hint')" :value="$loss->translations('unit')" :maxlength="40" required />
                <x-ui.select name="icon" :label="__('admin.losses.fields.icon')" :options="LossIcon::options()" :selected="$loss->icon?->value" :placeholder="__('admin.common.choose')" required />
                <div class="sm:col-span-2">
                    <x-ui.translatable name="description" :label="__('admin.losses.fields.description')" :value="$loss->translations('description')" multiline :rows="4" :maxlength="1000" required />
                </div>
            </x-ui.form-section>

            @include('admin.partials.publishing', ['step' => 2, 'model' => $loss])

            <x-ui.form-actions :hint="__('ui.form.required_hint')">
                <x-ui.button type="submit" icon="check">{{ $editing ? __('ui.actions.save_changes') : __('ui.actions.save') }}</x-ui.button>
                <x-ui.cancel-button :href="route('admin.losses.index')">{{ __('ui.actions.cancel') }}</x-ui.cancel-button>
            </x-ui.form-actions>
        </form>

        <x-slot:aside>
            <x-ui.form-help :title="__('admin.common.help_title')" :items="__('admin.losses.help')" />
        </x-slot:aside>
    </x-ui.form-page>
</x-layouts.admin>
