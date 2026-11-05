<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UserPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');

        return $user instanceof User && $this->user()->can('permissions', $user);
    }

    public function rules(): array
    {
        return [
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

        if ($actor->isAdmin()) {
            return app(Permissions::class)->all();
        }

        return array_values(array_unique([...$actor->permissions(), ...($this->route('user')->role?->permissions() ?? [])]));
    }
}
