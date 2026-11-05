@php
    $editing = $faq->exists;
    $title = $editing ? __('admin.faqs.edit') : __('admin.faqs.add');
@endphp

<x-layouts.admin :title="$title">
    <x-ui.form-page icon="circle-help" :title="$title" :subtitle="__('admin.faqs.form_subtitle')" :back="route('admin.faqs.index')" :parent="__('admin.faqs.title')" :parent-url="route('admin.faqs.index')">
        <form method="POST" action="{{ $editing ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" class="space-y-6" novalidate>
            @csrf
            @if ($editing) @method('PUT') @endif

            <x-ui.form-section step="1" :title="__('admin.faqs.sections.content')">
                <div class="sm:col-span-2">
                    <x-ui.translatable name="question" :label="__('admin.faqs.fields.question')" :value="$faq->translations('question')" :maxlength="255" required />
                </div>
                <div class="sm:col-span-2">
                    <x-ui.translatable name="answer" :label="__('admin.faqs.fields.answer')" :hint="__('admin.common.body_hint')" :value="$faq->translations('answer')" multiline :rows="6" :maxlength="5000" required />
                </div>
            </x-ui.form-section>

            @include('admin.partials.publishing', ['step' => 2, 'model' => $faq])

            <x-ui.form-actions :hint="__('ui.form.required_hint')">
                <x-ui.button type="submit" icon="check">{{ $editing ? __('ui.actions.save_changes') : __('ui.actions.save') }}</x-ui.button>
                <x-ui.cancel-button :href="route('admin.faqs.index')">{{ __('ui.actions.cancel') }}</x-ui.cancel-button>
            </x-ui.form-actions>
        </form>
    </x-ui.form-page>
</x-layouts.admin>
