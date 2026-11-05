@props([
    'action',
    'name',
])

<form method="POST" action="{{ $action }}" @confirm('danger', 'delete', ['name' => $name])>
    @csrf
    @method('DELETE')
    <x-ui.icon-button type="submit" icon="trash-2" tone="danger" :label="__('ui.actions.delete')" />
</form>
