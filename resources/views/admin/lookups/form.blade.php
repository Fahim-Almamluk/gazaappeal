@php
    use App\Models\Lookup;

    $editing = $lookup->exists;
    $title = $editing ? __('admin.lookups.edit') : __('admin.lookups.add');
    $index = route('admin.lookups.index', ['type' => $lookup->type->value]);
    $currentIcon = (string) old('icon', $lookup->icon);
    $tones = collect(Lookup::TONES)->mapWithKeys(fn (string $tone): array => [$tone => __("admin.lookups.tones.{$tone}")])->all();
@endphp

<x-layouts.admin :title="$title">
    <x-ui.form-page icon="tags" :title="$title" :subtitle="$lookup->type->label()" :back="$index" :parent="__('admin.lookups.title')" :parent-url="$index">
        <form method="POST" action="{{ $editing ? route('admin.lookups.update', $lookup) : route('admin.lookups.store') }}" class="space-y-6" novalidate>
            @csrf
            @if ($editing)
                @method('PUT')
            @else
                <input type="hidden" name="type" value="{{ $lookup->type->value }}">
            @endif

            <x-ui.form-section step="1" :title="__('admin.lookups.sections.identity')" :description="__('admin.lookups.form_subtitle')">
                <div class="sm:col-span-2">
                    <x-ui.translatable name="name" :label="__('admin.lookups.fields.name')" :value="$lookup->translations('name')" :maxlength="120" required />
                </div>
            </x-ui.form-section>

            <x-ui.form-section step="2" :title="__('admin.lookups.sections.appearance')" :description="__('admin.lookups.sections.appearance_hint')">
                <fieldset class="sm:col-span-2">
                    <legend class="mb-2 text-sm font-semibold text-ink">{{ __('admin.lookups.fields.icon') }}</legend>
                    <div class="flex flex-wrap gap-2">
                        <label class="flex h-10 min-w-10 cursor-pointer items-center justify-center rounded-xl border border-line px-2 text-xs font-semibold text-ink-muted transition hover:border-line-strong hover:text-ink has-[:checked]:border-primary has-[:checked]:bg-primary-soft has-[:checked]:text-primary-soft-ink has-[:focus-visible]:ring-4 has-[:focus-visible]:ring-primary/20">
                            <input type="radio" name="icon" value="" class="sr-only" @checked($currentIcon === '')>
                            {{ __('admin.lookups.none') }}
                        </label>
                        @foreach (Lookup::ICONS as $icon)
                            <label title="{{ $icon }}" class="flex size-10 cursor-pointer items-center justify-center rounded-xl border border-line text-ink-muted transition hover:border-line-strong hover:text-ink has-[:checked]:border-primary has-[:checked]:bg-primary-soft has-[:checked]:text-primary-soft-ink has-[:focus-visible]:ring-4 has-[:focus-visible]:ring-primary/20">
                                <input type="radio" name="icon" value="{{ $icon }}" class="sr-only" @checked($currentIcon === $icon)>
                                <x-ui.icon :name="$icon" :size="18" />
                                <span class="sr-only">{{ $icon }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('icon')
                        <p class="mt-2 flex items-center gap-1.5 text-[13px] font-medium text-danger" role="alert"><x-ui.icon name="circle-alert" :size="15" />{{ $message }}</p>
                    @enderror
                </fieldset>
                <x-ui.select name="tone" :label="__('admin.lookups.fields.tone')" :options="$tones" :selected="$lookup->tone" :placeholder="__('admin.lookups.none')" />
            </x-ui.form-section>

            <x-ui.form-section step="3" :title="__('admin.lookups.sections.status')">
                <div class="sm:col-span-2">
                    <x-ui.toggle name="is_active" :label="__('admin.lookups.fields.is_active')" :description="__('admin.lookups.fields.is_active_hint')" :checked="$lookup->is_active" />
                </div>
                <x-ui.input name="sort_order" type="number" min="0" max="9999" :label="__('ui.form.sort_order')" :hint="__('ui.form.sort_order_hint')" :value="old('sort_order', $lookup->sort_order)" required />
            </x-ui.form-section>

            <x-ui.form-actions :hint="__('ui.form.required_hint')">
                <x-ui.button type="submit" icon="check">{{ $editing ? __('ui.actions.save_changes') : __('ui.actions.save') }}</x-ui.button>
                <x-ui.cancel-button :href="$index">{{ __('ui.actions.cancel') }}</x-ui.cancel-button>
            </x-ui.form-actions>
        </form>
    </x-ui.form-page>
</x-layouts.admin>
