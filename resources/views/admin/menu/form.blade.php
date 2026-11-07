@php
    $editing = $item->exists;
    $title = $editing ? __('admin.menu.edit') : __('admin.menu.add');
    $type = old('type', $editing ? $item->type() : 'page');
    $pages = collect(config('navigation.pages'))->map(fn (array $page): string => __($page['label']))->all();
    $types = [
        'page' => __('admin.menu.type.page'),
        'url' => __('admin.menu.type.url'),
        'group' => __('admin.menu.type.group'),
    ];
@endphp

<x-layouts.admin :title="$title">
    <x-ui.form-page icon="list-tree" :title="$title" :subtitle="$editing ? $item->title() : __('admin.menu.form_subtitle')" :back="route('admin.menu.index')" :parent="__('admin.menu.title')" :parent-url="route('admin.menu.index')">
        <form method="POST" action="{{ $editing ? route('admin.menu.update', $item) : route('admin.menu.store') }}" class="space-y-6" x-data="menuFields" novalidate>
            @csrf
            @if ($editing) @method('PUT') @endif

            <x-ui.form-section step="1" :title="__('admin.menu.sections.target')" :description="__('admin.menu.sections.target_hint')">
                <x-ui.select name="type" :label="__('admin.menu.fields.type')" :options="$types" :selected="$type" x-ref="type" x-on:change="sync" required />
                <x-ui.select name="parent_id" :label="__('admin.menu.fields.parent')" :hint="__('admin.menu.fields.parent_hint')" :options="$parents" :selected="$item->parent_id" :placeholder="__('admin.menu.fields.no_parent')" />
                <div x-show="isPage" @unless ($type === 'page') x-cloak @endunless class="sm:col-span-2">
                    <x-ui.select name="page" :label="__('admin.menu.fields.page')" :options="$pages" :selected="$item->page" :placeholder="__('admin.common.choose')" />
                </div>
                <div x-show="isUrl" @unless ($type === 'url') x-cloak @endunless class="sm:col-span-2">
                    <x-ui.input name="url" type="url" dir="ltr" placeholder="https://" :label="__('admin.menu.fields.url')" :value="old('url', $item->url)" maxlength="255" />
                </div>
            </x-ui.form-section>

            <x-ui.form-section step="2" :title="__('admin.menu.sections.label')" :description="__('admin.menu.sections.label_hint')">
                <div class="sm:col-span-2">
                    <x-ui.translatable name="label" :label="__('admin.menu.fields.label')" :hint="__('admin.menu.fields.label_hint')" :value="$item->translations('label')" :maxlength="60" />
                </div>
                <div class="sm:col-span-2">
                    <x-ui.toggle name="is_visible" :label="__('admin.menu.fields.visible')" :description="__('admin.menu.fields.visible_hint')" :checked="$item->is_visible" />
                </div>
            </x-ui.form-section>

            <x-ui.form-actions :hint="__('ui.form.required_hint')">
                <x-ui.button type="submit" icon="check">{{ $editing ? __('ui.actions.save_changes') : __('ui.actions.save') }}</x-ui.button>
                <x-ui.cancel-button :href="route('admin.menu.index')">{{ __('ui.actions.cancel') }}</x-ui.cancel-button>
            </x-ui.form-actions>
        </form>
    </x-ui.form-page>
</x-layouts.admin>
