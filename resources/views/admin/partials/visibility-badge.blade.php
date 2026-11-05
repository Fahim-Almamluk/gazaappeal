@if ($published)
    <x-ui.badge tone="success" icon="eye">{{ __('ui.status.published') }}</x-ui.badge>
@else
    <x-ui.badge tone="neutral" icon="eye-off">{{ __('ui.status.hidden') }}</x-ui.badge>
@endif
