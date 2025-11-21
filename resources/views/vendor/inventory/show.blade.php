@extends('layouts.plain')

@section('content')
<div class="min-h-screen flex items-start justify-center py-10 px-4">
    <div class="w-full max-w-4xl">
        <div class="mb-6 flex items-center justify-between">
            <nav aria-label="Breadcrumb" class="flex items-center text-sm font-medium text-gray-500 gap-2">
                <a href="{{ route('vendor.inventory.index') }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l7-9v4h11v10H10v4l-7-9z" />
                    </svg>
                    Inventory
                </a>
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-700">Item #{{ $inventory->id }}</span>
            </nav>
            <a href="{{ route('vendor.inventory.index') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
        </div>

        <!-- Inventory Item Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-2xl font-bold mb-6">Inventory Item #{{ $inventory->id }}</h1>
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-600">Product</p>
                    <p class="text-lg font-semibold">{{ $inventory->product->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Category</p>
                    <p class="text-lg font-semibold">{{ $inventory->product->category->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Quantity Purchased</p>
                    <p class="text-lg font-semibold">{{ $inventory->quantity }} kg</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Purchase Price</p>
                    <p class="text-lg font-semibold">₱{{ number_format($inventory->purchase_price, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Purchased At</p>
                    <p class="text-lg font-semibold">{{ $inventory->purchased_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="text-lg font-semibold">
                        <span class="px-3 py-1 rounded-full text-sm
                            @if($inventory->status === 'in_stock') bg-green-100 text-green-800
                            @elseif($inventory->status === 'listed') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $inventory->status)) }}
                        </span>
                    </p>
                </div>
            </div>

            @if($inventory->status === 'in_stock')
            <div class="mt-6 pt-6 border-t">
                <a href="{{ route('vendor.inventory.create-listing', $inventory) }}" 
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                    Create Marketplace Listing with AI Pricing
                </a>
            </div>
            @endif
        </div>

        <!-- Marketplace Listings -->
        @if($inventory->marketplaceListings->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Marketplace Listings</h2>
            <div class="space-y-4">
                @foreach($inventory->marketplaceListings as $listing)
                <div class="border rounded-lg p-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Listed Price</p>
                            <p class="text-lg font-bold text-green-600">₱{{ number_format($listing->final_price, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Your Profit</p>
                            <p class="text-lg font-bold text-blue-600">₱{{ number_format($listing->vendor_profit, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <p class="text-lg font-semibold">
                                <span class="px-3 py-1 rounded-full text-sm
                                    @if($listing->status === 'active') bg-green-100 text-green-800
                                    @elseif($listing->status === 'sold') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($listing->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        Listed {{ $listing->listing_date->diffForHumans() }} • ML Multiplier: {{ $listing->ml_multiplier }}x • Confidence: {{ round($listing->ml_confidence * 100, 1) }}%
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@include('partials.message-notification')
@include('partials.toast-notifications')
@endsection
