@props(['href'])

<x-ui.button variant="back" icon="arrow-left" :href="$href" {{ $attributes }}>{{ $slot }}</x-ui.button>
