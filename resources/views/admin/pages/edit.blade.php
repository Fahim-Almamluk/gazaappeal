<x-layouts.admin :title="__('admin.pages.title')">
    <div class="mx-auto max-w-6xl space-y-6">
        <x-ui.page-header icon="file-text" :title="__('admin.pages.title')" :subtitle="__('admin.pages.subtitle')" />

        <form method="POST" action="{{ route('admin.pages.update') }}" class="space-y-6" novalidate>
            @csrf
            @method('PUT')

            @foreach ($pages->sections() as $page => $groups)
                <x-ui.form-section :step="$loop->iteration" :title="__('admin.pages.sections.'.$page)" :description="__('admin.pages.sections.'.$page.'_hint')">
                    @foreach ($groups as $group => $fields)
                        @php
                            $wide = collect($fields)->contains('multiline', true);
                        @endphp

                        <h3 @class(['text-sm font-bold text-primary sm:col-span-2', 'mt-4' => ! $loop->first])>{{ __('admin.pages.groups.'.$group) }}</h3>

                        @foreach ($fields as $name => $field)
                            <div @class(['sm:col-span-2' => $wide])>
                                <x-ui.translatable
                                    :name="$name"
                                    :names="collect($pages->locales())->mapWithKeys(fn (string $locale): array => [$locale => $pages->column($name, $locale)])->all()"
                                    :label="$pages->label($field)"
                                    :value="$pages->translations($name)"
                                    :multiline="$field['multiline']"
                                    :rows="3"
                                    :maxlength="$field['max']"
                                />
                            </div>
                        @endforeach
                    @endforeach
                </x-ui.form-section>
            @endforeach

            <x-ui.form-actions :hint="__('admin.pages.hint')">
                <x-ui.button type="submit" icon="check">{{ __('ui.actions.save_changes') }}</x-ui.button>
            </x-ui.form-actions>
        </form>
    </div>
</x-layouts.admin>
