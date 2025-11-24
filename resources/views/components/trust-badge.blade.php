@props(['score' => null, 'tier' => null, 'compact' => false])
@php
    $tier = strtolower($tier ?? 'bronze');
    $tierClass = 'trust-badge--' . $tier;
@endphp
<span {{ $attributes->class(['trust-badge', $tierClass, $compact ? 'trust-badge--compact' : null]) }}>
    <span class="trust-badge__icon">★</span>
    <span class="trust-badge__label">{{ ucfirst($tier) }}</span>
    @if(!$compact && $score !== null)
        <span class="trust-badge__score">{{ $score }}</span>
    @endif
</span>