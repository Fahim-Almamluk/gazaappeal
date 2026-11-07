@php
    use App\Enums\Governorate;
    use App\Enums\LookupType;
    use App\Models\Lookup;

    $editing = $damage->exists;
    $title = $editing ? __('admin.damages.edit') : __('admin.damages.add');
@endphp

<x-layouts.admin :title="$title">
    <x-ui.form-page icon="map-pin" :title="$title" :subtitle="$editing ? $damage->item : __('admin.damages.form_subtitle')" :back="route('admin.damages.index')" :parent="__('admin.damages.title')" :parent-url="route('admin.damages.index')">
        <form method="POST" action="{{ $editing ? route('admin.damages.update', $damage) : route('admin.damages.store') }}" class="space-y-6" novalidate>
            @csrf
            @if ($editing) @method('PUT') @endif

            <x-ui.form-section step="1" :title="__('admin.damages.sections.content')" :description="__('admin.damages.sections.content_hint')">
                <x-ui.select name="governorate" :label="__('admin.damages.fields.governorate')" :options="Governorate::options()" :selected="$damage->governorate?->value" :placeholder="__('admin.common.choose')" required />
                <x-ui.select name="category_id" :label="__('admin.damages.fields.category')" :options="Lookup::options(LookupType::DamageCategory, true, $damage->category_id)" :selected="$damage->category_id" :placeholder="__('admin.common.choose')" required />
                <div class="sm:col-span-2">
                    <x-ui.translatable name="item" :label="__('admin.damages.fields.item')" :value="$damage->translations('item')" :maxlength="120" required />
                </div>
                <x-ui.input name="amount" type="number" min="0" step="1" dir="ltr" :label="__('admin.damages.fields.amount')" :hint="__('admin.damages.fields.amount_hint')" :value="old('amount', $damage->amount)" required />
                <x-ui.input name="sort_order" type="number" min="0" max="9999" :label="__('ui.form.sort_order')" :hint="__('ui.form.sort_order_hint')" :value="old('sort_order', $damage->sort_order)" required />
            </x-ui.form-section>

            <x-ui.form-actions :hint="__('ui.form.required_hint')">
                <x-ui.button type="submit" icon="check">{{ $editing ? __('ui.actions.save_changes') : __('ui.actions.save') }}</x-ui.button>
                <x-ui.cancel-button :href="route('admin.damages.index')">{{ __('ui.actions.cancel') }}</x-ui.cancel-button>
            </x-ui.form-actions>
        </form>

        <x-slot:aside>
            <x-ui.form-help :title="__('admin.common.help_title')" :items="__('admin.damages.help')" />
        </x-slot:aside>
    </x-ui.form-page>
</x-layouts.admin>
