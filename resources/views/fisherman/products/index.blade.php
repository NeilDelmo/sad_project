<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap5/css/bootstrap.min.css') }}" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>SeaLedger - My Products</title>
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
            padding: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            margin: 0;
        }

        .nav-brand {
            color: white;
            font-size: 28px;
            font-weight: bold;
            font-family: "Koulen", sans-serif;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-logo {
            height: 40px;
            width: auto;
        }

        .nav-links {
            display: flex;
            gap: 10px;
            margin-left: auto;
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

        .container-main {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
        }

        /* keep the inner layout spacing of the navbar, but snap it to the viewport edge */
        .navbar .nav-layout { padding: 12px 20px; }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-family: "Koulen", sans-serif;
            font-size: 42px;
            color: #1B5E88;
            margin: 0;
        }

        .btn-add {
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

        .btn-add:hover {
            background: #1B5E88;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,117,181,0.3);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .product-image {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #E7FAFE 0%, #B3E5F5 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .product-name {
            font-size: 22px;
            font-weight: bold;
            color: #1B5E88;
            margin-bottom: 10px;
        }

        .product-category {
            display: inline-block;
            background: #E7FAFE;
            color: #0075B5;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 28px;
            font-weight: bold;
            color: #B12704;
            margin: 15px 0;
        }

        .product-details {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .product-details i {
            color: #0075B5;
            margin-right: 8px;
            width: 20px;
        }

        .freshness-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            margin-top: 10px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .btn-edit {
            flex: 1;
            background: #0075B5;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s;
        }

        .btn-edit:hover {
            background: #1B5E88;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .empty-state {
            text-align: center;
            padding: 80px 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .empty-state i {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 25px;
        }

        .empty-state h2 {
            font-family: "Koulen", sans-serif;
            font-size: 36px;
            color: #1B5E88;
            margin-bottom: 15px;
        }

        .empty-state p {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
    </style>
</head>
<body>

    @include('fisherman.partials.nav')

    <div class="container-main">
        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
        @endif

        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">My Products</h1>
            <a href="{{ route('fisherman.products.create') }}" class="btn-add">
                <i class="fa-solid fa-plus"></i>
                Add New Product
            </a>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
        <div class="products-grid">
            @foreach($products as $product)
            <div class="product-card">
                <div class="product-image">
                    @if($product->image_path)
                        <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                    @else
                        <i class="fa-solid fa-fish fa-4x" style="color: #0075B5;"></i>
                    @endif
                </div>
                
                @if($product->category)
                <span class="product-category">{{ $product->category->name }}</span>
                @endif
                
                <div class="product-name">{{ $product->name }}</div>
                
                <div class="product-price">₱{{ number_format($product->unit_price, 2) }}/kg</div>
                
                <div class="product-details">
                    <i class="fa-solid fa-box"></i>
                    <strong>Stock:</strong> {{ $product->available_quantity }} kg
                </div>
                
                @if($product->description)
                <div class="product-details">
                    <i class="fa-solid fa-info-circle"></i>
                    {{ Str::limit($product->description, 60) }}
                </div>
                @endif
                
                @if($product->freshness_metric)
                <span class="freshness-badge">
                    🌟 {{ $product->freshness_metric }}
                </span>
                @endif
                
                <div class="product-details" style="margin-top: 12px; font-size: 12px; color: #999;">
                    <i class="fa-solid fa-clock"></i>
                    Posted {{ $product->created_at->diffForHumans() }}
                </div>

                <div class="product-actions">
                    @if($product->is_edit_locked ?? false)
                        <button type="button" class="btn-edit" disabled style="opacity: 0.5; cursor: not-allowed;">
                            <i class="fa-solid fa-lock"></i> Locked
                        </button>
                        <small style="display:block;margin-top:6px;color:#dc3545;font-size:12px;">
                            Editing disabled while offers/transactions are active or stock is 0.
                        </small>
                    @else
                        <a href="{{ route('fisherman.products.edit', $product->id) }}" class="btn-edit">
                            <i class="fa-solid fa-edit"></i> Edit
                        </a>
                    @endif
                    <form action="{{ route('fisherman.products.destroy', $product->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fa-solid fa-fish"></i>
            <h2>No Products Yet</h2>
            <p>Start selling your fresh catch! Add your first product to reach buyers.</p>
            <a href="{{ route('fisherman.products.create') }}" class="btn-add">
                <i class="fa-solid fa-plus"></i>
                Add Your First Product
            </a>
        </div>
        @endif
    </div>

    @include('partials.message-notification')

</body>
</html>
