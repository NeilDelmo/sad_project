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

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .product-name {
            font-size: 20px;
            font-weight: bold;
            color: #1B5E88;
            margin-bottom: 10px;
        }

        .product-details {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .product-price {
            font-size: 24px;
            font-weight: bold;
            color: #B12704;
            margin-bottom: 15px;
        }

        .btn-purchase {
            background: #0075B5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }

        .btn-purchase:hover {
            background: #1B5E88;
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
                <div class="product-name">
                    <i class="fa-solid fa-fish" style="color: #0075B5;"></i> {{ $product->name }}
                </div>
                <div class="product-details">
                    <div><strong>Category:</strong> {{ $product->category->name ?? '—' }}</div>
                    <div><strong>Available:</strong> {{ $product->available_quantity }} kg</div>
                    <div><strong>Supplier:</strong> {{ $product->supplier->username ?? 'User #'.$product->supplier_id }}</div>
                    <div style="color: #999; font-size: 12px; margin-top: 5px;">Posted {{ $product->created_at->diffForHumans() }}</div>
                </div>
                <div class="product-price">₱{{ number_format($product->unit_price, 2) }}/kg</div>
                <form action="{{ route('vendor.inventory.purchase', ['product' => $product->id]) }}" method="post">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 12px;">Quantity (kg)</label>
                        <input type="number" name="quantity" min="1" max="{{ $product->available_quantity }}" class="form-control form-control-sm" placeholder="Qty" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 12px;">Your Buy Price (₱/kg)</label>
                        <input type="number" step="0.01" name="purchase_price" class="form-control form-control-sm" placeholder="Price" required>
                    </div>
                    <button class="btn-purchase"><i class="fa-solid fa-cart-plus"></i> Purchase</button>
                </form>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: #999;">
                <i class="fa-solid fa-fish" style="font-size: 64px; color: #ddd; display: block; margin-bottom: 20px;"></i>
                <h3 style="color: #1B5E88;">No Products Found</h3>
                <p>Try adjusting your filters or check back later for new listings.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 30px;">
        {{ $products->links() }}
    </div>
</div>

</body>
</html>
