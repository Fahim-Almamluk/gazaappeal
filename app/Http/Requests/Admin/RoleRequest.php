<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\ValidatesTranslations;
use App\Models\Role;
use App\Support\Permissions;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class RoleRequest extends FormRequest
{
    use ValidatesTranslations;

    public function authorize(): bool
    {
        $role = $this->route('role');

        return $role instanceof Role
            ? $this->user()->can('update', $role)
            : $this->user()->can('create', Role::class);
    }

    public function rules(): array
    {
        $role = $this->route('role');
        $rules = $this->translated('name', ['max:60']);
        $rules['name.ar'][] = function (string $attribute, mixed $value, Closure $fail) use ($role): void {
            $taken = Role::query()
                ->whereTranslation('name', (string) $value)
                ->when($role instanceof Role, fn ($query) => $query->whereKeyNot($role->getKey()))
                ->exists();

            if ($taken) {
                $fail(__('validation.unique', ['attribute' => $this->attributes()[$attribute] ?? $attribute]));
            }
        };

        return [
            ...$rules,
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'distinct', Rule::in($this->grantable())],
        ];
    }

    /**
     * @return list<string>
     */
    private function grantable(): array
    {
        $actor = $this->user();

        return $actor->isAdmin() ? app(Permissions::class)->all() : $actor->permissions();
    }
}
