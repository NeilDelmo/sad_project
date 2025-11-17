<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>SeaLedger - Browse Products</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
            padding: 18px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-brand {
            color: white;
            font-size: 32px;
            font-weight: bold;
            font-family: "Koulen", sans-serif;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            font-weight: 600;
        }

        .nav-link i {
            margin-right: 6px;
        }

        .container-custom {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 30px;
        }

        .page-header {
            margin-bottom: 35px;
        }

        .page-title {
            font-family: "Koulen", sans-serif;
            font-size: 42px;
            color: #1B5E88;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .page-subtitle {
            color: #6c757d;
            font-size: 16px;
        }

        .filter-card {
            background: white;
            padding: 28px 32px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 35px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .filter-card .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .filter-card .form-control {
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .filter-card .form-control:focus {
            border-color: #0075B5;
            box-shadow: 0 0 0 3px rgba(0, 117, 181, 0.1);
        }

        .filter-card .form-check {
            padding: 12px 16px;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 0;
        }

        .filter-card .form-check-input {
            cursor: pointer;
            margin-top: 0.25em;
        }

        .filter-card .form-check-label {
            cursor: pointer;
            font-weight: 500;
            color: #495057;
        }

        .filter-card .btn-primary {
            background: linear-gradient(135deg, #0075B5 0%, #1B5E88 100%);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 117, 181, 0.3);
            transition: all 0.3s ease;
        }

        .filter-card .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 117, 181, 0.4);
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 28px;
            margin-bottom: 40px;
        }

        .product-card {
            background: white;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
            height: fit-content;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.12);
            border-color: #0075B5;
        }

        .product-header {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f0f0f0;
        }

        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            background: #f8f9fa;
        }

        .product-image-placeholder {
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 48px;
        }

        .product-name {
            font-size: 22px;
            font-weight: 700;
            color: #1B5E88;
            margin-bottom: 10px;
            line-height: 1.3;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-name i {
            font-size: 20px;
        }

        .product-category {
            display: inline-block;
            background: linear-gradient(135deg, #E7FAFE 0%, #d4f4fa 100%);
            color: #0075B5;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .product-details {
            background: #f8f9fa;
            padding: 18px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .product-detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .product-detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #6c757d;
            font-weight: 500;
            font-size: 14px;
        }

        .detail-value {
            color: #2c3e50;
            font-weight: 700;
            font-size: 14px;
        }

        .product-price {
            font-size: 32px;
            font-weight: 800;
            color: #1B5E88;
            margin: 24px 0;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            text-align: center;
            border: 2px dashed #dee2e6;
        }

        .price-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .offer-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 24px;
            border-radius: 12px;
            margin-top: 24px;
            border: 2px solid #e9ecef;
        }

        .offer-input-group {
            margin-bottom: 18px;
        }

        .offer-input-group:last-of-type {
            margin-bottom: 20px;
        }

        .offer-label {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .offer-label i {
            color: #0075B5;
            font-size: 14px;
        }

        .form-control-custom {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s;
            font-family: inherit;
        }

        .form-control-custom:focus {
            border-color: #0075B5;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 117, 181, 0.1);
        }

        .form-control-custom::placeholder {
            color: #adb5bd;
        }

        .btn-make-offer {
            background: linear-gradient(135deg, #0075B5 0%, #1B5E88 100%);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 117, 181, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-make-offer:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 117, 181, 0.5);
        }

        .btn-make-offer:active {
            transform: translateY(0);
        }

        .supplier-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            padding-top: 18px;
            border-top: 2px solid #f0f0f0;
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }

        .supplier-info i {
            color: #0075B5;
            font-size: 16px;
        }

        .empty-state {
            text-align: center;
            padding: 100px 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }

        .empty-state i {
            font-size: 80px;
            color: #dee2e6;
            margin-bottom: 24px;
            display: block;
        }

        .empty-state h3 {
            color: #1B5E88;
            margin-bottom: 12px;
            font-size: 28px;
            font-weight: 700;
        }

        .empty-state p {
            color: #6c757d;
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                gap: 4px;
            }

            .product-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .container-custom {
                padding: 30px 20px;
            }

            .page-title {
                font-size: 32px;
            }

            .filter-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    @include('vendor.partials.nav')>

    <div class="container-custom">
        <div class="page-header">
            <div class="page-title">Browse Fisherman Products</div>
            <div class="page-subtitle">Discover fresh catches and make your offers</div>
        </div>

        <!-- Filter Section -->
        <form method="get" class="filter-card">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Search Products</label>
                    <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Product name or description">
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input type="checkbox" name="apply_filters" value="1" class="form-check-input" id="applyFilters" @if($applyFilters) checked @endif>
                        <label class="form-check-label" for="applyFilters">
                            <i class="fa-solid fa-sliders"></i> Apply My Preferences
                            @if($prefs)
                                <small class="text-muted d-block">Filters by your saved category, price & quantity preferences</small>
                            @else
                                <small class="text-muted d-block">Set preferences in onboarding to use this filter</small>
                            @endif
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100"><i class="fa-solid fa-filter"></i> Apply Filters</button>
                </div>
            </div>
        </form>

        <!-- Product Grid -->
        <div class="product-grid">
            @forelse($products as $product)
            <!-- Product Card -->
            <div class="product-card">
                @if($product->image_path)
                    <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="product-image">
                @else
                    <div class="product-image-placeholder">
                        <i class="fa-solid fa-fish"></i>
                    </div>
                @endif
                
                <div class="product-header">
                    <div class="product-name">
                        <i class="fa-solid fa-fish"></i>
                        <span>{{ $product->name }}</span>
                    </div>
                    <span class="product-category">{{ $product->category->name ?? 'Uncategorized' }}</span>
                </div>

                <div class="product-details">
                    <div class="product-detail-row">
                        <span class="detail-label">Available</span>
                        <span class="detail-value">{{ $product->available_quantity }} {{ $product->unit_of_measure ?? 'kg' }}</span>
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
                    ₱{{ number_format($product->unit_price, 2) }}/{{ $product->unit_of_measure ?? 'kg' }}
                </div>

                <div class="offer-section">
                    <form action="{{ route('vendor.offers.store', $product) }}" method="post">
                        @csrf
                        <div class="offer-input-group">
                            <label class="offer-label">
                                <i class="fa-solid fa-weight"></i>
                                Quantity ({{ $product->unit_of_measure ?? 'kg' }})
                            </label>
                            <input type="number" name="quantity" min="1" max="{{ $product->available_quantity }}" 
                                   class="form-control-custom" placeholder="Enter quantity" required>
                        </div>
                        <div class="offer-input-group">
                            <label class="offer-label">
                                <i class="fa-solid fa-peso-sign"></i>
                                Your Offer Price (₱/{{ $product->unit_of_measure ?? 'kg' }})
                            </label>
                            <input type="number" step="0.01" name="offered_price" 
                                   class="form-control-custom" placeholder="Enter your offer" required>
                        </div>
                        <div class="offer-input-group">
                            <label class="offer-label">
                                <i class="fa-solid fa-message"></i>
                                Message (Optional)
                            </label>
                            <textarea name="message" rows="2" class="form-control-custom" 
                                      placeholder="Add a message to the fisherman..." maxlength="500"></textarea>
                        </div>
                        <button type="submit" class="btn-make-offer">
                            <i class="fa-solid fa-handshake"></i>
                            <span>Make Offer</span>
                        </button>
                    </form>
                </div>

                <div class="supplier-info">
                    <i class="fa-solid fa-user"></i>
                    <span>{{ $product->supplier->name ?? $product->supplier->username ?? 'Fisherman #'.$product->supplier_id }}</span>
                </div>
            </div>
            @empty
            <div class="empty-state" style="grid-column: 1 / -1;">
                <i class="fa-solid fa-fish-fins"></i>
                <h3>No Products Available</h3>
                <p>Try adjusting your filters or check back later for new listings from fishermen.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div style="margin-top: 30px; text-align: center;">
            {{ $products->links() }}
        </div>
    </div>

    @include('partials.message-notification')

</body>
</html>