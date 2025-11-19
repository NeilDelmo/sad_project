<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('bootstrap5/css/bootstrap.min.css') }}" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>SeaLedger - Vendor Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');

        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            margin: 0;
        }

        .navbar .container-fluid {
            width: 100%;
            max-width: 100%;
            padding: 0 40px;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 16px;
            flex-wrap: nowrap;
        }

        .nav-brand {
            color: white;
            font-size: 24px;
            font-weight: bold;
            font-family: "Koulen", sans-serif;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .nav-links {
            display: flex;
            gap: 5px;
            align-items: center;
            margin-left: auto;
            flex-wrap: wrap;
            padding-bottom: 4px;
        }

        .nav-links::-webkit-scrollbar {
            display: none;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            white-space: nowrap;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: white;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
        }

        .nav-link:hover::before {
            transform: translateX(0);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .dashboard-shell {
            width: 100%;
            padding: 40px 0 60px;
            box-sizing: border-box;
        }

        .dashboard-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px 40px;
            display: flex;
            flex-direction: column;
            gap: 36px;
            box-sizing: border-box;
        }

        .welcome-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .welcome-title {
            font-family: "Koulen", sans-serif;
            font-size: 42px;
            color: #1B5E88;
            margin-bottom: 10px;
        }

        .btn-primary-custom {
            background: #0075B5;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            background: #1B5E88;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,117,181,0.3);
        }

        .btn-secondary-custom {
            background: white;
            color: #0075B5;
            padding: 15px 30px;
            border: 2px solid #0075B5;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-secondary-custom:hover {
            background: #E7FAFE;
            transform: translateY(-2px);
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-icon {
            font-size: 48px;
            color: #0075B5;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #1B5E88;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 16px;
            color: #666;
        }

        .section-title {
            font-family: "Koulen", sans-serif;
            font-size: 28px;
            color: #1B5E88;
            margin-bottom: 20px;
            border-bottom: 3px solid #0075B5;
            padding-bottom: 10px;
        }

        .offer-list {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .offer-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .offer-item:last-child {
            border-bottom: none;
        }

        .offer-info {
            flex-grow: 1;
        }

        .offer-name {
            font-size: 18px;
            font-weight: bold;
            color: #1B5E88;
            margin-bottom: 5px;
        }

        .offer-details {
            font-size: 14px;
            color: #666;
        }

        .offer-price {
            font-size: 20px;
            font-weight: bold;
            color: #16a34a;
            margin-right: 20px;
        }

        .btn-view-details {
            background: #0075B5;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-view-details:hover {
            background: #1B5E88;
            transform: translateY(-1px);
        }

        /* Receipt Modal Styles */
        .receipt-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            align-items: center;
            justify-content: center;
        }

        .receipt-modal.active {
            display: flex;
        }

        .receipt-content {
            background: white;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .receipt-header {
            background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
            color: white;
            padding: 25px;
            border-radius: 12px 12px 0 0;
            text-align: center;
        }

        .receipt-header h2 {
            margin: 0;
            font-family: "Koulen", sans-serif;
            font-size: 28px;
        }

        .receipt-header .receipt-date {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
        }

        .receipt-body {
            padding: 30px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .receipt-row:last-child {
            border-bottom: none;
        }

        .receipt-label {
            color: #666;
            font-weight: 500;
        }

        .receipt-value {
            color: #1B5E88;
            font-weight: 600;
        }

        .receipt-total {
            background: #f8f9fa;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .receipt-total-label {
            font-size: 18px;
            font-weight: 700;
            color: #1B5E88;
        }

        .receipt-total-value {
            font-size: 28px;
            font-weight: 800;
            color: #16a34a;
        }

        .receipt-footer {
            padding: 20px 30px;
            background: #f8f9fa;
            border-radius: 0 0 12px 12px;
            text-align: center;
        }

        .close-receipt {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .close-receipt:hover {
            background: #5a6268;
        }

        .receipt-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .receipt-status.accepted {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-accepted {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-countered {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Toast styles removed; using shared partial in vendor.nav */

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    @include('vendor.partials.nav')

    <div class="dashboard-shell">

        <div class="dashboard-container">
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-title">Vendor Dashboard</div>
            <p style="font-size: 18px; color: #666; margin-bottom: 0;">Welcome back, {{ Auth::user()->username ?? Auth::user()->email }}!</p>
            <p style="font-size: 16px; color: #999;">Browse fisherman products and manage your inventory</p>

            <div class="action-buttons">
                <a href="{{ route('vendor.products.index') }}" class="btn-primary-custom">
                    <i class="fa-solid fa-fish"></i>
                    Browse Products
                </a>
                <a href="{{ route('vendor.inventory.index') }}" class="btn-secondary-custom">
                    <i class="fa-solid fa-box"></i>
                    My Inventory
                </a>
                <a href="{{ route('orders.index') }}" class="btn-secondary-custom">
                    <i class="fa-solid fa-clipboard-list"></i>
                    Orders
                </a>
                <a href="{{ route('vendor.onboarding') }}" class="btn-secondary-custom">
                    <i class="fa-solid fa-gear"></i>
                    Preferences
                </a>
            </div>
        </div>

        <!-- Revenue Overview Cards -->
        <div class="section-title">Revenue Overview</div>
        <div class="stats-grid" style="margin-bottom: 30px;">
            <div class="stat-card" style="background: linear-gradient(135deg, #0075B5 0%, #1B5E88 100%); color: white;">
                <div class="stat-icon" style="color: white; opacity: 0.9;">
                    <i class="fa-solid fa-peso-sign"></i>
                </div>
                <div class="stat-number" style="color: white;">₱{{ number_format($totalIncome ?? 0, 2) }}</div>
                <div class="stat-label" style="color: rgba(255,255,255,0.9);">Total Income</div>
                <div style="font-size: 12px; opacity: 0.8; margin-top: 5px;">Marketplace sales</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #0075B5 0%, #1B5E88 100%); color: white;">
                <div class="stat-icon" style="color: white; opacity: 0.9;">
                    <i class="fa-solid fa-money-bill-trend-up"></i>
                </div>
                <div class="stat-number" style="color: white;">₱{{ number_format($totalSpending ?? 0, 2) }}</div>
                <div class="stat-label" style="color: rgba(255,255,255,0.9);">Total Spending</div>
                <div style="font-size: 12px; opacity: 0.8; margin-top: 5px;">Buying from fishermen</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #0075B5 0%, #1B5E88 100%); color: white;">
                <div class="stat-icon" style="color: white; opacity: 0.9;">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="stat-number" style="color: white;">₱{{ number_format(($totalIncome ?? 0) - ($totalSpending ?? 0), 2) }}</div>
                <div class="stat-label" style="color: rgba(255,255,255,0.9);">Net Profit</div>
                <div style="font-size: 12px; opacity: 0.8; margin-top: 5px;">Income - Spending</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #0075B5 0%, #1B5E88 100%); color: white;">
                <div class="stat-icon" style="color: white; opacity: 0.9;">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <div class="stat-number" style="color: white;">
                    @php
                        $netProfit = ($totalIncome ?? 0) - ($totalSpending ?? 0);
                        $profitMargin = ($totalIncome ?? 0) > 0 ? (($netProfit / $totalIncome) * 100) : 0;
                    @endphp
                    {{ number_format($profitMargin, 1) }}%
                </div>
                <div class="stat-label" style="color: rgba(255,255,255,0.9);">Profit Margin</div>
                <div style="font-size: 12px; color: rgba(255,255,255,0.8); margin-top: 5px;">
                    @if($profitMargin >= 30)
                        <span>✓ Excellent</span>
                    @elseif($profitMargin >= 15)
                        <span>✓ Good</span>
                    @else
                        <span>⚠ Low</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Business Statistics -->
        <div class="section-title">Business Statistics</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <div class="stat-number">{{ $acceptedOffersCount ?? 0 }}</div>
                <div class="stat-label">Accepted Offers</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="stat-number">{{ $pendingOffersCount ?? 0 }}</div>
                <div class="stat-label">Pending Offers</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div class="stat-number">
                    @php
                        $inventoryCount = \App\Models\VendorInventory::where('vendor_id', Auth::id())->count();
                    @endphp
                    {{ $inventoryCount }}
                </div>
                <div class="stat-label">Inventory Items</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div class="stat-number">
                    @php
                        $activeListings = \App\Models\MarketplaceListing::whereHas('vendorInventory', function($q) {
                            $q->where('vendor_id', Auth::id());
                        })->where('status', 'active')->count();
                    @endphp
                    {{ $activeListings }}
                </div>
                <div class="stat-label">Active Listings</div>
            </div>
        </div>

        <!-- Revenue & Spending Trend Chart -->
        <div class="section-title">Revenue & Spending Trend (Last 14 Days)</div>
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <canvas id="revenueChart" style="max-height: 350px;"></canvas>
        </div>

        <!-- Recent Marketplace Customer Orders -->
        @if(isset($recentCustomerOrders) && $recentCustomerOrders->count() > 0)
        <div class="section-title">Recent Marketplace Orders</div>
        <div class="offer-list" style="margin-bottom: 30px;">
            @foreach($recentCustomerOrders as $order)
            <div class="offer-item">
                <div class="offer-info">
                    <div class="offer-name">
                        @php
                            $statusIcon = match($order->status) {
                                'pending_payment' => ['icon' => 'fa-clock', 'color' => '#f59e0b'],
                                'in_transit' => ['icon' => 'fa-truck', 'color' => '#0075B5'],
                                'delivered' => ['icon' => 'fa-box-open', 'color' => '#8b5cf6'],
                                'received' => ['icon' => 'fa-circle-check', 'color' => '#16a34a'],
                                'refund_requested' => ['icon' => 'fa-exclamation-triangle', 'color' => '#dc2626'],
                                'refunded' => ['icon' => 'fa-rotate-left', 'color' => '#6c757d'],
                                'refund_declined' => ['icon' => 'fa-times-circle', 'color' => '#991b1b'],
                                default => ['icon' => 'fa-question-circle', 'color' => '#666']
                            };
                        @endphp
                        <i class="fa-solid {{ $statusIcon['icon'] }}" style="color: {{ $statusIcon['color'] }}; margin-right: 8px;"></i>
                        {{ $order->listing->product->name ?? 'Product' }}
                    </div>
                    <div class="offer-details">
                        Buyer: {{ $order->buyer->username ?? $order->buyer->email }}
                        • {{ $order->quantity }} kg @ ₱{{ number_format($order->unit_price, 2) }}/kg
                        • <span class="status-badge status-{{ str_replace('_', '-', $order->status) }}">{{ str_replace('_',' ', ucfirst($order->status)) }}</span>
                        <span style="color: #999; margin-left: 10px;">{{ $order->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div class="offer-price" style="color: {{ in_array($order->status, ['received', 'delivered']) ? '#16a34a' : '#666' }};">₱{{ number_format($order->total, 2) }}</div>
                    <a href="{{ route('marketplace.orders.index') }}" class="btn-view-details">
                        <i class="fa-solid fa-eye"></i> View
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Recent Transaction History -->
        @if(isset($recentAcceptedOffers) && $recentAcceptedOffers->count() > 0)
        <div class="section-title">Recent Transaction History</div>
        <div class="offer-list">
            @foreach($recentAcceptedOffers as $offer)
            <div class="offer-item">
                <div class="offer-info">
                    <div class="offer-name">
                        @if($offer->status === 'accepted')
                            <i class="fa-solid fa-circle-check" style="color: #16a34a; margin-right: 8px;"></i>
                        @elseif($offer->status === 'pending')
                            <i class="fa-solid fa-clock" style="color: #f59e0b; margin-right: 8px;"></i>
                        @elseif($offer->status === 'rejected')
                            <i class="fa-solid fa-circle-xmark" style="color: #dc2626; margin-right: 8px;"></i>
                        @else
                            <i class="fa-solid fa-handshake" style="color: #0075B5; margin-right: 8px;"></i>
                        @endif
                        {{ $offer->product->name ?? 'Product' }}
                    </div>
                    <div class="offer-details">
                        Fisherman: {{ $offer->fisherman->username ?? $offer->fisherman->email }}
                        • {{ $offer->quantity }} kg @ ₱{{ number_format($offer->offered_price, 2) }}/kg
                        • <span class="status-badge status-{{ $offer->status }}">{{ ucfirst($offer->status) }}</span>
                        <span style="color: #999; margin-left: 10px;">{{ $offer->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div class="offer-price" style="color: {{ $offer->status === 'accepted' ? '#16a34a' : ($offer->status === 'rejected' ? '#dc2626' : '#666') }};">₱{{ number_format($offer->offered_price * $offer->quantity, 2) }}</div>
                    <button class="btn-view-details" 
                        data-id="{{ $offer->id }}"
                        data-product="{{ $offer->product->name ?? 'Product' }}"
                        data-fisherman="{{ $offer->fisherman->username ?? $offer->fisherman->email }}"
                        data-quantity="{{ $offer->quantity }}"
                        data-unit-price="{{ $offer->offered_price }}"
                        data-total="{{ $offer->offered_price * $offer->quantity }}"
                        data-date="{{ $offer->updated_at->format('F d, Y h:i A') }}"
                        data-status="{{ $offer->status }}"
                        onclick="showReceipt({
                            id: this.dataset.id,
                            product: this.dataset.product,
                            fisherman: this.dataset.fisherman,
                            quantity: parseFloat(this.dataset.quantity),
                            unit_price: parseFloat(this.dataset.unitPrice),
                            total: parseFloat(this.dataset.total),
                            date: this.dataset.date,
                            status: this.dataset.status
                        })">
                        <i class="fa-solid fa-receipt"></i> View Details
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Counter Offers Awaiting Response -->
        @if(isset($recentCounterOffers) && $recentCounterOffers->count() > 0)
        <div class="section-title" style="margin-top:30px;">Counter Offers Awaiting Your Response</div>
        <div class="offer-list">
            @foreach($recentCounterOffers as $offer)
            <div class="offer-item" style="background:#fff8e6;">
                <div class="offer-info">
                    <div class="offer-name">
                        <i class="fa-solid fa-hourglass-half" style="color: #d97706; margin-right: 8px;"></i>
                        {{ $offer->product->name ?? 'Product' }}
                    </div>
                    <div class="offer-details">
                        Fisherman: {{ $offer->fisherman->username ?? $offer->fisherman->email }} • Counter: ₱{{ number_format($offer->fisherman_counter_price, 2) }}
                        <span style="color:#999; margin-left:10px;">Sent {{ $offer->responded_at?->diffForHumans() }}</span>
                        @if($offer->expires_at)
                        <span style="color:#b91c1c; margin-left:10px;">Expires {{ $offer->expires_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
                <div style="display:flex; gap:8px; align-items:center;">
                    <form method="POST" action="{{ route('vendor.offers.accept-counter', $offer) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm" style="background:#16a34a; color:#fff; border:none; padding:8px 12px; border-radius:6px;">Accept Counter</button>
                    </form>
                    <form method="POST" action="{{ route('vendor.offers.decline-counter', $offer) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm" style="background:#dc2626; color:#fff; border:none; padding:8px 12px; border-radius:6px;">Reject</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    </div>

    <!-- Toasts handled by shared partial via vendor.nav -->

    <!-- Receipt Modal -->
    <div id="receiptModal" class="receipt-modal">
        <div class="receipt-content">
            <div class="receipt-header">
                <h2><i class="fa-solid fa-receipt"></i> Transaction Receipt</h2>
                <div class="receipt-date" id="receiptDate"></div>
            </div>
            <div class="receipt-body">
                <div class="receipt-row">
                    <span class="receipt-label">Transaction ID</span>
                    <span class="receipt-value" id="receiptId"></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Product</span>
                    <span class="receipt-value" id="receiptProduct"></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Fisherman</span>
                    <span class="receipt-value" id="receiptFisherman"></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Quantity</span>
                    <span class="receipt-value" id="receiptQuantity"></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Unit Price</span>
                    <span class="receipt-value" id="receiptUnitPrice"></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Status</span>
                    <span id="receiptStatus"></span>
                </div>
                <div class="receipt-total">
                    <span class="receipt-total-label">Total Amount</span>
                    <span class="receipt-total-value" id="receiptTotal"></span>
                </div>
            </div>
            <div class="receipt-footer">
                <button class="close-receipt" onclick="closeReceipt()">Close</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        function showReceipt(data) {
            document.getElementById('receiptId').textContent = '#' + data.id;
            document.getElementById('receiptDate').textContent = data.date;
            document.getElementById('receiptProduct').textContent = data.product;
            document.getElementById('receiptFisherman').textContent = data.fisherman;
            document.getElementById('receiptQuantity').textContent = data.quantity + ' kg';
            document.getElementById('receiptUnitPrice').textContent = '₱' + data.unit_price.toFixed(2) + '/kg';
            document.getElementById('receiptTotal').textContent = '₱' + data.total.toFixed(2);
            document.getElementById('receiptStatus').innerHTML = '<span class="receipt-status accepted">' + data.status + '</span>';
            document.getElementById('receiptModal').classList.add('active');
        }

        function closeReceipt() {
            document.getElementById('receiptModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('receiptModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReceipt();
            }
        });

        // Revenue & Spending Dual-Axis Chart
        const canvas = document.getElementById('revenueChart');
        if (canvas && window.Chart) {
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels ?? []),
                datasets: [
                    {
                        label: 'Income (₱)',
                        data: @json($chartIncomeValues ?? []),
                        borderColor: '#0075B5',
                        backgroundColor: 'rgba(0, 117, 181, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#0075B5',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Spending (₱)',
                        data: @json($chartSpendingValues ?? []),
                        borderColor: '#0075B5',
                        backgroundColor: 'rgba(0, 117, 181, 0.05)',
                        borderWidth: 3,
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#0075B5',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: { size: 13, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        bodySpacing: 6,
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = context.parsed.y;
                                return label + ': ₱' + value.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            },
                            footer: function(tooltipItems) {
                                if (tooltipItems.length >= 2) {
                                    const income = tooltipItems[0].parsed.y;
                                    const spending = tooltipItems[1].parsed.y;
                                    const net = income - spending;
                                    return '\nNet: ₱' + net.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                                return '';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        position: 'left',
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString('en-PH');
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        }
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return 'Income: ₱' + context.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString('en-PH');
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        }

        // Messaging removed; no unread polling on vendor dashboard.
    </script>
    

</body>
</html>
