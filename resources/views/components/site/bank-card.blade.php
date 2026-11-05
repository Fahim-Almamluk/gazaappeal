@props(['method'])

@php
    $iban = $method->bankDetail('iban');
    $qr = App\Support\QrCode::svg(collect([
        $method->bankDetail('beneficiary'),
        trim($method->bankDetail('bank_name').' - '.$method->bankDetail('branch'), ' -'),
        $iban ? 'IBAN: '.$iban : null,
        $method->bankDetail('swift_code') ? 'SWIFT: '.$method->bankDetail('swift_code') : null,
        $method->bankDetail('account_number') ? 'Account: '.$method->bankDetail('account_number') : null,
    ])->filter()->implode("\n"), 220);
@endphp

<div x-data="bankCard" {{ $attributes->class('flex w-full max-w-[460px] flex-col items-center') }}>
    <div x-ref="stage" x-on:pointermove="tilt" x-on:pointerleave="rest" x-on:click="toggle" class="relative w-full cursor-pointer select-none [perspective:1400px]">
        <div class="absolute inset-0 translate-x-5 translate-y-5 -rotate-2 ltr:-translate-x-5 ltr:rotate-2 rounded-[24px] bg-brand-200/70 dark:bg-brand-800/60"></div>

        <div x-ref="card" data-tilting="false" class="group/card relative transition-[transform,scale] duration-500 ease-out-soft [transform-style:preserve-3d] [transform:rotateX(var(--tilt-x,8deg))_rotateY(var(--tilt-y,-10deg))] data-[tilting=true]:scale-[1.03] data-[tilting=true]:duration-150 motion-reduce:transition-none">
            <div x-bind:data-flipped="flipped" data-flipped="false" class="relative aspect-[1.586] transition-transform duration-700 ease-out-soft [transform-style:preserve-3d] data-[flipped=true]:[transform:rotateY(180deg)] motion-reduce:duration-0">
                <div class="absolute inset-0 isolate flex flex-col justify-between overflow-hidden rounded-[24px] bg-linear-to-br from-brand-400 via-brand-600 to-brand-900 p-6 text-white shadow-[0_40px_70px_-30px_rgb(21_104_201/0.75)] backface-hidden sm:p-7">
                    <div class="pointer-events-none absolute -top-16 -end-10 -z-10 size-56 rounded-full bg-white/15 blur-2xl"></div>
                    <div class="dot-grid pointer-events-none absolute inset-0 -z-10 opacity-40"></div>
                    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_var(--glare-x,50%)_var(--glare-y,50%),rgb(255_255_255/0.28),transparent_55%)] opacity-0 transition-opacity duration-300 group-data-[tilting=true]/card:opacity-100"></div>

                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-medium text-white/70">{{ $method->name }}</p>
                            <p class="mt-0.5 text-lg font-extrabold">{{ $method->bankDetail('bank_name') }}</p>
                        </div>
                        <x-brand.mark :size="40" variant="on-brand" />
                    </div>

                    <svg width="46" height="34" viewBox="0 0 46 34" aria-hidden="true">
                        <rect width="46" height="34" rx="7" class="fill-sun"/>
                        <path d="M0 12H15M0 22H15M31 12H46M31 22H46M15 0V34M31 0V34M15 17H31" stroke="#B45309" stroke-opacity=".55" stroke-width="1.4" fill="none"/>
                    </svg>

                    <div>
                        @if ($iban)
                            <p class="tabular text-[13px] font-bold tracking-[0.05em] whitespace-nowrap sm:text-[16px]" dir="ltr">{{ trim(chunk_split($iban, 4, ' ')) }}</p>
                        @endif
                        <div class="mt-3 flex items-end justify-between gap-4 text-xs">
                            <div class="min-w-0">
                                <p class="text-white/60">{{ __('site.donate.bank.beneficiary') }}</p>
                                <p class="truncate font-bold">{{ $method->bankDetail('beneficiary') }}</p>
                            </div>
                            @if ($method->bankDetail('swift_code'))
                                <div class="shrink-0 text-end">
                                    <p class="text-white/60">{{ __('site.donate.bank.swift_code') }}</p>
                                    <p class="tabular font-bold" dir="ltr">{{ $method->bankDetail('swift_code') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="absolute inset-0 flex items-center gap-5 overflow-hidden rounded-[24px] border border-line bg-surface p-5 shadow-[0_40px_70px_-30px_rgb(21_104_201/0.55)] [transform:rotateY(180deg)] backface-hidden sm:p-6">
                    <div class="aspect-square h-full shrink-0 rounded-2xl border border-line bg-white p-2 [&>svg]:size-full">{{ $qr }}</div>
                    <div class="min-w-0 space-y-2">
                        <x-brand.mark :size="30" />
                        <p class="text-sm font-extrabold text-ink sm:text-base">{{ __('site.giving.qr.bank_title') }}</p>
                        <p class="text-xs leading-5 text-ink-muted">{{ __('site.giving.qr.bank_body') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button type="button" x-on:click="toggle" x-bind:aria-pressed="flipped" aria-pressed="false" class="mt-10 inline-flex h-10 items-center gap-2 rounded-full border border-primary/30 bg-primary-soft px-4 text-sm font-bold text-primary-soft-ink transition hover:border-primary">
        <x-ui.icon name="rotate-ccw" :size="16" />
        {{ __('site.giving.qr.flip') }}
    </button>
</div>
