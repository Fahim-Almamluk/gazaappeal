@props([
    'column',
    'first' => 'asc',
])

@php
    $active = request()->query('sort') === $column;
    $direction = request()->query('dir') === 'asc' ? 'asc' : 'desc';
    $next = $active ? ($direction === 'asc' ? 'desc' : 'asc') : $first;
@endphp

<a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'dir' => $next, 'page' => null]) }}" @class(['inline-flex items-center gap-1 transition hover:text-ink', 'text-ink' => $active])>
    {{ $slot }}
    @if ($active)
        <x-ui.icon :name="$direction === 'asc' ? 'arrow-up' : 'arrow-down'" :size="12" />
    @endif
</a>
