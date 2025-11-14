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
            <div class="p-4 @if(!$n->read_at) bg-blue-50 dark:bg-blue-900/20 @endif">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="font-medium">New catch: {{ data_get($n->data,'name','Product') }}</div>
                        <div class="text-sm text-gray-600">Qty {{ data_get($n->data,'available_quantity') }} • ₱{{ data_get($n->data,'unit_price') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $n->created_at->diffForHumans() }}</div>
                    </div>
                    @if(!$n->read_at)
                    <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                        @csrf
                        <button class="text-xs px-2 py-1 rounded bg-blue-600 text-white">Mark read</button>
                    </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-4 text-gray-600">No notifications</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
@endsection
