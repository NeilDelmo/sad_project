<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gear Rental Catalog - SeaLedger</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #1B5E88;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
        }

        .nav-bar {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-link {
            text-decoration: none;
            color: #1B5E88;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background: #E7FAFE;
        }

        .gear-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .gear-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .gear-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .gear-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f0f0f0;
        }

        .gear-info {
            padding: 15px;
        }

        .gear-name {
            font-size: 18px;
            font-weight: bold;
            color: #1B5E88;
            margin-bottom: 10px;
        }

        .gear-description {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .gear-price {
            font-size: 20px;
            font-weight: bold;
            color: #0075B5;
            margin-bottom: 10px;
        }

        .gear-stock {
            font-size: 12px;
            color: #999;
            margin-bottom: 15px;
        }

        .stock-available {
            color: #22c55e;
        }

        .stock-low {
            color: #f59e0b;
        }

        .rent-button {
            width: 100%;
            padding: 10px;
            background: #1B5E88;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: background 0.2s;
        }

        .rent-button:hover {
            background: #0075B5;
        }

        .empty-state {
            background: white;
            padding: 60px 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .empty-state h2 {
            color: #1B5E88;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛠️ Gear Rental Catalog</h1>
            <p>Browse and rent fishing gear from our organization</p>
        </div>

        <div class="nav-bar">
            <a href="{{ route('marketplace.index') }}" class="nav-link">🐟 Marketplace</a>
            <a href="{{ route('rentals.index') }}" class="nav-link" style="background: #E7FAFE;">🛠️ Gear Rentals</a>
            @auth
                <a href="{{ route('rentals.myrentals') }}" class="nav-link">📋 My Rentals</a>
                <a href="{{ route('dashboard') }}" class="nav-link">🏠 Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="nav-link">🔑 Login</a>
            @endauth
        </div>

        @if($gearItems->count() > 0)
            <div class="gear-grid">
                @foreach($gearItems as $gear)
                    <div class="gear-card">
                        @if($gear->image_path)
                            <img src="{{ asset($gear->image_path) }}" alt="{{ $gear->name }}" class="gear-image">
                        @else
                            <div class="gear-image" style="display: flex; align-items: center; justify-content: center; font-size: 48px;">
                                🛠️
                            </div>
                        @endif
                        
                        <div class="gear-info">
                            <div class="gear-name">{{ $gear->name }}</div>
                            <div class="gear-description">{{ Str::limit($gear->description, 80) }}</div>
                            <div class="gear-price">₱{{ number_format($gear->rental_price_per_day, 2) }}/day</div>
                            <div class="gear-stock {{ $gear->rental_stock > 5 ? 'stock-available' : 'stock-low' }}">
                                {{ $gear->rental_stock }} available
                            </div>
                            
                            @auth
                                <button onclick="window.location.href='{{ route('rentals.create', ['product_id' => $gear->id]) }}'" class="rent-button">
                                    Rent Now
                                </button>
                            @else
                                <button onclick="window.location.href='{{ route('login') }}'" class="rent-button">
                                    Login to Rent
                                </button>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <h2>No Gear Available</h2>
                <p>Currently, there are no gear items available for rent. Please check back later.</p>
            </div>
        @endif
    </div>
</body>
</html>
