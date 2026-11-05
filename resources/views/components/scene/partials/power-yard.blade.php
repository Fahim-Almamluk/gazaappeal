<polygon points="{{ $object['pad'] }}" class="fill-line dark:fill-surface-2"/>
<path d="{{ $object['cables'] }}" stroke="#FACC15" stroke-width="1.6" stroke-dasharray="3 5" stroke-linecap="round" fill="none" class="animate-flow motion-reduce:animate-none"/>

@foreach ($object['back'] as $side)
    @include('components.scene.partials.wire-fence', ['side' => $side])
@endforeach

@foreach ($object['items'] as $item)
    @switch($item['type'])
        @case('generator')
            @php
                [$sx, $sy] = $item['stack'];
                [$bx, $by] = $item['bolt'];
                [$lx, $ly] = $item['light'];
            @endphp
            <polygon points="{{ $item['right'] }}" class="fill-brand-300 dark:fill-brand-700"/>
            <polygon points="{{ $item['left'] }}" class="fill-brand-200 dark:fill-brand-600"/>
            <polygon points="{{ $item['top'] }}" class="fill-brand-50 stroke-brand-300 dark:fill-brand-300 dark:stroke-brand-800" stroke-width=".8"/>
            <path d="{{ $item['louvers'] }}" class="stroke-brand-500" stroke-width="1" stroke-linecap="round"/>
            <polygon points="{{ $bx }},{{ $by - 5 }} {{ $bx - 3 }},{{ $by + 1 }} {{ $bx - 0.6 }},{{ $by + 1 }} {{ $bx - 1.6 }},{{ $by + 6 }} {{ $bx + 3 }},{{ $by - 1 }} {{ $bx + 0.6 }},{{ $by - 1 }}" class="fill-sun"/>
            <rect x="{{ $sx - 1.8 }}" y="{{ $sy - 8 }}" width="3.6" height="8" class="fill-slate-500"/>
            <ellipse cx="{{ $sx }}" cy="{{ $sy - 8 }}" rx="1.8" ry="0.9" class="fill-slate-700"/>
            <circle cx="{{ $lx }}" cy="{{ $ly }}" r="1.4" class="fill-sun animate-light-on motion-reduce:animate-none {{ $item['delay'] }}"/>
            @break

        @case('transformer')
            <polygon points="{{ $item['right'] }}" class="fill-slate-600"/>
            <polygon points="{{ $item['left'] }}" class="fill-slate-500"/>
            <polygon points="{{ $item['top'] }}" class="fill-slate-400"/>
            @foreach ($item['tops'] as [$tx, $ty])
                <rect x="{{ $tx - 0.9 }}" y="{{ $ty - 5 }}" width="1.8" height="5" class="fill-brand-200 stroke-brand-500" stroke-width=".4"/>
            @endforeach
            @break

        @case('truck')
            <polygon points="{{ $item['bed']['right'] }}" class="fill-brand-600"/>
            <polygon points="{{ $item['bed']['left'] }}" class="fill-brand-500"/>
            <polygon points="{{ $item['bed']['top'] }}" class="fill-brand-300"/>
            <polygon points="{{ $item['cab']['right'] }}" class="fill-brand-700"/>
            <polygon points="{{ $item['cab']['left'] }}" class="fill-brand-600"/>
            <polygon points="{{ $item['cab']['top'] }}" class="fill-brand-400"/>
            <polygon points="{{ $item['window'] }}" class="fill-brand-100"/>
            @foreach ($item['wheels'] as $wheel)
                <polygon points="{{ $wheel['tyre'] }}" class="fill-slate-800"/>
                <polygon points="{{ $wheel['hub'] }}" class="fill-slate-400"/>
            @endforeach
            @if ($item['reel'])
                <polygon points="{{ $item['reel']['core']['right'] }}" class="fill-slate-600"/>
                <polygon points="{{ $item['reel']['core']['top'] }}" class="fill-slate-500"/>
                <polygon points="{{ $item['reel']['rim'] }}" class="fill-sun stroke-slate-600" stroke-width=".6"/>
                <polygon points="{{ $item['reel']['wound'] }}" class="fill-slate-700"/>
                <polygon points="{{ $item['reel']['hub'] }}" class="fill-slate-300"/>
                <path d="{{ $item['reel']['tail'] }}" stroke="#FACC15" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
            @endif
            @foreach ($item['poles'] as $pole)
                <polygon points="{{ $pole['right'] }}" class="fill-slate-500"/>
                <polygon points="{{ $pole['left'] }}" class="fill-slate-400"/>
                <polygon points="{{ $pole['top'] }}" class="fill-slate-300"/>
            @endforeach
            @break

        @case('line')
            @foreach ($item['poles'] as $pole)
                <path d="{{ $pole['mast'] }}" class="stroke-slate-500" stroke-width="2.6" stroke-linecap="round"/>
                <path d="{{ $pole['arm'] }}" class="stroke-slate-600" stroke-width="2" stroke-linecap="round"/>
                @foreach ($pole['caps'] as [$kx, $ky])
                    <circle cx="{{ $kx }}" cy="{{ $ky }}" r="1.5" class="fill-brand-200 stroke-brand-500" stroke-width=".5"/>
                @endforeach
            @endforeach
            <path d="{{ $item['wires'] }}" class="stroke-slate-700" stroke-width=".7" fill="none"/>
            <path d="{{ $item['lineman']['body'] }}" class="stroke-slate-700" stroke-width="2.4" stroke-linecap="round"/>
            <circle cx="{{ $item['lineman']['head'][0] }}" cy="{{ $item['lineman']['head'][1] }}" r="1.9" class="fill-sun"/>
            @break

        @case('drum')
            @php([$cx, $cy] = $item['at'])
            <rect x="{{ $cx - 5 }}" y="{{ $cy - 5 }}" width="10" height="5" class="fill-slate-500"/>
            <ellipse cx="{{ $cx }}" cy="{{ $cy }}" rx="5" ry="2.5" class="fill-slate-600"/>
            <ellipse cx="{{ $cx }}" cy="{{ $cy - 5 }}" rx="5" ry="2.5" class="fill-sun"/>
            <ellipse cx="{{ $cx }}" cy="{{ $cy - 5 }}" rx="2.2" ry="1.1" class="fill-slate-500"/>
            @break

        @case('logs')
            @foreach ($item['boxes'] as $log)
                <polygon points="{{ $log['right'] }}" class="fill-slate-500"/>
                <polygon points="{{ $log['left'] }}" class="fill-slate-400"/>
                <polygon points="{{ $log['top'] }}" class="fill-slate-300"/>
            @endforeach
            @break

        @case('cabin')
            <polygon points="{{ $item['right'] }}" class="fill-brand-300 dark:fill-brand-700"/>
            <polygon points="{{ $item['left'] }}" class="fill-brand-200 dark:fill-brand-600"/>
            <polygon points="{{ $item['top'] }}" class="fill-white stroke-brand-300 dark:fill-brand-300 dark:stroke-brand-800" stroke-width=".8"/>
            <polygon points="{{ $item['window'] }}" class="fill-brand-800/70"/>
            <polygon points="{{ $item['window'] }}" class="fill-sun animate-light-on motion-reduce:animate-none [animation-delay:3s]"/>
            @break

        @case('worker')
            <path d="{{ $item['body'] }}" class="stroke-slate-700" stroke-width="2.4" stroke-linecap="round"/>
            <circle cx="{{ $item['head'][0] }}" cy="{{ $item['head'][1] }}" r="1.9" class="fill-sun"/>
            @break
    @endswitch
@endforeach

@foreach ($object['front'] as $side)
    @include('components.scene.partials.wire-fence', ['side' => $side])
@endforeach
