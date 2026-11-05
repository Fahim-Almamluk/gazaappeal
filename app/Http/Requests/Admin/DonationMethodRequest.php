<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\DonationMethodType;
use App\Http\Requests\Concerns\ValidatesTranslations;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class DonationMethodRequest extends FormRequest
{
    use ValidatesTranslations;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_published' => $this->boolean('is_published')]);
    }

    public function rules(): array
    {
        $isLink = (int) $this->input('type') === DonationMethodType::Link->value;
        $bankRequired = $isLink ? 'nullable' : 'required';

        return [
            'type' => ['required', Rule::enum(DonationMethodType::class)],
            ...$this->translated('name', ['max:120']),
            ...$this->translated('description', ['max:300'], required: false),
            'url' => [$isLink ? 'required' : 'nullable', 'url:https', 'max:255'],
            'bank_details.bank_name' => [$bankRequired, 'string', 'max:120'],
            'bank_details.beneficiary' => [$bankRequired, 'string', 'max:160'],
            'bank_details.branch' => ['nullable', 'string', 'max:120'],
            'bank_details.address' => ['nullable', 'string', 'max:190'],
            'bank_details.bank_name_en' => ['nullable', 'string', 'max:120'],
            'bank_details.beneficiary_en' => ['nullable', 'string', 'max:160'],
            'bank_details.branch_en' => ['nullable', 'string', 'max:120'],
            'bank_details.address_en' => ['nullable', 'string', 'max:190'],
            'bank_details.account_number' => [$bankRequired, 'string', 'max:60'],
            'bank_details.swift_code' => ['nullable', 'string', 'regex:/^[A-Za-z0-9]{8,11}$/'],
            'bank_details.iban' => ['nullable', 'string', 'regex:/^[A-Za-z]{2}[0-9A-Za-z ]{10,40}$/'],
            'is_published' => ['boolean'],
            'sort_order' => ['required', 'integer', 'between:0,9999'],
        ];
    }

    public function payload(): array
    {
        $data = $this->safe()->except('bank_details');
        $isBank = (int) $data['type'] === DonationMethodType::BankTransfer->value;

        return [
            ...$data,
            'url' => $isBank ? null : ($data['url'] ?? null),
            'bank_details' => $isBank ? array_filter($this->validated('bank_details', [])) : null,
        ];
    }
}
