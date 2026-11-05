<path d="{{ $side['mesh'] }}" class="stroke-slate-400/70" stroke-width=".45" fill="none"/>
<path d="{{ $side['rails'] }}" class="stroke-slate-500" stroke-width=".8" fill="none"/>
@foreach ($side['poles'] as $pole)
    <path d="{{ $pole }}" class="stroke-slate-600" stroke-width="1.3" stroke-linecap="round"/>
@endforeach
