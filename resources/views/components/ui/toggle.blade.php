@props([
    'name',
    'label',
    'description' => null,
    'checked' => false,
])

<label {{ $attributes->class('flex cursor-pointer items-start gap-3 rounded-xl border border-line p-4 transition hover:border-line-strong has-[:checked]:border-primary/50 has-[:checked]:bg-primary-soft/50') }}>
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" class="peer sr-only" @checked(old($name, $checked))>
    <span class="relative mt-0.5 h-6 w-11 shrink-0 rounded-full bg-line-strong transition peer-checked:bg-primary peer-focus-visible:ring-4 peer-focus-visible:ring-primary/20 after:absolute after:top-0.5 after:start-0.5 after:size-5 after:rounded-full after:bg-white after:shadow-sm after:transition peer-checked:after:-translate-x-5 ltr:peer-checked:after:translate-x-5" aria-hidden="true"></span>
    <span class="min-w-0">
        <span class="block text-sm font-semibold text-ink">{{ $label }}</span>
        @if ($description)
            <span class="block text-[13px] leading-5 text-ink-muted">{{ $description }}</span>
        @endif
    </span>
</label>
