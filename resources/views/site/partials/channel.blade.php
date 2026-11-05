<span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink"><x-ui.icon :name="$icon" :size="18" /></span>
<span class="min-w-0">
    <span class="block text-xs text-ink-subtle">{{ __("site.contact.channels.{$icon}") }}</span>
    <span @if ($channel['ltr']) dir="ltr" @endif @class(['block font-bold text-ink', 'truncate' => $channel['ltr'], 'leading-6' => ! $channel['ltr']])>{{ $channel['value'] }}</span>
</span>
