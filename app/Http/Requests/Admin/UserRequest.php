<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

final class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');

        return $user instanceof User
            ? $this->user()->can('update', $user)
            : $this->user()->can('create', User::class);
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $editingSelf = $user instanceof User && $user->is($this->user());

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email:rfc', 'max:190', Rule::unique(User::class)->ignore($user)],
            'role_id' => ['required', 'integer', Rule::in($editingSelf ? [$user->role_id] : Role::assignableBy($this->user())->modelKeys())],
            'password' => [$user instanceof User ? 'nullable' : 'required', 'string', 'confirmed', Password::defaults()],
        ];
    }
}
