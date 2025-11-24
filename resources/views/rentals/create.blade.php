<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Rental Request - SeaLedger</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .container { max-width: 900px; margin: 0 auto; padding: 20px; }
        .header {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header h1 { color: #1B5E88; margin-bottom: 10px; }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .cart-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #1B5E88;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .item-info { flex: 1; }
        .item-name { font-weight: bold; color: #1B5E88; margin-bottom: 5px; }
        .item-price { color: #666; font-size: 14px; }
        .item-quantity {
            background: white;
            padding: 5px 15px;
            border-radius: 4px;
            border: 2px solid #1B5E88;
            font-weight: bold;
            margin: 0 15px;
        }
        .remove-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .remove-btn:hover { background: #dc2626; }
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .summary {
            background: #E7FAFE;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 15px;
        }
        .summary-total {
            font-size: 22px;
            font-weight: bold;
            color: #1B5E88;
            border-top: 2px solid #1B5E88;
            padding-top: 12px;
            margin-top: 10px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1B5E88;
            font-weight: 600;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: #1B5E88;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.2s;
        }
        .submit-btn:hover { background: #0075B5; }
        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .clear-cart-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            float: right;
            margin-bottom: 15px;
        }
        .clear-cart-btn:hover { background: #dc2626; }
        .navbar { background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%); padding: 15px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-brand { color:#fff; font-size:28px; font-weight:bold; text-decoration:none; display: flex; align-items: center; gap: 10px; }
        .nav-logo { height: 40px; width: auto; }
        .nav-link { color: rgba(255,255,255,0.9); text-decoration:none; padding:10px 16px; border-radius:8px; transition: all .2s; }
        .nav-link:hover { color:#fff; background: rgba(255,255,255,0.15); }
        .nav-link.active { background: rgba(255,255,255,0.25); color:#fff; font-weight:600; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="nav-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="SeaLedger Logo" class="nav-logo">
                SeaLedger
            </a>
            <div class="d-flex align-items-center" style="gap:8px;">
                <a href="{{ route('rentals.index') }}" class="nav-link"><i class="fa-solid fa-toolbox"></i> Browse Gear</a>
                <a href="{{ route('rentals.create') }}" class="nav-link active"><i class="fa-solid fa-shopping-cart"></i> Cart</a>
                <a href="{{ route('rentals.myrentals') }}" class="nav-link"><i class="fa-solid fa-clipboard-list"></i> My Rentals</a>
                <a href="{{ route('dashboard') }}" class="nav-link"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1>🛒 Your Rental Cart</h1>
            <p>Review your items and submit your rental request</p>
        </div>

        <div class="form-container">
            @if(count($cartItems) > 0)
                <form action="{{ route('rentals.cart.clear') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="clear-cart-btn" onclick="return confirm('Clear all items from cart?')">
                        <i class="fa-solid fa-trash"></i> Clear Cart
                    </button>
                </form>
                <div style="clear: both;"></div>

                <h3 style="color: #1B5E88; margin-bottom: 15px;">Cart Items ({{ count($cartItems) }})</h3>
                
                @foreach($cartItems as $item)
                    <div class="cart-item">
                        <div class="item-info">
                            <div class="item-name">{{ $item['product']->name }}</div>
                            <div class="item-price">₱{{ number_format($item['product']->rental_price_per_day, 2) }}/day</div>
                        </div>
                        <div class="item-quantity">Qty: {{ $item['quantity'] }}</div>
                        <form action="{{ route('rentals.cart.remove') }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                            <button type="submit" class="remove-btn">
                                <i class="fa-solid fa-times"></i> Remove
                            </button>
                        </form>
                    </div>
                @endforeach

                <form action="{{ route('rentals.store') }}" method="POST" id="rentalForm">
                    @csrf

                    <div class="form-group">
                        <label for="rental_date">Rental Date *</label>
                        <input type="date" id="rental_date" name="rental_date" required value="{{ old('rental_date') }}" min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label for="return_date">Return Date *</label>
                        <input type="date" id="return_date" name="return_date" required value="{{ old('return_date') }}">
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes (Optional)</label>
                        <textarea id="notes" name="notes" placeholder="Any special requests or notes...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="summary">
                        <h3 style="margin-bottom: 15px; color: #1B5E88;">📋 Rental Summary</h3>
                        <div class="summary-row">
                            <span>Duration:</span>
                            <span id="duration">Please select dates</span>
                        </div>
                        <div class="summary-row">
                            <span>Total Items:</span>
                            <span>{{ count($cartItems) }} items</span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>Estimated Total:</span>
                            <span id="total">₱0.00</span>
                        </div>
                        <div class="summary-row" style="font-size: 14px; color: #666; margin-top: 10px;">
                            <span>Deposit Required (30%):</span>
                            <span id="deposit">₱0.00</span>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fa-solid fa-paper-plane"></i> Submit Rental Request
                    </button>
                </form>
            @else
                <div class="empty-cart">
                    <i class="fa-solid fa-shopping-cart" style="font-size: 80px; color: #ccc; margin-bottom: 20px;"></i>
                    <h2 style="color: #666; margin-bottom: 10px;">Your cart is empty</h2>
                    <p style="margin-bottom: 30px;">Add items from the gear rental catalog to get started.</p>
                    <a href="{{ route('rentals.index') }}" style="background: #1B5E88; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: bold;">
                        <i class="fa-solid fa-toolbox"></i> Browse Gear
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        const rentalDateInput = document.getElementById('rental_date');
        const returnDateInput = document.getElementById('return_date');
        const durationSpan = document.getElementById('duration');
        const totalSpan = document.getElementById('total');
        const depositSpan = document.getElementById('deposit');

        const items = @json($cartItems);

        function calculateSummary() {
            const rentalDate = new Date(rentalDateInput.value);
            const returnDate = new Date(returnDateInput.value);

            if (rentalDate && returnDate && returnDate > rentalDate) {
                const days = Math.ceil((returnDate - rentalDate) / (1000 * 60 * 60 * 24)) + 1;
                durationSpan.textContent = days + ' day' + (days > 1 ? 's' : '');

                let subtotal = 0;
                items.forEach(item => {
                    subtotal += item.product.rental_price_per_day * item.quantity * days;
                });

                const deposit = subtotal * 0.3;
                totalSpan.textContent = '₱' + subtotal.toFixed(2);
                depositSpan.textContent = '₱' + deposit.toFixed(2);
            } else {
                durationSpan.textContent = 'Please select dates';
                totalSpan.textContent = '₱0.00';
                depositSpan.textContent = '₱0.00';
            }
        }

        if (rentalDateInput && returnDateInput) {
            rentalDateInput.addEventListener('change', function() {
                returnDateInput.min = this.value;
                calculateSummary();
            });

            returnDateInput.addEventListener('change', calculateSummary);
        }
    </script>
    @include('partials.toast-notifications')
</body>
</html>
