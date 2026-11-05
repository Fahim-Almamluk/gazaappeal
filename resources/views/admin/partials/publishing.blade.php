<x-ui.form-section :step="$step" :title="__('ui.form.publishing')" :description="__('ui.form.publishing_hint')">
    <div class="sm:col-span-2">
        <x-ui.toggle name="is_published" :label="__('ui.form.is_published')" :description="__('ui.form.is_published_hint')" :checked="$model->is_published" />
    </div>
    @if ($sortable ?? true)
        <x-ui.input name="sort_order" type="number" min="0" max="9999" :label="__('ui.form.sort_order')" :hint="__('ui.form.sort_order_hint')" :value="old('sort_order', $model->sort_order)" required />
    @endif
</x-ui.form-section>
