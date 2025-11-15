<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('bootstrap5/css/bootstrap.min.css') }}" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>SeaLedger - Browse Products</title>
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
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            font-weight: 600;
        }

        .container-custom {
            max-width: 1400px;
            margin: 30px auto;
            padding: 20px;
        }

        .page-title {
            font-family: "Koulen", sans-serif;
            font-size: 36px;
            color: #1B5E88;
            margin-bottom: 20px;
        }

        .filter-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }

        .product-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            border-color: #0075B5;
        }

        .product-header {
            display: flex;
            align-items: start;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .product-name {
            font-size: 20px;
            font-weight: 600;
            color: #1B5E88;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .product-category {
            display: inline-block;
            background: #E7FAFE;
            color: #0075B5;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .product-details {
            color: #666;
            font-size: 14px;
            margin-bottom: 16px;
            line-height: 1.8;
        }

        .product-detail-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
        }

        .detail-label {
            color: #999;
            font-weight: 500;
        }

        .detail-value {
            color: #333;
            font-weight: 600;
        }

        .product-price {
            font-size: 28px;
            font-weight: 700;
            color: #1B5E88;
            margin: 20px 0;
            padding: 16px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            text-align: center;
        }

        .price-label {
            font-size: 12px;
            color: #666;
            font-weight: 500;
            display: block;
            margin-bottom: 4px;
        }

        .offer-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
        }

        .offer-input-group {
            margin-bottom: 14px;
        }

        .offer-label {
            font-size: 13px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 6px;
            display: block;
        }

        .form-control-custom {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s;
        }

        .form-control-custom:focus {
            border-color: #0075B5;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 117, 181, 0.1);
        }

        .btn-make-offer {
            background: linear-gradient(135deg, #0075B5 0%, #1B5E88 100%);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 117, 181, 0.3);
        }

        .btn-make-offer:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 117, 181, 0.4);
        }

        .btn-make-offer i {
            margin-right: 8px;
        }

        .supplier-info {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f0f0f0;
            font-size: 13px;
            color: #666;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 72px;
            color: #ddd;
            margin-bottom: 24px;
            display: block;
        }

        .empty-state h3 {
            color: #1B5E88;
            margin-bottom: 12px;
            font-size: 24px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="nav-brand" href="{{ route('marketplace.index') }}" style="text-decoration: none;">🐟 SeaLedger</a>
            <div class="nav-links">
                <a href="{{ route('vendor.dashboard') }}" class="nav-link">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a href="{{ route('vendor.products.index') }}" class="nav-link active">
                    <i class="fa-solid fa-fish"></i> Browse Products
                </a>
                <a href="{{ route('vendor.inventory.index') }}" class="nav-link">
                    <i class="fa-solid fa-box"></i> My Inventory
                </a>
                <a href="{{ route('marketplace.index') }}" class="nav-link">
                    <i class="fa-solid fa-store"></i> Marketplace
                </a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-link" style="background: none; border: none; cursor: pointer;">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-custom">
        <div class="page-title">Browse Fisherman Products</div>

    <form method="get" class="filter-card">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Product name or description">
            </div>
            <div class="col-md-2">
                <div class="form-check">
                    <input type="checkbox" name="only_fish" value="1" class="form-check-input" id="onlyFish" @if($onlyFish) checked @endif>
                    <label class="form-check-label" for="onlyFish">Only Fish</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check">
                    <input type="checkbox" name="apply_filters" value="1" class="form-check-input" id="applyFilters" @if($applyFilters) checked @endif>
                    <label class="form-check-label" for="applyFilters">My Preferences</label>
                </div>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100"><i class="fa-solid fa-filter"></i> Apply</button>
            </div>
        </div>
    </form>

    <div class="product-grid">
        @forelse($products as $product)
            <div class="product-card">
                <div class="product-header">
                    <div>
                        <div class="product-name">
                            <i class="fa-solid fa-fish" style="color: #0075B5;"></i> {{ $product->name }}
                        </div>
                        <span class="product-category">{{ $product->category->name ?? 'Uncategorized' }}</span>
                    </div>
                </div>

                <div class="product-details">
                    <div class="product-detail-row">
                        <span class="detail-label">Available</span>
                        <span class="detail-value">{{ $product->available_quantity }} kg</span>
                    </div>
                    <div class="product-detail-row">
                        <span class="detail-label">Quality</span>
                        <span class="detail-value">{{ $product->freshness_metric ?? 'Good' }}</span>
                    </div>
                    <div class="product-detail-row">
                        <span class="detail-label">Listed</span>
                        <span class="detail-value">{{ $product->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <div class="product-price">
                    <span class="price-label">Fisherman's Asking Price</span>
                    ₱{{ number_format($product->unit_price, 2) }}/kg
                </div>

                <div class="offer-section">
                    <form action="{{ route('vendor.offers.store', $product) }}" method="post">
                        @csrf
                        <div class="offer-input-group">
                            <label class="offer-label"><i class="fa-solid fa-weight"></i> Quantity (kg)</label>
                            <input type="number" name="quantity" min="1" max="{{ $product->available_quantity }}" 
                                   class="form-control-custom" placeholder="Enter quantity" required>
                        </div>
                        <div class="offer-input-group">
                            <label class="offer-label"><i class="fa-solid fa-peso-sign"></i> Your Offer Price (₱/kg)</label>
                            <input type="number" step="0.01" name="offered_price" 
                                   class="form-control-custom" placeholder="Enter your offer" required>
                        </div>
                        <div class="offer-input-group">
                            <label class="offer-label"><i class="fa-solid fa-message"></i> Message (Optional)</label>
                            <textarea name="message" rows="2" class="form-control-custom" 
                                      placeholder="Add a message to the fisherman..." maxlength="500"></textarea>
                        </div>
                        <button type="submit" class="btn-make-offer">
                            <i class="fa-solid fa-handshake"></i> Make Offer
                        </button>
                    </form>
                </div>

                <div class="supplier-info">
                    <i class="fa-solid fa-user"></i>
                    <span>{{ $product->supplier->username ?? 'Fisherman #'.$product->supplier_id }}</span>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fa-solid fa-fish-fins"></i>
                <h3>No Products Available</h3>
                <p>Try adjusting your filters or check back later for new listings from fishermen.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 30px;">
        {{ $products->links() }}
    </div>
</div>

</body>
</html>
