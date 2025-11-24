<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>Shopping Cart - SeaLedger</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); font-family: Arial, sans-serif; min-height: 100vh; }
        .navbar { background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%); padding: 12px 0; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
        .nav-brand { color: white; font-size: 32px; font-weight: bold; font-family: "Koulen", sans-serif; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .container-custom { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .cart-card { background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .cart-item { display: flex; align-items: center; gap: 20px; padding: 20px 0; border-bottom: 1px solid #eee; }
        .cart-item:last-child { border-bottom: none; }
        .item-image { width: 80px; height: 80px; border-radius: 8px; object-fit: cover; background: #f8f9fa; }
        .item-details { flex: 1; }
        .item-title { font-weight: 700; color: #1B5E88; font-size: 18px; margin-bottom: 4px; }
        .item-seller { font-size: 13px; color: #666; }
        .item-price { font-weight: 700; color: #1B5E88; font-size: 16px; }
        .qty-control { display: flex; align-items: center; gap: 10px; }
        .qty-input { width: 60px; text-align: center; }
        .remove-btn { color: #dc3545; background: none; border: none; font-size: 14px; cursor: pointer; }
        .remove-btn:hover { text-decoration: underline; }
        .cart-summary { background: #f8f9fa; padding: 20px; border-radius: 12px; margin-top: 20px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 16px; }
        .total-row { font-weight: 700; font-size: 20px; color: #1B5E88; border-top: 2px solid #ddd; padding-top: 10px; margin-top: 10px; }
        .checkout-btn { background: #16a34a; color: white; border: none; padding: 15px 30px; border-radius: 8px; font-weight: 700; width: 100%; font-size: 18px; margin-top: 20px; transition: .2s; }
        .checkout-btn:hover { background: #15803d; }
        .empty-cart { text-align: center; padding: 60px 20px; }
        .back-link { color: #0075B5; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 20px; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    @include('partials.message-notification')

    <nav class="navbar">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="nav-brand" href="{{ route('marketplace.index') }}">🐟 SeaLedger</a>
            @auth
                <a href="{{ route('dashboard') }}" class="text-white text-decoration-none fw-bold">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-white text-decoration-none fw-bold">Login</a>
            @endauth
        </div>
    </nav>

    <div class="container-custom">
        <a href="{{ route('marketplace.shop') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> Continue Shopping</a>
        
        <div class="cart-card">
            <h2 class="mb-4" style="color: #1B5E88; font-family: 'Koulen', sans-serif;">Your Shopping Cart</h2>

            @if(count($cartItems) > 0)
                <div class="cart-items">
                    @foreach($cartItems as $item)
                        <div class="cart-item">
                            @if($item['listing']->product->image_path)
                                <img src="{{ asset($item['listing']->product->image_path) }}" class="item-image" alt="{{ $item['listing']->product->name }}">
                            @else
                                <div class="item-image d-flex align-items-center justify-content-center text-muted"><i class="fa-solid fa-fish fa-2x"></i></div>
                            @endif
                            
                            <div class="item-details">
                                <div class="item-title">{{ $item['listing']->product->name }}</div>
                                <div class="item-seller">Seller: {{ $item['listing']->seller->name ?? $item['listing']->seller->username }}</div>
                                <div class="item-price mt-1">₱{{ number_format($item['price'], 2) }} / {{ $item['listing']->product->unit_of_measure ?? 'kg' }}</div>
                            </div>

                            <div class="qty-control">
                                <form action="{{ route('marketplace.cart.update') }}" method="POST" class="d-flex align-items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="listing_id" value="{{ $item['listing']->id }}">
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['max_quantity'] }}" class="form-control qty-input" onchange="this.form.submit()">
                                </form>
                            </div>

                            <div class="text-end" style="min-width: 100px;">
                                <div class="fw-bold text-success">₱{{ number_format($item['subtotal'], 2) }}</div>
                                <form action="{{ route('marketplace.cart.remove') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="listing_id" value="{{ $item['listing']->id }}">
                                    <button type="submit" class="remove-btn mt-1"><i class="fa-solid fa-trash"></i> Remove</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="cart-summary">
                    <div class="summary-row total-row">
                        <span>Total Amount</span>
                        <span>₱{{ number_format($total, 2) }}</span>
                    </div>
                    
                    <form action="{{ route('marketplace.checkout') }}" method="POST">
                        @csrf
                        <button type="submit" class="checkout-btn">
                            <i class="fa-solid fa-lock"></i> Proceed to Checkout
                        </button>
                    </form>
                    
                    <form action="{{ route('marketplace.cart.clear') }}" method="POST" class="text-center mt-3">
                        @csrf
                        <button type="submit" class="btn btn-link text-danger text-decoration-none" onclick="return confirm('Are you sure you want to clear your cart?')">Clear Cart</button>
                    </form>
                </div>
            @else
                <div class="empty-cart">
                    <i class="fa-solid fa-cart-shopping fa-4x text-muted mb-3"></i>
                    <h3 class="text-muted">Your cart is empty</h3>
                    <p class="text-muted">Looks like you haven't added any fresh catches yet.</p>
                    <a href="{{ route('marketplace.shop') }}" class="btn btn-primary mt-3" style="background: #0075B5; border: none;">Start Shopping</a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
