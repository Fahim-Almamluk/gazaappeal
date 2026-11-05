<div class="w-full sm:w-44">
    <x-ui.select
        name="visibility"
        compact
        :options="['published' => __('ui.status.published'), 'hidden' => __('ui.status.hidden')]"
        :selected="request('visibility')"
        :placeholder="__('admin.common.all_visibility')"
        :aria-label="__('admin.common.visibility')"
    />
</div>
