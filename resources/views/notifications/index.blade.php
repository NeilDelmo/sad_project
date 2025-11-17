@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Notifications</h1>
        <form method="POST" action="{{ route('notifications.read.all') }}">
            @csrf
            <button class="text-sm px-3 py-1 rounded bg-gray-200">Mark all as read</button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded border dark:border-gray-700 divide-y">
        @forelse($notifications as $n)
            @php($data = $n->data ?? [])
            <div class="p-4 @if(!$n->read_at) bg-blue-50 dark:bg-blue-900/20 @endif">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <div class="font-medium">
                            {{ $data['title'] ?? 'Notification' }}
                            @if(!$n->read_at)
                                <span class="ml-2 inline-block text-[10px] px-2 py-0.5 rounded bg-blue-600 text-white align-middle">NEW</span>
                            @endif
                        </div>
                        @if(!empty($data['message']))
                            <div class="text-sm text-gray-700 dark:text-gray-200 mt-1">{{ $data['message'] }}</div>
                        @endif

                        @if(($data['type'] ?? null) === 'rental_approved')
                            @if(!empty($data['pickup_otp']))
                                <div class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-blue-800 bg-blue-100 dark:bg-blue-900/40 dark:text-blue-200 px-2.5 py-1 rounded">
                                    <span class="inline-block">Pickup OTP:</span>
                                    <span class="tracking-widest">{{ $data['pickup_otp'] }}</span>
                                </div>
                            @endif
                            @if(!empty($data['expires_at']))
                                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Expires: {{ $data['expires_at'] }}</div>
                            @endif
                        @endif

                        <div class="text-xs text-gray-500 mt-2">{{ $n->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="flex items-start gap-2">
                        @if(!empty($data['action_url']))
                            <a href="{{ $data['action_url'] }}" class="text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-700 dark:text-gray-100">Open</a>
                        @endif
                        @if(!$n->read_at)
                            <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                                @csrf
                                <button class="text-xs px-2 py-1 rounded bg-blue-600 text-white">Mark read</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-4 text-gray-600">No notifications</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
@endsection
