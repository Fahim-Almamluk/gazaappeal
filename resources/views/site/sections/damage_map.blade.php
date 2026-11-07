@if ($damages->isNotEmpty())
    <x-site.damage-map :damages="$damages" :categories="$damageCategories" />
@endif
