@props(['href'])

<x-ui.button variant="cancel" icon="x" :href="$href" {{ $attributes }}>{{ $slot }}</x-ui.button>
