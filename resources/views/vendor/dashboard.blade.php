@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Vendor Dashboard</h1>
        <a href="{{ route('vendor.onboarding') }}" class="text-blue-600">Edit Preferences</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if($prefs)
        <div class="bg-gray-50 p-4 rounded mb-6">
            <div class="text-sm text-gray-700">Notifications: <strong>{{ strtoupper($prefs->notify_on) }}</strong> | Min Qty: <strong>{{ $prefs->min_quantity ?? '—' }}</strong> | Max Price: <strong>{{ $prefs->max_unit_price ?? '—' }}</strong></div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($products as $product)
        <div class="border rounded p-4">
            <div class="font-semibold text-lg">{{ $product->name }}</div>
            <div class="text-sm text-gray-600">Category: {{ $product->category->name ?? '—' }}</div>
            <div class="mt-2 text-sm">Qty: <strong>{{ $product->available_quantity }}</strong> • Price: <strong>{{ $product->unit_price }}</strong></div>
            <div class="text-xs text-gray-500 mt-1">By: {{ $product->supplier->username ?? ('User #'.$product->supplier_id) }}</div>
        </div>
        @empty
            <div class="col-span-full text-gray-600">No listings yet.</div>
        @endforelse
    </div>
</div>
@endsection
