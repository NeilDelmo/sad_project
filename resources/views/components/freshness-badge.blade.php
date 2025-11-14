{{-- Freshness Indicator Component --}}
@props(['product', 'listing' => null])

@php
    $listing = $listing ?? $product->activeMarketplaceListing ?? null;
    $freshnessLevel = $listing?->freshness_level ?? $product->freshness_level ?? null;
    $timeOnMarket = $listing?->time_on_market ?? $product->time_on_market ?? null;
    
    $badgeClass = match($freshnessLevel) {
        'Fresh' => 'bg-success',
        'Good' => 'bg-info',
        'Aging' => 'bg-warning text-dark',
        'Stale' => 'bg-warning text-dark',
        'Spoiled' => 'bg-danger',
        default => 'bg-secondary'
    };
    
    $icon = match($freshnessLevel) {
        'Fresh' => 'fa-leaf',
        'Good' => 'fa-fish',
        'Aging' => 'fa-clock',
        'Stale' => 'fa-exclamation-triangle',
        'Spoiled' => 'fa-ban',
        default => 'fa-question'
    };
@endphp

@if($freshnessLevel)
    <div {{ $attributes->merge(['class' => 'd-flex align-items-center gap-2']) }}>
        <span class="badge {{ $badgeClass }}">
            <i class="fas {{ $icon }}"></i> {{ $freshnessLevel }}
        </span>
        @if($timeOnMarket)
            <small class="text-muted">
                <i class="far fa-clock"></i> {{ $timeOnMarket }}
            </small>
        @endif
    </div>
@else
    <span class="badge bg-secondary">
        <i class="fas fa-info-circle"></i> Not Listed
    </span>
@endif
