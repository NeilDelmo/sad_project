<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('bootstrap5/css/bootstrap.min.css') }}" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>SeaLedger - Fisherman Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');

        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-brand {
            color: white;
            font-size: 28px;
            font-weight: bold;
            font-family: "Koulen", sans-serif;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .nav-links {
            display: flex;
            gap: 10px;
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

        .dashboard-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
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

        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
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

        .section-title {
            font-family: "Koulen", sans-serif;
            font-size: 28px;
            color: #1B5E88;
            margin-bottom: 20px;
            border-bottom: 3px solid #0075B5;
            padding-bottom: 10px;
        }

        .product-list {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-info {
            flex-grow: 1;
        }

        .product-name {
            font-size: 18px;
            font-weight: bold;
            color: #1B5E88;
            margin-bottom: 5px;
        }

        .product-details {
            font-size: 14px;
            color: #666;
        }

        .product-price {
            font-size: 20px;
            font-weight: bold;
            color: #B12704;
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
            margin-left: 15px;
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

        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }

        .toast {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            margin-bottom: 15px;
            padding: 16px;
            display: flex;
            align-items: start;
            gap: 12px;
            animation: slideInRight 0.3s ease;
            cursor: pointer;
            transition: all 0.3s;
            border-left: 4px solid #0075B5;
        }

        .toast:hover {
            transform: translateX(-5px);
            box-shadow: 0 6px 25px rgba(0,0,0,0.25);
        }

        .toast.fade-out {
            animation: slideOutRight 0.3s ease forwards;
        }

        @keyframes slideInRight {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(400px); opacity: 0; }
        }

        .toast-icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .toast-content {
            flex: 1;
            min-width: 0;
        }

        .toast-title {
            font-weight: 700;
            color: #1B5E88;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .toast-message {
            color: #666;
            font-size: 13px;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .toast-time {
            color: #999;
            font-size: 11px;
        }

        .toast-close {
            flex-shrink: 0;
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .toast-close:hover {
            background: #f0f0f0;
            color: #666;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .empty-state i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }

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

    @include('fisherman.partials.nav')

    @include('partials.toast-notifications')

    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-title">Fisherman Dashboard</div>
            <p style="font-size: 18px; color: #666; margin-bottom: 0;">Welcome back, {{ Auth::user()->username ?? Auth::user()->email }}!</p>
            <p style="font-size: 16px; color: #999;">Manage your products and connect with buyers</p>

            <div class="action-buttons">
                <a href="{{ route('fisherman.products.create') }}" class="btn-primary-custom">
                    <i class="fa-solid fa-plus"></i>
                    Add New Product
                </a>
                <a href="{{ route('fisherman.products.index') }}" class="btn-secondary-custom">
                    <i class="fa-solid fa-box"></i>
                    View All Products
                </a>
                <a href="{{ route('rentals.index') }}" class="btn-secondary-custom">
                    <i class="fa-solid fa-toolbox"></i>
                    Rent Equipment
                    @if(isset($pendingRentalsCount) && $pendingRentalsCount > 0)
                    <span style="background: #ffc107; color: white; padding: 2px 8px; border-radius: 12px; font-size: 14px;">{{ $pendingRentalsCount }} pending</span>
                    @endif
                </a>
                <a href="{{ route('fisherman.offers.index') }}" class="btn-secondary-custom">
                    <i class="fa-solid fa-handshake"></i>
                    Offers
                </a>
                <a href="{{ route('fishing-safety.public') }}" class="btn-secondary-custom">
                    <i class="fa-solid fa-life-ring"></i>
                    Check Safety Map
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-fish"></i>
                </div>
                <div class="stat-number">{{ $productsCount ?? 0 }}</div>
                <div class="stat-label">Total Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <div class="stat-number">{{ $pendingOffersCount ?? 0 }}</div>
                <div class="stat-label">Pending Offers</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-peso-sign"></i>
                </div>
                <div class="stat-number" style="color: #16a34a;">₱{{ number_format($totalIncome ?? 0, 2) }}</div>
                <div class="stat-label">Total Income (Received Orders)</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-money-bill-trend-up"></i>
                </div>
                <div class="stat-number" style="color: #dc2626;">₱{{ number_format($totalSpending ?? 0, 2) }}</div>
                <div class="stat-label">Total Spending (Rentals)</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <div class="stat-number">{{ $acceptedOffersCount ?? 0 }}</div>
                <div class="stat-label">Accepted Offers</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-toolbox"></i>
                </div>
                <div class="stat-number">{{ $activeRentalsCount ?? 0 }}</div>
                <div class="stat-label">Active Rentals</div>
                @if(isset($pendingRentalsCount) && $pendingRentalsCount > 0)
                <div style="margin-top: 10px; background: #ffc107; color: white; padding: 5px 10px; border-radius: 12px; font-size: 12px; display: inline-block;">
                    <i class="fa-solid fa-clock"></i> {{ $pendingRentalsCount }} Pending
                </div>
                @endif
            </div>
        </div>

        <!-- Income Chart -->
        <div class="section-title">Income Trend (Last 14 Days)</div>
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <canvas id="incomeChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Recent Products -->
        <div class="section-title">Recent Products</div>
        <div class="product-list">
            @if(isset($recentProducts) && $recentProducts->count() > 0)
                @foreach($recentProducts as $product)
                <div class="product-item">
                    <div class="product-info">
                        <div class="product-name">
                            <i class="fa-solid fa-fish" style="color: #0075B5; margin-right: 8px;"></i>
                            {{ $product->name }}
                        </div>
                        <div class="product-details">
                            {{ $product->available_quantity }} kg available • 
                            @if($product->freshness_level)
                                <span class="badge 
                                    @if($product->freshness_level == 'Fresh') bg-success
                                    @elseif($product->freshness_level == 'Good') bg-info
                                    @elseif($product->freshness_level == 'Aging') bg-warning text-dark
                                    @elseif($product->freshness_level == 'Stale') bg-warning text-dark
                                    @else bg-danger
                                    @endif">
                                    {{ $product->freshness_level }}
                                </span>
                            @else
                                {{ $product->freshness_metric ?? 'Fresh' }}
                            @endif
                            <span style="color: #999; margin-left: 10px;">Posted {{ $product->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="product-price">₱{{ number_format($product->unit_price, 2) }}/kg</div>
                    <div>
                        <a href="{{ route('fisherman.products.edit', $product->id) }}" style="color: #0075B5; text-decoration: none; margin-right: 15px;">
                            <i class="fa-solid fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                @endforeach
                <div style="text-align: center; padding-top: 20px;">
                    <a href="{{ route('fisherman.products.index') }}" style="color: #0075B5; text-decoration: none; font-weight: bold;">
                        View All Products <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            @else
                <div class="empty-state">
                    <i class="fa-solid fa-fish"></i>
                    <h3 style="color: #1B5E88; margin-bottom: 10px;">No Products Yet</h3>
                    <p style="margin-bottom: 20px;">Start selling by adding your first product!</p>
                    <a href="{{ route('fisherman.products.create') }}" class="btn-primary-custom">
                        <i class="fa-solid fa-plus"></i>
                        Add Your First Product
                    </a>
                </div>
            @endif
        </div>

        <!-- Recent Transaction History -->
        @if(isset($recentAcceptedOffers) && $recentAcceptedOffers->count() > 0)
        <div class="section-title" style="margin-top: 30px;">Recent Transaction History</div>
        <div class="product-list">
            @foreach($recentAcceptedOffers as $offer)
            <div class="product-item">
                <div class="product-info">
                    <div class="product-name">
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
                    <div class="product-details">
                        Vendor: {{ $offer->vendor->username ?? $offer->vendor->email }}
                        • {{ $offer->quantity }} kg
                        • <span class="status-badge status-{{ $offer->status }}">{{ ucfirst($offer->status) }}</span>
                        <span style="color: #999; margin-left: 10px;">{{ $offer->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div style="display: flex; align-items: center;">
                    <div class="product-price" style="color: {{ $offer->status === 'accepted' ? '#16a34a' : ($offer->status === 'rejected' ? '#dc2626' : '#666') }};">₱{{ number_format($offer->offered_price * $offer->quantity, 2) }}</div>
                    <button class="btn-view-details" onclick="showReceipt({{ json_encode([
                        'id' => $offer->id,
                        'product' => $offer->product->name ?? 'Product',
                        'vendor' => $offer->vendor->username ?? $offer->vendor->email,
                        'quantity' => $offer->quantity,
                        'unit_price' => $offer->offered_price,
                        'total' => $offer->offered_price * $offer->quantity,
                        'date' => $offer->updated_at->format('F d, Y h:i A'),
                        'status' => $offer->status,
                    ]) }})">
                        <i class="fa-solid fa-receipt"></i> View Details
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // No message polling; messaging removed. Dashboard scripts kept minimal.
    </script>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="receipt-modal" onclick="if(event.target.id === 'receiptModal') closeReceipt()">
        <div class="receipt-content">
            <div class="receipt-header">
                <h2><i class="fa-solid fa-receipt"></i> Transaction Receipt</h2>
                <div class="receipt-date" id="receiptDate"></div>
            </div>
            <div class="receipt-body">
                <div class="receipt-row">
                    <span class="receipt-label">Transaction ID:</span>
                    <span class="receipt-value" id="receiptId"></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Product:</span>
                    <span class="receipt-value" id="receiptProduct"></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Vendor:</span>
                    <span class="receipt-value" id="receiptVendor"></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Quantity:</span>
                    <span class="receipt-value" id="receiptQuantity"></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Unit Price:</span>
                    <span class="receipt-value" id="receiptUnitPrice"></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Status:</span>
                    <span id="receiptStatus"></span>
                </div>
                <div class="receipt-total">
                    <span class="receipt-total-label">Total Amount:</span>
                    <span class="receipt-total-value" id="receiptTotal"></span>
                </div>
            </div>
            <div class="receipt-footer">
                <button class="close-receipt" onclick="closeReceipt()">Close</button>
            </div>
        </div>
    </div>

    <script>
        function showReceipt(data) {
            document.getElementById('receiptId').textContent = '#' + data.id;
            document.getElementById('receiptProduct').textContent = data.product;
            document.getElementById('receiptVendor').textContent = data.vendor;
            document.getElementById('receiptQuantity').textContent = data.quantity + ' kg';
            document.getElementById('receiptUnitPrice').textContent = '₱' + parseFloat(data.unit_price).toFixed(2);
            document.getElementById('receiptDate').textContent = data.date;
            document.getElementById('receiptTotal').textContent = '₱' + parseFloat(data.total).toFixed(2);
            
            const statusElement = document.getElementById('receiptStatus');
            statusElement.className = 'receipt-status ' + data.status.toLowerCase();
            statusElement.textContent = data.status;
            
            document.getElementById('receiptModal').classList.add('active');
        }

        function closeReceipt() {
            document.getElementById('receiptModal').classList.remove('active');
        }

        // Income Line Chart
        const canvas = document.getElementById('incomeChart');
        if (canvas && window.Chart) {
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels ?? []),
                datasets: [{
                    label: 'Daily Income (₱)',
                    data: @json($chartValues ?? []),
                    borderColor: '#0075B5',
                    backgroundColor: 'rgba(0, 117, 181, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#0075B5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
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
    </script>
    

</body>
</html>
