<x-ui.button
    variant="ghost"
    size="icon"
    x-data="theme"
    x-on:click="toggle"
    x-bind:aria-pressed="dark"
    x-bind:aria-label="label"
    x-bind:title="label"
    data-to-dark="{{ __('ui.theme.to_dark') }}"
    data-to-light="{{ __('ui.theme.to_light') }}"
    aria-label="{{ __('ui.theme.label') }}"
>
    <span x-show="light"><x-ui.icon name="sun" /></span>
    <span x-show="dark" x-cloak><x-ui.icon name="moon" /></span>
</x-ui.button>
