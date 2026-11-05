@props([
    'label',
    'wide' => false,
    'ltr' => false,
    'mono' => false,
])

<div {{ $attributes->class(['sm:col-span-2' => $wide, 'min-w-0']) }}>
    <dt class="text-xs font-semibold text-ink-subtle">{{ $label }}</dt>
    <dd @class(['mt-1 text-[15px] leading-7 font-semibold break-words text-ink', 'tabular' => $mono || $ltr])>
        @if ($ltr)<span dir="ltr" class="inline-block">{{ $slot }}</span>@else{{ $slot }}@endif
    </dd>
</div>
