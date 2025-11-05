<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap5/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>FishOrg Market</title>
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background: #1B5E88;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .nav-tabs {
            display: flex;
            gap: 30px;
        }

        .nav-tab {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .nav-tab.active {
            background: #0075B5;
        }

        .nav-tab:hover {
            background: #0075B5;
        }

        .products-grid {
            display: flex;
            gap: 20px;
            padding: 20px;
            overflow-x: auto;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            min-width: 250px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .product-image {
            width: 100%;
            height: 160px;
            background: #E7FAFE;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .product-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #1B5E88;
        }

        .product-price {
            font-size: 18px;
            font-weight: bold;
            color: #B12704;
            margin-bottom: 8px;
        }

        .product-info {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .contact-info {
            font-size: 13px;
            color: #0075B5;
            background: #E7FAFE;
            padding: 6px 10px;
            border-radius: 4px;
            margin-bottom: 8px;
            cursor: pointer;
            border: 1px solid #0075B5;
            transition: all 0.3s;
        }

        .contact-info:hover {
            background: #0075B5;
            color: white;
        }

        .contact-info.copied {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }

        .section-title {
            font-size: 22px;
            color: #1B5E88;
            margin: 20px 0 10px 20px;
            font-weight: bold;
            border-bottom: 2px solid #1B5E88;
            padding-bottom: 5px;
        }

        .gear-contact {
            text-align: center;
            color: #666;
            font-style: italic;
            margin: 10px 20px;
            background: #E7FAFE;
            padding: 10px;
            border-radius: 6px;
        }

        .contact-btn {
            width: 100%;
            padding: 8px 12px;
            background: #1B5E88;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            margin-top: 8px;
            transition: background 0.2s;
        }

        .contact-btn:hover {
            background: #0075B5;
            transform: translateY(-1px);
        }

        .time-posted {
            font-size: 12px;
            color: #999;
            font-style: italic;
            margin-top: 5px;
        }

        .freshness-badge {
            background: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            display: inline-block;
            margin-top: 5px;
        }

        .stock-info {
            color: #28a745;
            font-size: 12px;
            margin-top: 5px;
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
            width: 100%;
            color: #666;
            background: white;
            border-radius: 8px;
            margin: 20px;
        }

        .empty-state i {
            color: #ddd;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            font-size: 18px;
            margin: 10px 0 5px 0;
            color: #1B5E88;
        }

        .empty-state p {
            font-size: 14px;
            color: #999;
            margin: 0;
        }
    </style>
</head>

<body>

    <!-- Simple Navbar -->
    <nav class="navbar">
        <div class="nav-brand">FishOrg</div>
        <div class="nav-tabs">
            <a href="#" class="nav-tab active">Marketplace</a>
            <a href="#" class="nav-tab">Organization</a>
        </div>
    </nav>

    <!-- Fresh Fish Section -->
    <div class="section-title">Fresh Fish</div>
    <div class="products-grid">
        @forelse($fishProducts as $product)
        <div class="product-card">
            <div class="product-image">
                <i class="fa-solid fa-fish fa-2x" style="color: #0075B5;"></i>
            </div>
            <div class="product-title">{{ $product->name }}</div>
            <div class="product-price">₱{{ number_format($product->unit_price, 2) }}/kg</div>
            <div class="product-info">
                {{ $product->description ?? 'Fresh catch' }}
            </div>
            @if($product->freshness_metric)
            <div class="product-info" style="color: #28a745; font-weight: bold;">
                🌟 {{ $product->freshness_metric }}
            </div>
            @endif
            <div class="product-info" style="font-size: 12px; color: #999;">
                Posted {{ $product->created_at->diffForHumans() }}
            </div>
            @if($product->supplier && $product->supplier->phone)
            <div class="contact-info" onclick="copyContact(this)" data-contact="{{ $product->supplier->phone }}">
                📞 {{ $product->supplier->phone }} (Click to copy)
            </div>
            @endif
            @auth
                <button class="contact-btn" onclick="window.location.href='{{ route('marketplace.message') }}'">Message Seller</button>
            @else
                <button class="contact-btn" onclick="showLoginPrompt()">Message Seller</button>
            @endauth
        </div>
        @empty
        <div style="padding: 40px; text-align: center; width: 100%; color: #666;">
            <i class="fa-solid fa-fish fa-3x" style="color: #ddd; margin-bottom: 15px;"></i>
            <p style="font-size: 18px; margin: 0;">No fish products available at the moment</p>
            <p style="font-size: 14px; color: #999; margin-top: 5px;">Check back soon for fresh catches!</p>
        </div>
        @endforelse
    </div>

    <!-- Fishing Gear Section -->
    <div class="section-title">Fishing Gear & Equipment</div>
    <div class="gear-contact" onclick="copyOrgContact(this)" style="cursor: pointer;">
        Contact Equipment Manager: 📞 0916-777-8888 (Click to copy)
    </div>
    <div class="products-grid">
        @forelse($gearProducts as $product)
        <div class="product-card">
            <div class="product-image">
                <i class="fa-solid fa-toolbox fa-2x" style="color: #0075B5;"></i>
            </div>
            <div class="product-title">{{ $product->name }}</div>
            <div class="product-price">₱{{ number_format($product->unit_price, 2) }}</div>
            <div class="product-info">
                {{ $product->description ?? 'Quality equipment' }}
            </div>
            @if($product->available_quantity > 0)
            <div class="product-info" style="color: #28a745; font-size: 12px;">
                ✓ {{ $product->available_quantity }} units available
            </div>
            @endif
        </div>
        @empty
        <div style="padding: 40px; text-align: center; width: 100%; color: #666;">
            <i class="fa-solid fa-toolbox fa-3x" style="color: #ddd; margin-bottom: 15px;"></i>
            <p style="font-size: 18px; margin: 0;">No fishing gear available at the moment</p>
            <p style="font-size: 14px; color: #999; margin-top: 5px;">Check back soon for equipment!</p>
        </div>
        @endforelse
    </div>

    <!-- Login Prompt Modal -->
    <div id="loginModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 12px; max-width: 400px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
            <h2 style="color: #1B5E88; margin-bottom: 15px; font-size: 24px;">Login Required</h2>
            <p style="color: #666; margin-bottom: 25px;">Please login or create an account to message sellers.</p>
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button onclick="window.location.href='{{ route('login') }}'" style="background: #0075B5; color: white; padding: 12px 30px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 16px;">
                    Login
                </button>
                <button onclick="window.location.href='{{ route('register') }}'" style="background: #1B5E88; color: white; padding: 12px 30px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 16px;">
                    Sign Up
                </button>
            </div>
            <button onclick="closeLoginPrompt()" style="background: transparent; color: #999; padding: 10px; border: none; cursor: pointer; margin-top: 15px; text-decoration: underline;">
                Cancel
            </button>
        </div>
    </div>

    <script>
        function copyContact(element) {
            const contact = element.textContent.match(/\d{4}-\d{3}-\d{4}/)[0];
            navigator.clipboard.writeText(contact).then(() => {
                const originalText = element.innerHTML;
                element.innerHTML = '✅ Copied!';
                element.classList.add('copied');

                setTimeout(() => {
                    element.innerHTML = originalText;
                    element.classList.remove('copied');
                }, 1500);
            });
        }

        // Organization contact copy function
        function copyOrgContact(element) {
            const contact = element.textContent.match(/\d{4}-\d{3}-\d{4}/)[0];
            navigator.clipboard.writeText(contact).then(() => {
                const originalText = element.innerHTML;
                element.innerHTML = '✅ Contact copied to clipboard!';
                element.style.background = '#d4edda';

                setTimeout(() => {
                    element.innerHTML = originalText;
                    element.style.background = '#E7FAFE';
                }, 1500);
            });
        }

        // Login prompt functions
        function showLoginPrompt() {
            document.getElementById('loginModal').style.display = 'flex';
        }

        function closeLoginPrompt() {
            document.getElementById('loginModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLoginPrompt();
            }
        });
    </script>

</body>

</html>