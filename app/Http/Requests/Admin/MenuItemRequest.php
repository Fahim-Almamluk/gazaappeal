<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\ValidatesTranslations;
use App\Models\MenuItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class MenuItemRequest extends FormRequest
{
    use ValidatesTranslations {
        attributes as translatedAttributes;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            ...$this->translatedAttributes(),
            'type' => __('admin.menu.fields.type'),
            'page' => __('admin.menu.fields.page'),
            'url' => __('admin.menu.fields.url'),
            'parent_id' => __('admin.menu.fields.parent'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_visible' => $this->boolean('is_visible'),
            'parent_id' => $this->filled('parent_id') ? $this->input('parent_id') : null,
        ]);
    }

    public function rules(): array
    {
        $type = $this->input('type');

        return [
            'type' => ['required', Rule::in(['page', 'url', 'group'])],
            'page' => [Rule::requiredIf($type === 'page'), 'nullable', Rule::in(array_keys(config('navigation.pages')))],
            'url' => [Rule::requiredIf($type === 'url'), 'nullable', 'url:https', 'max:255'],
            ...$this->translated('label', ['max:60'], $type !== 'page'),
            'parent_id' => ['nullable', 'integer', Rule::exists('menu_items', 'id')->whereNull('parent_id')],
            'is_visible' => ['boolean'],
        ];
    }

    /**
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $item = $this->route('menuItem');
            $parentId = $this->integer('parent_id') ?: null;

            if ($item instanceof MenuItem && $parentId !== null && ($parentId === $item->id || $item->children()->exists())) {
                $validator->errors()->add('parent_id', __('admin.menu.errors.nested'));

                return;
            }

            if ($parentId !== null && $this->input('type') === 'group') {
                $validator->errors()->add('type', __('admin.menu.errors.group_child'));

                return;
            }

            $limit = config($parentId === null ? 'navigation.limits.roots' : 'navigation.limits.children');
            $siblings = MenuItem::query()
                ->where('parent_id', $parentId)
                ->when($item instanceof MenuItem, fn ($query) => $query->whereKeyNot($item->id))
                ->count();

            if ($siblings >= $limit) {
                $validator->errors()->add('parent_id', __('admin.menu.errors.limit', ['max' => $limit]));
            }
        }];
    }
}
