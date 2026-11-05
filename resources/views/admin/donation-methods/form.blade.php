@php
    use App\Enums\DonationMethodType;

    $editing = $method->exists;
    $title = $editing ? __('admin.donation_methods.edit') : __('admin.donation_methods.add');
    $bank = fn (string $field): ?string => old("bank_details.{$field}", $method->bank_details[$field] ?? null);
    $bankTranslations = fn (string $field): array => ['ar' => $method->bank_details[$field] ?? null, 'en' => $method->bank_details["{$field}_en"] ?? null];
    $bankNames = fn (string $field): array => ['ar' => "bank_details[{$field}]", 'en' => "bank_details[{$field}_en]"];
    $isBank = (int) old('type', $method->type?->value) === DonationMethodType::BankTransfer->value;
@endphp

<x-layouts.admin :title="$title">
    <x-ui.form-page icon="hand-coins" :title="$title" :subtitle="$editing ? $method->name : __('admin.donation_methods.form_subtitle')" :back="route('admin.donation-methods.index')" :parent="__('admin.donation_methods.title')" :parent-url="route('admin.donation-methods.index')">
        <form
            method="POST"
            action="{{ $editing ? route('admin.donation-methods.update', $method) : route('admin.donation-methods.store') }}"
            class="space-y-6"
            x-data="methodFields"
            data-link="{{ DonationMethodType::Link->value }}"
            data-bank="{{ DonationMethodType::BankTransfer->value }}"
            novalidate
        >
            @csrf
            @if ($editing) @method('PUT') @endif

            <x-ui.form-section step="1" :title="__('admin.donation_methods.sections.identity')" :description="__('admin.donation_methods.sections.identity_hint')">
                <x-ui.select name="type" :label="__('admin.donation_methods.fields.type')" :options="DonationMethodType::options()" :selected="$method->type?->value" x-ref="type" x-on:change="sync" required />
                <x-ui.translatable name="name" :label="__('admin.donation_methods.fields.name')" :value="$method->translations('name')" :maxlength="120" required />
                <div class="sm:col-span-2">
                    <x-ui.translatable name="description" :label="__('admin.donation_methods.fields.description')" :value="$method->translations('description')" :maxlength="300" />
                </div>
                <div class="sm:col-span-2" x-show="isLink" @if ($isBank) x-cloak @endif>
                    <x-ui.input name="url" type="url" dir="ltr" placeholder="https://" :label="__('admin.donation_methods.fields.url')" :hint="__('admin.donation_methods.fields.url_hint')" :value="old('url', $method->url)" maxlength="255" />
                </div>
            </x-ui.form-section>

            <div x-show="isBank" @unless ($isBank) x-cloak @endunless>
                <x-ui.form-section step="2" :title="__('admin.donation_methods.sections.bank')" :description="__('admin.donation_methods.sections.bank_hint')">
                    <x-ui.translatable name="bank_details[bank_name]" :names="$bankNames('bank_name')" :label="__('site.donate.bank.bank_name')" :value="$bankTranslations('bank_name')" :maxlength="120" />
                    <x-ui.translatable name="bank_details[beneficiary]" :names="$bankNames('beneficiary')" :label="__('site.donate.bank.beneficiary')" :value="$bankTranslations('beneficiary')" :maxlength="160" />
                    <x-ui.translatable name="bank_details[branch]" :names="$bankNames('branch')" :label="__('site.donate.bank.branch')" :value="$bankTranslations('branch')" :maxlength="120" />
                    <x-ui.translatable name="bank_details[address]" :names="$bankNames('address')" :label="__('site.donate.bank.address')" :value="$bankTranslations('address')" :maxlength="190" />
                    <x-ui.input name="bank_details[account_number]" id="account_number" dir="ltr" :label="__('site.donate.bank.account_number')" :value="$bank('account_number')" maxlength="60" />
                    <x-ui.input name="bank_details[swift_code]" id="swift_code" dir="ltr" :label="__('site.donate.bank.swift_code')" :value="$bank('swift_code')" maxlength="11" />
                    <div class="sm:col-span-2">
                        <x-ui.input name="bank_details[iban]" id="iban" dir="ltr" :label="__('site.donate.bank.iban')" :value="$bank('iban')" maxlength="60" />
                    </div>
                </x-ui.form-section>
            </div>

            @include('admin.partials.publishing', ['step' => 3, 'model' => $method])

            <x-ui.form-actions :hint="__('ui.form.required_hint')">
                <x-ui.button type="submit" icon="check">{{ $editing ? __('ui.actions.save_changes') : __('ui.actions.save') }}</x-ui.button>
                <x-ui.cancel-button :href="route('admin.donation-methods.index')">{{ __('ui.actions.cancel') }}</x-ui.cancel-button>
            </x-ui.form-actions>
        </form>

        <x-slot:aside>
            <x-ui.form-help :title="__('admin.common.help_title')" :items="__('admin.donation_methods.help')" />
        </x-slot:aside>
    </x-ui.form-page>
</x-layouts.admin>
