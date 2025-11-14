@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('vendor.inventory.show', $inventory) }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Back to Inventory
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Create Marketplace Listing</h1>
            <p class="text-gray-600 mt-2">AI-powered dynamic pricing for optimal market performance</p>
        </div>

        <!-- Product Info Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Product Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Product</p>
                    <p class="font-medium">{{ $inventory->product->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Category</p>
                    <p class="font-medium">{{ $inventory->product->category->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Available Quantity</p>
                    <p class="font-medium">{{ $inventory->quantity }} kg</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Your Purchase Cost</p>
                    <p class="font-medium">₱{{ number_format($baseCost, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- ML Pricing Analysis Card -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-md p-6 mb-6 border-2 border-blue-200">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-gray-900">AI Dynamic Pricing Analysis</h2>
            </div>

            <!-- Market Conditions -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg p-4">
                    <p class="text-xs text-gray-600 mb-1">Freshness Score</p>
                    <p class="text-2xl font-bold text-green-600">{{ round($pricingResult['features']['freshness_score']) }}/100</p>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <p class="text-xs text-gray-600 mb-1">Market Demand</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $pricingResult['features']['demand_factor'] }}x</p>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <p class="text-xs text-gray-600 mb-1">ML Confidence</p>
                    <p class="text-2xl font-bold text-blue-600">{{ round($mlConfidence * 100, 1) }}%</p>
                </div>
            </div>

            <!-- Pricing Breakdown -->
            <div class="bg-white rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Pricing Breakdown</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-gray-600">Your Purchase Cost</span>
                        <span class="font-medium">₱{{ number_format($baseCost, 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-gray-600 flex items-center">
                            AI Multiplier
                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ $mlMultiplier }}x</span>
                        </span>
                        <span class="font-medium text-blue-600">₱{{ number_format($dynamicPrice, 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-gray-600 flex items-center">
                            Platform Fee
                            <span class="ml-2 text-xs text-gray-500">(10%)</span>
                        </span>
                        <span class="font-medium text-red-600">-₱{{ number_format($platformFee, 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center pt-2">
                        <span class="font-semibold text-gray-900">Your Profit</span>
                        <span class="text-2xl font-bold text-green-600">₱{{ number_format($vendorProfit, 2) }}</span>
                    </div>
                </div>

                <!-- Profit Margin Indicator -->
                <div class="mt-4 p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Profit Margin:</span>
                        <span class="text-lg font-bold text-green-700">
                            {{ round(($vendorProfit / $baseCost) * 100, 1) }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ min(($vendorProfit / $baseCost) * 100, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listing Form -->
        <form action="{{ route('vendor.inventory.store-listing', $inventory) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
            @csrf
            
            <input type="hidden" name="quantity" value="{{ $inventory->quantity }}">
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Final Listing Price</h3>
                <div class="flex items-baseline">
                    <span class="text-4xl font-bold text-green-600">₱{{ number_format($dynamicPrice, 2) }}</span>
                    <span class="ml-2 text-gray-500">per kg</span>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    This AI-optimized price maximizes your profit while remaining competitive in the market.
                </p>
            </div>

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                    Create Listing
                </button>
                <a href="{{ route('vendor.inventory.show', $inventory) }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg text-center transition duration-200">
                    Cancel
                </a>
            </div>
        </form>

        <!-- Market Insights -->
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Market Insight:</strong> Our AI model analyzed current market conditions including freshness ({{ round($pricingResult['features']['freshness_score']) }}/100), demand ({{ $pricingResult['features']['demand_factor'] }}x), and seasonality to recommend this optimal price with {{ round($mlConfidence * 100, 1) }}% confidence.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
