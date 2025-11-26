<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Offers - Fisherman Dashboard</title>
    @php
        $offersFavicon = asset('images/logo.png').'?v=fisherman-offers';
    @endphp
    <link rel="icon" type="image/png" href="{{ $offersFavicon }}">
    <link rel="shortcut icon" type="image/png" href="{{ $offersFavicon }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Koulen&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .brand-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin: 2rem auto 1rem;
        }

        .brand-header img {
            height: 56px;
            width: auto;
        }

        .brand-title {
            font-family: 'Koulen', cursive;
            font-size: 2rem;
            color: #1B5E88;
            margin: 0;
            line-height: 1;
        }

        .brand-tagline {
            font-size: 0.95rem;
            color: #6c757d;
            margin: 0;
            letter-spacing: 0.5px;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .page-header {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .page-header h1 {
            font-family: 'Koulen', cursive;
            color: #1B5E88;
            margin: 0;
            font-size: 2.5rem;
        }

        .offer-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid #0075B5;
        }

        .offer-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .offer-status {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-countered {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-accepted {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .status-expired {
            background: #e2e3e5;
            color: #383d41;
        }

        .status-auto_rejected {
            background: #ffeaa7;
            color: #d63031;
        }

        .status-withdrawn {
            background: #dfe6e9;
            color: #2d3436;
        }

        .status-closed {
            background: #b2bec3;
            color: #2d3436;
        }

        .offer-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .offer-detail-row:last-child {
            border-bottom: none;
        }

        .offer-detail-label {
            font-weight: 600;
            color: #666;
            font-size: 0.9rem;
        }

        .offer-detail-value {
            font-weight: 500;
            color: #333;
        }

        .price-original {
            text-decoration: line-through;
            color: #999;
            font-size: 0.9rem;
            margin-right: 10px;
        }

        .price-offered {
            color: #0075B5;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .price-difference {
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 8px;
        }

        .price-difference.negative {
            color: #dc3545;
        }

        .price-difference.positive {
            color: #28a745;
        }

        .message-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }

        .message-label {
            font-weight: 600;
            color: #555;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .message-text {
            color: #333;
            font-style: italic;
            line-height: 1.5;
        }

        .action-buttons {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-accept {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-accept:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .btn-reject {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            color: white;
        }

        .btn-counter {
            background: linear-gradient(135deg, #0075B5 0%, #1B5E88 100%);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-counter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 117, 181, 0.3);
            color: white;
        }

        .counter-form {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1rem;
            display: none;
        }

        .counter-form.show {
            display: block;
        }

        .form-control-custom {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 14px;
            transition: all 0.3s ease;
        }

        .form-control-custom:focus {
            border-color: #0075B5;
            box-shadow: 0 0 0 0.2rem rgba(0, 117, 181, 0.15);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .empty-state i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #999;
        }

        .vendor-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #1B5E88;
        }

        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .tab {
            padding: 1rem 2rem;
            cursor: pointer;
            border: none;
            background: transparent;
            color: #666;
            font-weight: 600;
            position: relative;
            transition: all 0.3s ease;
        }

        .tab:hover {
            color: #0075B5;
        }

        .tab.active {
            color: #0075B5;
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: #0075B5;
        }

        .pricing-insights {
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 1rem;
            margin-top: 0.75rem;
        }

        .pricing-insights h6 {
            font-weight: 700;
            color: #1B5E88;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .insight-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 0.75rem;
        }

        .insight-item {
            background: #f8fafc;
            border-radius: 8px;
            padding: 0.75rem;
        }

        .insight-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.04em;
        }

        .insight-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .insight-note {
            font-size: 0.8rem;
            color: #475569;
        }

        .insight-meta {
            margin-top: 0.75rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: #475569;
        }
    </style>
</head>
<body>
    @include('fisherman.partials.nav')

    @include('partials.toast-notifications')

    <div class="container">
        <div class="brand-header">
            <img src="{{ asset('images/logo.png') }}" alt="SeaLedger logo">
            <div>
                <p class="brand-title mb-1">SeaLedger Fishermen</p>
                <p class="brand-tagline mb-0">Negotiate with trusted vendors</p>
            </div>
        </div>

        <div class="page-header">
            <h1><i class="fas fa-handshake"></i> Vendor Offers</h1>
            <p class="text-muted mb-0">Review and negotiate offers from vendors for your products</p>
        </div>

        <!-- Filter Tabs -->
        <div class="tabs">
            <button class="tab {{ request('status') == 'pending' || !request('status') ? 'active' : '' }}" 
                    onclick="window.location.href='{{ route('fisherman.offers.index', ['status' => 'pending']) }}'">
                Pending
            </button>
            <button class="tab {{ request('status') == 'countered' ? 'active' : '' }}" 
                    onclick="window.location.href='{{ route('fisherman.offers.index', ['status' => 'countered']) }}'">
                Countered
            </button>
            <button class="tab {{ request('status') == 'accepted' ? 'active' : '' }}" 
                    onclick="window.location.href='{{ route('fisherman.offers.index', ['status' => 'accepted']) }}'">
                Accepted
            </button>
            <button class="tab {{ request('status') == 'auto_rejected' ? 'active' : '' }}" 
                    onclick="window.location.href='{{ route('fisherman.offers.index', ['status' => 'auto_rejected']) }}'">
                Auto-Rejected
            </button>
            <button class="tab {{ request('status') == 'withdrawn' ? 'active' : '' }}" 
                    onclick="window.location.href='{{ route('fisherman.offers.index', ['status' => 'withdrawn']) }}'">
                Canceled
            </button>
            <button class="tab {{ request('status') == 'all' ? 'active' : '' }}" 
                    onclick="window.location.href='{{ route('fisherman.offers.index', ['status' => 'all']) }}'">
                All
            </button>
        </div>

        @if((request('status') == 'pending' || !request('status')) && $offers->isNotEmpty())
            @php
                $productsWithBids = $offers->groupBy('product_id');
            @endphp
            @if($productsWithBids->count() > 0)
                <div class="alert alert-warning mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-gavel"></i>
                            <strong>Bidding Management:</strong> You have {{ $offers->count() }} pending bid(s) across {{ $productsWithBids->count() }} product(s). 
                            Accept bids from highest to lowest to maximize profit.
                        </div>
                        @foreach($productsWithBids as $productId => $productOffers)
                            @php
                                $product = $productOffers->first()->product;
                            @endphp
                            <form method="POST" action="{{ route('fisherman.products.close-bidding', $product) }}" class="d-inline ms-2">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Close all {{ $productOffers->count() }} pending bid(s) for {{ $product->name }}? This will auto-reject all pending offers.')">
                                    <i class="fas fa-times-circle"></i> Close Bidding for {{ $product->name }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        @if($offers->isEmpty())
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No Offers Found</h3>
                <p>You don't have any {{ request('status') ?? 'pending' }} offers at the moment.</p>
            </div>
        @else
            @foreach($offers as $offer)
                <div class="offer-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="mb-2">{{ $offer->product->name }}</h4>
                            <div class="vendor-info">
                                <i class="fas fa-store"></i>
                                <span>{{ $offer->vendor->name }}</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="offer-status status-{{ $offer->status }}">
                                {{ ucfirst($offer->status) }}
                            </span>
                            @if($offer->status === 'pending')
                                @php
                                    $bidRank = $offer->getBidRank();
                                    $canFulfill = $offer->canBeFulfilled();
                                @endphp
                                <span class="badge" style="background: {{ $canFulfill ? '#28a745' : '#dc3545' }}; color: white; font-size: 0.8rem;">
                                    Bid Rank: #{{ $bidRank }}
                                </span>
                                @if($canFulfill)
                                    <span class="badge" style="background: #d4edda; color: #155724; font-size: 0.75rem;">
                                        <i class="fas fa-check-circle"></i> Can Fulfill
                                    </span>
                                @else
                                    <span class="badge" style="background: #f8d7da; color: #721c24; font-size: 0.75rem;">
                                        <i class="fas fa-exclamation-triangle"></i> Insufficient Stock
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>

                    @if($offer->status === 'pending')
                        <div class="alert alert-info mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-info-circle"></i>
                                <strong>Stock Remaining:</strong> {{ $offer->product->stock_quantity }} {{ $offer->product->unit_of_measure }}
                                @if($offer->canBeFulfilled())
                                    → <span class="text-success fw-bold">{{ $offer->product->stock_quantity - $offer->quantity }} {{ $offer->product->unit_of_measure }} left after this sale</span>
                                @else
                                    → <span class="text-danger fw-bold">Not enough stock</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="offer-detail-row">
                        <span class="offer-detail-label">Your Asking Price</span>
                        <span class="offer-detail-value">₱{{ number_format($offer->product->unit_price, 2) }}</span>
                    </div>

                    <div class="offer-detail-row">
                        <span class="offer-detail-label">Vendor's Offer</span>
                        <div>
                            <span class="price-offered">₱{{ number_format($offer->offered_price, 2) }}</span>
                            @php
                                $difference = $offer->offered_price - $offer->product->unit_price;
                                $percentage = ($difference / $offer->product->unit_price) * 100;
                            @endphp
                            <span class="price-difference {{ $difference < 0 ? 'negative' : 'positive' }}">
                                ({{ $difference < 0 ? '' : '+' }}{{ number_format($percentage, 1) }}%)
                            </span>
                        </div>
                    </div>

                    {{-- Pricing Analysis --}}
                    @if($offer->suggested_price_fisherman)
                        @php
                            $suggestedPrice = $offer->suggested_price_fisherman;
                            $vendorOffer = $offer->offered_price;
                            $priceDiff = $vendorOffer - $suggestedPrice;
                            $priceDiffPercent = ($priceDiff / $suggestedPrice) * 100;
                            
                            // Determine offer quality
                            if ($priceDiffPercent < -10) {
                                $qualityIcon = 'fa-triangle-exclamation';
                                $qualityText = 'Below Market Price';
                                $qualityColor = '#dc3545';
                            } elseif ($priceDiffPercent < -5) {
                                $qualityIcon = 'fa-info-circle';
                                $qualityText = 'Slightly Below Market';
                                $qualityColor = '#fd7e14';
                            } elseif ($priceDiffPercent > 10) {
                                $qualityIcon = 'fa-circle-check';
                                $qualityText = 'Excellent Offer';
                                $qualityColor = '#28a745';
                            } elseif ($priceDiffPercent > 5) {
                                $qualityIcon = 'fa-thumbs-up';
                                $qualityText = 'Good Offer';
                                $qualityColor = '#198754';
                            } else {
                                $qualityIcon = 'fa-balance-scale';
                                $qualityText = 'Fair Market Price';
                                $qualityColor = '#0075B5';
                            }
                        @endphp
                        
                        <div style="background: #f8f9fa; border-radius: 8px; padding: 1rem; margin: 1rem 0; border-left: 4px solid {{ $qualityColor }};">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 0.75rem;">
                                <i class="fas {{ $qualityIcon }}" style="color: {{ $qualityColor }}; font-size: 1.2rem;"></i>
                                <span style="font-weight: 700; color: {{ $qualityColor }}; font-size: 1rem;">{{ $qualityText }}</span>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <div style="font-size: 0.75rem; color: #6c757d; margin-bottom: 4px;">Suggested Price</div>
                                    <div style="font-size: 1.1rem; font-weight: 700; color: #0075B5;">₱{{ number_format($suggestedPrice, 2) }}</div>
                                </div>
                                <div>
                                    <div style="font-size: 0.75rem; color: #6c757d; margin-bottom: 4px;">Gap</div>
                                    <div style="font-size: 1.1rem; font-weight: 700; color: {{ $priceDiff >= 0 ? '#28a745' : '#dc3545' }};">
                                        {{ $priceDiff >= 0 ? '+' : '' }}₱{{ number_format($priceDiff, 2) }}
                                        <span style="font-size: 0.85rem;">({{ number_format($priceDiffPercent, 1) }}%)</span>
                                    </div>
                                </div>
                            </div>
                            
                            @if(abs($priceDiffPercent) > 5)
                            <div style="margin-top: 0.75rem; padding: 0.75rem; background: white; border-radius: 6px; font-size: 0.85rem; color: #333; line-height: 1.5;">
                                @if($priceDiffPercent < -10)
                                    ⚠️ This offer is <strong>{{ abs(number_format($priceDiffPercent, 1)) }}% below market value</strong>. Consider negotiating for ₱{{ number_format($suggestedPrice, 2) }} or higher.
                                @elseif($priceDiffPercent < -5)
                                    💭 This offer is below market rate. You might want to counter with ₱{{ number_format($suggestedPrice, 2) }}.
                                @elseif($priceDiffPercent > 10)
                                    🎉 This offer is <strong>{{ number_format($priceDiffPercent, 1) }}% above market price</strong>. Excellent deal!
                                @elseif($priceDiffPercent > 5)
                                    ✅ This offer is <strong>{{ number_format($priceDiffPercent, 1) }}% above market value</strong>. Good deal!
                                @endif
                            </div>
                            @endif
                        </div>
                    @endif

                    @php
                        $pricingLog = $offer->latestPricingLog;
                    @endphp

                    @if($pricingLog)
                        @php
                            $signals = $pricingLog->signals ?? [];
                            $demandScore = $signals['demand']['score'] ?? null;
                            $recentOrders = $signals['demand']['recent_retail_orders'] ?? null;
                            $supplyPressure = $signals['supply']['pressure'] ?? null;
                            $supplyCopy = $supplyPressure ? ($supplyPressure < 0.95 ? 'Tight supply' : ($supplyPressure > 1.15 ? 'Plenty of catch' : 'Balanced')) : null;
                            $acceptanceRate = isset($signals['wholesale']['acceptance_rate']) ? round($signals['wholesale']['acceptance_rate'] * 100, 1) : null;
                            $retailMedian = $signals['retail']['median'] ?? null;
                        @endphp
                        <div class="pricing-insights">
                            <h6>
                                <i class="fas fa-lightbulb"></i>
                                Why this price?
                            </h6>
                            <div class="insight-grid">
                                <div class="insight-item">
                                    <div class="insight-label">Buyer Interest</div>
                                    <div class="insight-value">{{ $demandScore ? number_format($demandScore, 2) . '×' : 'n/a' }}</div>
                                    <div class="insight-note">{{ $recentOrders ? $recentOrders . ' retail orders (24h)' : 'Low recent retail data' }}</div>
                                </div>
                                <div class="insight-item">
                                    <div class="insight-label">Market Supply</div>
                                    <div class="insight-value">{{ $supplyPressure ? number_format($supplyPressure, 2) . '×' : 'n/a' }}</div>
                                    <div class="insight-note">{{ $supplyCopy ?? 'Awaiting supply metrics' }}</div>
                                </div>
                                <div class="insight-item">
                                    <div class="insight-label">Bid Success Rate</div>
                                    <div class="insight-value">{{ $acceptanceRate !== null ? $acceptanceRate . '%' : 'n/a' }}</div>
                                    <div class="insight-note">{{ $acceptanceRate !== null ? 'Accepted wholesale bids last week' : 'No recent wholesale clearing' }}</div>
                                </div>
                                <div class="insight-item">
                                    <div class="insight-label">Avg. Store Price</div>
                                    <div class="insight-value">{{ $retailMedian ? '₱' . number_format($retailMedian, 2) : 'n/a' }}</div>
                                    <div class="insight-note">Recent consumer sales nearby category</div>
                                </div>
                            </div>
                            {{-- Debug info hidden for production --}}
                            {{-- 
                            <div class="insight-meta">
                                <span class="badge bg-{{ $pricingLog->used_fallback ? 'warning' : 'success' }} text-dark" style="font-size: 0.7rem;">
                                    {{ $pricingLog->used_fallback ? 'Heuristic fallback' : 'ML prediction' }}
                                </span>
                                @if($pricingLog->runtime_ms)
                                    <span>Runtime: {{ $pricingLog->runtime_ms }}ms</span>
                                @endif
                                @if(!empty($pricingLog->extra['base_price']))
                                    <span>Base price input: ₱{{ number_format($pricingLog->extra['base_price'], 2) }}</span>
                                @endif
                            </div>
                            --}}
                        </div>
                    @endif

                    @if($offer->status === 'countered' && $offer->fisherman_counter_price)
                        <div class="offer-detail-row">
                            <span class="offer-detail-label">Your Counter Offer</span>
                            <span class="offer-detail-value price-offered">₱{{ number_format($offer->fisherman_counter_price, 2) }}</span>
                        </div>
                    @endif

                    <div class="offer-detail-row">
                        <span class="offer-detail-label">Quantity</span>
                        <span class="offer-detail-value">{{ $offer->quantity }} {{ $offer->product->unit_of_measure }}</span>
                    </div>

                    <div class="offer-detail-row">
                        <span class="offer-detail-label">Total Value</span>
                        <span class="offer-detail-value">
                            ₱{{ number_format($offer->offered_price * $offer->quantity, 2) }}
                        </span>
                    </div>

                    <div class="offer-detail-row">
                        <span class="offer-detail-label">Expires</span>
                        <span class="offer-detail-value">
                            {{ $offer->expires_at ? $offer->expires_at->diffForHumans() : 'No expiration' }}
                        </span>
                    </div>

                    @if($offer->vendor_message)
                        <div class="message-section">
                            <div class="message-label">Vendor's Message</div>
                            <div class="message-text">{{ $offer->vendor_message }}</div>
                        </div>
                    @endif

                    @if($offer->fisherman_message && $offer->status === 'countered')
                        <div class="message-section">
                            <div class="message-label">Your Response</div>
                            <div class="message-text">{{ $offer->fisherman_message }}</div>
                        </div>
                    @endif

                    @if($offer->status === 'pending' && !$offer->isExpired())
                        <div class="action-buttons">
                            <form method="POST" action="{{ route('fisherman.offers.accept', $offer) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-accept" 
                                        @if(!$offer->canBeFulfilled()) disabled title="Insufficient stock to fulfill this bid" @endif
                                        onclick="return confirm('Accept this offer for ₱{{ number_format($offer->offered_price, 2) }} per unit?')">
                                    <i class="fas fa-check"></i> Accept Offer
                                </button>
                            </form>

                            <button type="button" class="btn btn-counter" onclick="toggleCounterForm('counter-{{ $offer->id }}')">
                                <i class="fas fa-reply"></i> Counter Offer
                            </button>
                        </div>

                        <!-- Counter Offer Form (Hidden by default) -->
                        <div id="counter-{{ $offer->id }}" class="counter-form">
                            <form method="POST" action="{{ route('fisherman.offers.counter', $offer) }}">
                                @csrf
                                <h5 class="mb-3">Make a Counter Offer</h5>
                                <div class="mb-3">
                                    <label class="form-label">Counter Price (per {{ $offer->product->unit_of_measure }})</label>
                                    <input type="number" 
                                           name="counter_price" 
                                           class="form-control form-control-custom" 
                                           step="0.01" 
                                         min="{{ number_format($offer->offered_price + 0.01, 2, '.', '') }}"
                                           value="{{ $offer->product->unit_price }}"
                                           required>
                                     <small class="text-muted d-block">Must be higher than the vendor's offer of ₱{{ number_format($offer->offered_price, 2) }}.</small>
                                     <small class="text-muted">Original asking price: ₱{{ number_format($offer->product->unit_price, 2) }}</small>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-counter">
                                        <i class="fas fa-paper-plane"></i> Send Counter Offer
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="toggleCounterForm('counter-{{ $offer->id }}')">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    @if($offer->status === 'accepted')
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="fas fa-check-circle"></i> 
                            This offer was accepted on {{ $offer->responded_at?->format('M d, Y g:i A') }}. 
                            An order has been created and is pending delivery.
                        </div>
                    @endif

                    @if($offer->status === 'rejected')
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="fas fa-times-circle"></i> 
                            This offer was rejected on {{ $offer->responded_at?->format('M d, Y g:i A') }}.
                        </div>
                    @endif

                    @if($offer->isExpired() && $offer->status === 'pending')
                        <div class="alert alert-secondary mt-3 mb-0">
                            <i class="fas fa-clock"></i> 
                            This offer has expired and can no longer be accepted.
                        </div>
                    @endif

                    @if($offer->status === 'auto_rejected')
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-robot"></i> 
                            This offer was automatically rejected on {{ $offer->responded_at?->format('M d, Y g:i A') }} due to insufficient stock after higher bids were accepted.
                        </div>
                    @endif

                    @if($offer->status === 'withdrawn')
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-undo"></i> 
                            This offer was withdrawn by the vendor on {{ $offer->responded_at?->format('M d, Y g:i A') }}.
                        </div>
                    @endif

                    @if($offer->status === 'closed')
                        <div class="alert alert-secondary mt-3 mb-0">
                            <i class="fas fa-gavel"></i> 
                            Bidding was closed on {{ $offer->responded_at?->format('M d, Y g:i A') }}. All pending offers were auto-rejected.
                        </div>
                    @endif
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $offers->links() }}
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleCounterForm(id) {
            const form = document.getElementById(id);
            form.classList.toggle('show');
        }
    </script>

    @include('partials.message-notification')

    <script data-collect-dnt="true" async src="https://scripts.simpleanalyticscdn.com/latest.js"></script>
</body>
</html>
