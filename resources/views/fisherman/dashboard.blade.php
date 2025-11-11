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

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="nav-brand" href="{{ route('marketplace.index') }}" style="text-decoration: none;">🐟 SeaLedger</a>
            <div class="nav-links">
                <a href="{{ route('fisherman.dashboard') }}" class="nav-link active">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a href="{{ route('fisherman.products.index') }}" class="nav-link">
                    <i class="fa-solid fa-box"></i> My Products
                </a>
                <a href="{{ route('fisherman.messages') }}" class="nav-link">
                    <i class="fa-solid fa-envelope"></i> Messages
                </a>
                <a href="{{ route('fishing-safety.public') }}" class="nav-link">
                    <i class="fa-solid fa-life-ring"></i> Safety Map
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
                <a href="{{ route('fisherman.messages') }}" class="btn-secondary-custom">
                    <i class="fa-solid fa-envelope"></i>
                    Messages
                    @if(isset($unreadCount) && $unreadCount > 0)
                    <span style="background: #dc3545; color: white; padding: 2px 8px; border-radius: 12px; font-size: 14px;">{{ $unreadCount }}</span>
                    @endif
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
                    <i class="fa-solid fa-message"></i>
                </div>
                <div class="stat-number">{{ $unreadCount ?? 0 }}</div>
                <div class="stat-label">Unread Messages</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-number">{{ isset($recentConversations) ? $recentConversations->count() : 0 }}</div>
                <div class="stat-label">Active Conversations</div>
            </div>
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
                            {{ $product->available_quantity }} kg available • {{ $product->freshness_metric ?? 'Fresh' }}
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
    </div>

</body>
</html>
