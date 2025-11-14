{{-- Presence Indicator Component --}}
@props(['user'])

@if($user->is_online ?? false)
    <span {{ $attributes->merge(['class' => 'badge bg-success text-white']) }}>
        <i class="fas fa-circle" style="font-size: 0.6rem;"></i> Online
    </span>
@else
    <span {{ $attributes->merge(['class' => 'badge bg-secondary text-white']) }}>
        <i class="far fa-circle" style="font-size: 0.6rem;"></i> Offline
        @if($user->last_seen_diff ?? null)
            <small class="ms-1">({{ $user->last_seen_diff }})</small>
        @endif
    </span>
@endif
