@if ($methods->isNotEmpty())
    <x-site.band>
        <x-site.giving :methods="$methods" />
    </x-site.band>
@endif
