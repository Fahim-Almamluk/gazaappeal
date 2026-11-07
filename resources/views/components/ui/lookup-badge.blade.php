@props(['lookup'])

<x-ui.badge :tone="$lookup->badgeTone()" :icon="$lookup->icon" {{ $attributes }}>{{ $lookup->name }}</x-ui.badge>
