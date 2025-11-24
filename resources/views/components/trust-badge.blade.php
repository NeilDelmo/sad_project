@props(['score' => 100, 'tier' => null, 'compact' => false])
@php
    // Score is 0-200. Calculate percentage for circle (0-100)
    $percentage = min(100, max(0, ($score / 200) * 100));
    $circumference = 2 * pi() * 18; // r=18
    $dashoffset = $circumference - ($percentage / 100) * $circumference;
    
    // Color based on score
    $color = match(true) {
        $score >= 150 => '#8b5cf6', // Platinum/Purple
        $score >= 120 => '#eab308', // Gold
        $score >= 90  => '#3b82f6', // Silver/Blue
        default => '#f97316', // Bronze/Orange
    };
@endphp
<div class="trust-indicator" title="Trust Score: {{ $score }}/200">
    <svg width="44" height="44" viewBox="0 0 44 44" class="trust-circle">
        <circle cx="22" cy="22" r="18" fill="none" stroke="#e5e7eb" stroke-width="4"></circle>
        <circle cx="22" cy="22" r="18" fill="none" stroke="{{ $color }}" stroke-width="4"
                stroke-dasharray="{{ $circumference }}"
                stroke-dashoffset="{{ $dashoffset }}"
                transform="rotate(-90 22 22)"
                style="transition: stroke-dashoffset 0.5s ease;"></circle>
        <text x="50%" y="50%" dy=".3em" text-anchor="middle" font-size="12" font-weight="bold" fill="#374151">
            {{ $score }}
        </text>
    </svg>
</div>
<style>
    .trust-indicator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: help;
    }
</style>