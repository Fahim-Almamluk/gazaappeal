@php
    $editing = $story->exists;
    $title = $editing ? __('admin.stories.edit') : __('admin.stories.add');
@endphp

<x-layouts.admin :title="$title">
    <x-ui.form-page icon="award" :title="$title" :subtitle="$editing ? $story->title : __('admin.stories.form_subtitle')" :back="route('admin.stories.index')" :parent="__('admin.stories.title')" :parent-url="route('admin.stories.index')">
        <form method="POST" action="{{ $editing ? route('admin.stories.update', $story) : route('admin.stories.store') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
            @csrf
            @if ($editing) @method('PUT') @endif

            <x-ui.form-section step="1" :title="__('admin.stories.sections.content')" :description="__('admin.stories.sections.content_hint')">
                <div class="sm:col-span-2">
                    <x-ui.translatable name="title" :label="__('admin.stories.fields.title')" :value="$story->translations('title')" :maxlength="160" required />
                </div>
                <div class="sm:col-span-2">
                    <x-ui.translatable name="summary" :label="__('admin.stories.fields.summary')" :value="$story->translations('summary')" :maxlength="300" required />
                </div>
                <div class="sm:col-span-2">
                    <x-ui.translatable name="body" :label="__('admin.stories.fields.body')" :hint="__('admin.common.body_hint')" :value="$story->translations('body')" multiline :rows="9" :maxlength="20000" required />
                </div>
                <div class="sm:col-span-2">
                    <x-ui.select name="project_id" :label="__('admin.stories.fields.project')" :hint="__('admin.stories.fields.project_hint')" :options="$projects" :selected="$story->project_id" :placeholder="__('admin.stories.fields.no_project')" />
                </div>
                <x-ui.image-input :label="__('admin.stories.fields.image')" :current="$story->imageUrl()" />
            </x-ui.form-section>

            @include('admin.partials.publishing', ['step' => 2, 'model' => $story, 'sortable' => false])

            <x-ui.form-actions :hint="__('ui.form.required_hint')">
                <x-ui.button type="submit" icon="check">{{ $editing ? __('ui.actions.save_changes') : __('ui.actions.save') }}</x-ui.button>
                <x-ui.cancel-button :href="route('admin.stories.index')">{{ __('ui.actions.cancel') }}</x-ui.cancel-button>
            </x-ui.form-actions>
        </form>

        <x-slot:aside>
            <x-ui.form-help :title="__('admin.common.help_title')" :items="__('admin.stories.help')" />
        </x-slot:aside>
    </x-ui.form-page>
</x-layouts.admin>
