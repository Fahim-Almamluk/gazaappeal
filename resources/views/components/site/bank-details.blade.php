@props(['method'])

@php
    $details = $method->filledBankDetails();
    $bankName = $details['bank_name'] ?? $method->name;
    $info = array_intersect_key($details, array_flip(['beneficiary', 'branch', 'address']));
    $numbers = array_intersect_key($details, array_flip(['account_number', 'swift_code', 'iban']));
@endphp

<div {{ $attributes->class('overflow-hidden rounded-card border border-line bg-surface shadow-elevated') }}>
    <div class="flex items-center gap-4 border-b border-line bg-surface-2 px-5 py-4 sm:px-6">
        <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink">
            <x-ui.icon name="landmark" :size="22" />
        </span>
        <div class="min-w-0">
            <p class="text-xs font-semibold text-ink-subtle">{{ __('site.donate.bank.bank_name') }}</p>
            <p class="truncate text-lg font-extrabold text-ink">{{ $bankName }}</p>
        </div>
    </div>

    @if ($info)
        <dl class="grid border-b border-line sm:grid-cols-2">
            @foreach ($info as $field => $value)
                <div @class([
                    'border-line px-5 py-4 sm:px-6',
                    'border-b sm:col-span-2' => $field === 'beneficiary',
                    'border-b last:border-b-0 sm:border-b-0' => $field !== 'beneficiary',
                    'sm:border-e' => $field === 'branch' && isset($info['address']),
                ])>
                    <dt class="text-xs font-semibold text-ink-subtle">{{ __("site.donate.bank.{$field}") }}</dt>
                    <dd class="mt-1 font-bold text-ink">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    @endif

    <dl class="divide-y divide-line">
        @foreach ($numbers as $field => $value)
            <div class="flex items-center gap-3 px-5 py-3.5 sm:px-6">
                <dt class="w-24 shrink-0 text-xs font-semibold text-ink-subtle sm:w-28">{{ __("site.donate.bank.{$field}") }}</dt>
                <dd class="min-w-0 flex-1 text-end">
                    <span dir="ltr" class="tabular font-mono text-[15px] font-bold tracking-wide break-all text-ink sm:text-base">{{ $field === 'iban' ? implode(' ', str_split($value, 4)) : $value }}</span>
                </dd>
                <span x-data="copyText" data-text="{{ $value }}" data-copy="{{ __('site.donate.copy') }}" data-copied="{{ __('site.donate.copied') }}" class="shrink-0">
                    <button type="button" x-on:click="copy" x-bind:aria-label="label" x-bind:title="label" aria-label="{{ __('site.donate.copy') }}" class="flex size-9 items-center justify-center rounded-lg border border-line text-ink-subtle transition hover:border-primary/40 hover:bg-primary-soft hover:text-primary">
                        <span x-show="idle"><x-ui.icon name="copy" :size="16" /></span>
                        <span x-show="copied" x-cloak class="text-success"><x-ui.icon name="check" :size="16" /></span>
                    </button>
                </span>
            </div>
        @endforeach
    </dl>
</div>
