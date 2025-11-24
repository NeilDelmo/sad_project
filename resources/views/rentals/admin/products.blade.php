<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Rental Inventory - Admin - SeaLedger</title>
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-family: "Koulen", sans-serif;
            font-size: 36px;
            color: #1B5E88;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #666;
            font-size: 16px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: #1B5E88;
            color: white;
        }

        .btn-primary:hover {
            background: #154a6b;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #e2e3e5;
            color: #383d41;
        }

        .btn-secondary:hover {
            background: #d6d8db;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 14px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s;
        }

        .product-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            transform: translateY(-5px);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f0f0f0;
        }

        .product-content {
            padding: 20px;
        }

        .product-name {
            font-size: 18px;
            font-weight: bold;
            color: #1B5E88;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 20px;
            font-weight: bold;
            color: #10b981;
            margin-bottom: 15px;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
            color: #666;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-available {
            background: #d1fae5;
            color: #065f46;
        }

        .status-maintenance {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-retired {
            background: #e5e7eb;
            color: #374151;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #86efac;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    @include('admin.partials.nav')

    <div class="container">
        <div class="page-header">
            <div>
                <div class="page-title">Rental Inventory</div>
                <div class="page-subtitle">Manage equipment available for rent</div>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('rentals.admin.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-list-check"></i> Rental Requests
                </a>
                <a href="{{ route('rentals.admin.products.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Add Product
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <div class="products-grid">
            @foreach($products as $product)
                <div class="product-card">
                    <img src="{{ $product->image_path ? asset('storage/' . $product->image_path) : asset('images/placeholder-gear.jpg') }}" alt="{{ $product->name }}" class="product-image">
                    <div class="product-content">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div class="product-name">{{ $product->name }}</div>
                            <span class="status-badge status-{{ $product->equipment_status }}">
                                {{ ucfirst($product->equipment_status) }}
                            </span>
                        </div>
                        
                        <div class="product-price">₱{{ number_format($product->rental_price_per_day, 2) }} <span style="font-size: 14px; color: #666; font-weight: normal;">/ day</span></div>
                        
                        <div class="product-meta">
                            <div><i class="fa-solid fa-boxes-stacked"></i> Stock: {{ $product->rental_stock }}</div>
                            <div><i class="fa-solid fa-wrench"></i> Maint: {{ $product->maintenance_count ?? 0 }}</div>
                        </div>

                        <a href="{{ route('rentals.admin.products.edit', $product) }}" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">
                            <i class="fa-solid fa-pen-to-square"></i> Edit Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
