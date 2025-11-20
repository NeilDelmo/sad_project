@props(['score' => null, 'tier' => null, 'compact' => false])
@php
    $tier = strtolower($tier ?? 'bronze');
    [$bg,$fg] = match($tier) {
        'platinum' => ['linear-gradient(135deg,#6366f1,#a855f7)','white'],
        'gold' => ['#eab308','white'],
        'silver' => ['#9ca3af','white'],
        default => ['#f97316','white'],
    };
@endphp
<span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:14px;font-size:11px;font-weight:600;letter-spacing:.5px;background: {{ $bg }}; color: {{ $fg }};">
    {{ ucfirst($tier) }}
    @if(!$compact && $score !== null)
        <span style="opacity:.8;font-weight:500;">{{ $score }}</span>
    @endif
</span>