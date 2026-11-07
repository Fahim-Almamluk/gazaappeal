@php
    use App\Enums\LookupType;
    use App\Models\Lookup;

    $editing = $project->exists;
    $title = $editing ? __('admin.projects.edit') : __('admin.projects.add');
@endphp

<x-layouts.admin :title="$title">
    <x-ui.form-page icon="solar-panel" :title="$title" :subtitle="$editing ? $project->title : __('admin.projects.form_subtitle')" :back="route('admin.projects.index')" :parent="__('admin.projects.title')" :parent-url="route('admin.projects.index')">
        <form method="POST" action="{{ $editing ? route('admin.projects.update', $project) : route('admin.projects.store') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
            @csrf
            @if ($editing) @method('PUT') @endif

            <x-ui.form-section step="1" :title="__('admin.projects.sections.content')" :description="__('admin.projects.sections.content_hint')">
                <div class="sm:col-span-2">
                    <x-ui.translatable name="title" :label="__('admin.projects.fields.title')" :value="$project->translations('title')" :maxlength="160" required />
                </div>
                <div class="sm:col-span-2">
                    <x-ui.translatable name="summary" :label="__('admin.projects.fields.summary')" :hint="__('admin.projects.fields.summary_hint')" :value="$project->translations('summary')" :maxlength="300" required />
                </div>
                <div class="sm:col-span-2">
                    <x-ui.translatable name="body" :label="__('admin.projects.fields.body')" :hint="__('admin.common.body_hint')" :value="$project->translations('body')" multiline :rows="9" :maxlength="20000" required />
                </div>
                <x-ui.image-input :label="__('admin.projects.fields.image')" :current="$project->imageUrl()" />
            </x-ui.form-section>

            <x-ui.form-section step="2" :title="__('admin.projects.sections.funding')" :description="__('admin.projects.sections.funding_hint')">
                <x-ui.select name="status_id" :label="__('admin.projects.fields.status')" :options="Lookup::options(LookupType::ProjectStatus, true, $project->status_id)" :selected="$project->status_id" :placeholder="__('admin.common.choose')" required />
                <div class="hidden sm:block"></div>
                <x-ui.input name="goal_amount" type="number" min="0" step="0.01" dir="ltr" :label="__('admin.projects.fields.goal_amount')" :hint="__('admin.projects.fields.goal_hint')" :value="old('goal_amount', $project->goal_amount)" />
                <x-ui.input name="raised_amount" type="number" min="0" step="0.01" dir="ltr" :label="__('admin.projects.fields.raised_amount')" :value="old('raised_amount', $project->raised_amount ?? 0)" />
                <div class="sm:col-span-2">
                    <x-ui.toggle name="is_urgent" :label="__('admin.projects.fields.is_urgent')" :description="__('admin.projects.fields.is_urgent_hint')" :checked="$project->is_urgent" />
                </div>
            </x-ui.form-section>

            @include('admin.partials.publishing', ['step' => 3, 'model' => $project])

            <x-ui.form-actions :hint="__('ui.form.required_hint')">
                <x-ui.button type="submit" icon="check">{{ $editing ? __('ui.actions.save_changes') : __('ui.actions.save') }}</x-ui.button>
                <x-ui.cancel-button :href="route('admin.projects.index')">{{ __('ui.actions.cancel') }}</x-ui.cancel-button>
            </x-ui.form-actions>
        </form>

        <x-slot:aside>
            <x-ui.form-help :title="__('admin.common.help_title')" :items="__('admin.projects.help')" />
        </x-slot:aside>
    </x-ui.form-page>
</x-layouts.admin>
