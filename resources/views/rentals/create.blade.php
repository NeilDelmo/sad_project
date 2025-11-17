<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Rental Request - SeaLedger</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
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

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1B5E88;
            font-weight: 600;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .item-row {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #1B5E88;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .item-name {
            font-weight: bold;
            color: #1B5E88;
        }

        .remove-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .add-item-btn {
            background: #10b981;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .summary {
            background: #E7FAFE;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .summary-total {
            font-size: 20px;
            font-weight: bold;
            color: #1B5E88;
            border-top: 2px solid #1B5E88;
            padding-top: 10px;
            margin-top: 10px;
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

        .submit-btn:hover {
            background: #0075B5;
        }

        /* Navbar */
        .navbar { background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%); padding: 15px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-brand { color:#fff; font-size:28px; font-weight:bold; text-decoration:none; }
        .nav-link { color: rgba(255,255,255,0.9); text-decoration:none; padding:10px 16px; border-radius:8px; transition: all .2s; }
        .nav-link:hover { color:#fff; background: rgba(255,255,255,0.15); }
        .nav-link.active { background: rgba(255,255,255,0.25); color:#fff; font-weight:600; }

        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .gear-select-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 10px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="nav-brand" href="{{ route('dashboard') }}">🐟 SeaLedger</a>
            <div class="d-flex align-items-center" style="gap:8px;">
                <a href="{{ route('rentals.index') }}" class="nav-link"><i class="fa-solid fa-toolbox"></i> Gear Rentals</a>
                <a href="{{ route('rentals.myrentals') }}" class="nav-link"><i class="fa-solid fa-clipboard-list"></i> My Rentals</a>
                <a href="{{ route('dashboard') }}" class="nav-link"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container">

        <div class="header">
            <h1>🛠️ Create Rental Request</h1>
            <p>Fill out the form below to request gear rental</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Error!</strong>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-container">
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
                    <label>Gear Items *</label>
                    <div id="items-container">
                        @if($product)
                            <div class="item-row" data-index="0">
                                <div class="item-header">
                                    <span class="item-name">{{ $product->name }} - ₱{{ number_format($product->rental_price_per_day, 2) }}/day</span>
                                </div>
                                <input type="hidden" name="items[0][product_id]" value="{{ $product->id }}">
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="number" name="items[0][quantity]" min="1" max="{{ $product->rental_stock }}" value="1" required>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Notes (Optional)</label>
                    <textarea id="notes" name="notes" placeholder="Any special requests or notes...">{{ old('notes') }}</textarea>
                </div>

                <div class="summary">
                    <h3 style="margin-bottom: 10px; color: #1B5E88;">Rental Summary</h3>
                    <div class="summary-row">
                        <span>Duration:</span>
                        <span id="duration">-</span>
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

                <button type="submit" class="submit-btn">Submit Rental Request</button>
            </form>
        </div>
    </div>

    <script>
        const rentalDateInput = document.getElementById('rental_date');
        const returnDateInput = document.getElementById('return_date');
        const durationSpan = document.getElementById('duration');
        const totalSpan = document.getElementById('total');
        const depositSpan = document.getElementById('deposit');

        function calculateSummary() {
            const rentalDate = new Date(rentalDateInput.value);
            const returnDate = new Date(returnDateInput.value);

            if (rentalDate && returnDate && returnDate > rentalDate) {
                const days = Math.ceil((returnDate - rentalDate) / (1000 * 60 * 60 * 24)) + 1;
                durationSpan.textContent = days + ' day' + (days > 1 ? 's' : '');

                // Calculate total based on items (simplified for now)
                const total = days * {{ $product ? $product->rental_price_per_day : 0 }};
                const deposit = total * 0.3;

                totalSpan.textContent = '₱' + total.toFixed(2);
                depositSpan.textContent = '₱' + deposit.toFixed(2);
            } else {
                durationSpan.textContent = '-';
                totalSpan.textContent = '₱0.00';
                depositSpan.textContent = '₱0.00';
            }
        }

        rentalDateInput.addEventListener('change', function() {
            // Set minimum return date to rental date
            returnDateInput.min = this.value;
            calculateSummary();
        });

        returnDateInput.addEventListener('change', calculateSummary);

        // Set minimum date for rental to today
        const today = new Date().toISOString().split('T')[0];
        rentalDateInput.min = today;
    </script>
</body>
</html>
