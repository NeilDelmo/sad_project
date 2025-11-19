<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- bootstrap -->
    <link rel="stylesheet" href="bootstrap5/css/bootstrap.min.css" />

    <!-- icons -->

    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Koulen&display=swap">
    <title>SeaLedger Marketplace</title>

    <style>
        .koulen-regular {
            font-family: "Koulen", sans-serif;
            font-weight: 400;
            font-style: normal;
        }

        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            height: 100vh;
            margin: 0;
            background-color: #BFBFBF;
            font-family: Arial, sans-serif;
            /* ← default for everything except brand */
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .font-title {
            font-size: 110px;
            font-family: "Koulen", sans-serif;
            font-weight: 400;
            font-style: normal;
            line-height: 1;
            display: block;
        }

        .font-subtitle {
            font-size: 27px;
            font-family: "Koulen", sans-serif;
            font-weight: 400;
            font-style: normal;
        }

        .btn-text {
            font-size: 20px;
            font-family: "Koulen", sans-serif;
            font-weight: 400;
            font-style: normal;
        }

        .blue {
            color: #0075B5;
        }

        .light-blue {
            color: #E7FAFE;
        }

        .gray {
            color: #7A96AC;
        }

        .dark-blue {
            color: #1B5E88;
        }

        /* Modern Navbar — NOW USING DASHBOARD STYLE */
        .navbar {
            background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar .container-fluid {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
        }

        .nav-brand {
            flex-shrink: 0;
            color: white;
            font-size: 28px;
            font-weight: bold;
            font-family: "Koulen", sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .nav-links {
            display: flex !important;
            gap: 10px !important;
            margin-left: auto !important;
            flex-shrink: 0;
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
            font-family: Arial, sans-serif;
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Keep your original page styles */
        .center-div {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: calc(100vh - 68px);
            text-align: center;
            background: url('/images/fishing.jpg') center center/cover no-repeat;
            min-height: calc(100vh - 68px);
            width: 100%;
        }

        .btn-size {
            padding: 3px 20px;
            width: auto;
            max-width: 90%;
            white-space: nowrap;
        }

        .page-indicators {
            position: fixed;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .indicator {
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            background-color: #1B5E88;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .indicator.active {
            background-color: #1B5E88;
            opacity: 1;
            width: 40px;
        }
    </style>
</head>

<body style="background-color: #BFBFBF;">

    @include('partials.toast-notifications')
    @include('partials.message-notification')

    @if(session('success'))
    <div id="flash-toast" style="position: fixed; top: 80px; right: 20px; z-index: 9999;">
        <div style="background: #fff; border-left: 4px solid #16a34a; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); padding: 14px 16px; display:flex; gap:10px; align-items:center; min-width: 320px;">
            <div style="width:36px;height:36px;border-radius:50%;background:#16a34a;display:flex;align-items:center;justify-content:center;color:#fff;">
                <i class="fa-solid fa-check"></i>
            </div>
            <div style="flex:1; min-width:0;">
                <div style="font-weight:700; color:#166534; margin-bottom:2px;">Listing Posted</div>
                <div style="color:#444; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ session('success') }}</div>
            </div>
            <button onclick="(function(btn){ var t=btn.closest('[id=flash-toast]'); if(t){ t.remove(); } })(this)" style="background:none;border:none;color:#888;font-size:18px;cursor:pointer;">×</button>
        </div>
    </div>
    <script>
        setTimeout(function() {
            var t = document.getElementById('flash-toast');
            if (t) {
                t.remove();
            }
        }, 4000);
    </script>
    @endif

    <!-- Role-Based Navbar -->
    <nav class="navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="nav-brand" href="{{ route('marketplace.index') }}" style="text-decoration: none;">🐟 Marketplace &amp; Forum</a>
            <div class="nav-links">
                @if(Auth::check())
                @if(Auth::user()->user_type === 'vendor')
                <!-- Vendor Navigation -->
                <a href="{{ route('vendor.dashboard') }}" class="nav-link">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a href="{{ route('vendor.products.index') }}" class="nav-link">
                    <i class="fa-solid fa-fish"></i> Browse
                </a>
                <a href="{{ route('vendor.inventory.index') }}" class="nav-link">
                    <i class="fa-solid fa-box"></i> Inventory
                </a>
                <a href="{{ route('vendor.offers.index') }}" class="nav-link">
                    <i class="fa-solid fa-handshake"></i> Offers
                </a>
                <a href="{{ route('marketplace.index') }}" class="nav-link active">
                    <i class="fa-solid fa-store"></i> Marketplace & Forum
                </a>
                <a href="{{ route('marketplace.orders.index') }}" class="nav-link">
                    <i class="fa-solid fa-shopping-cart"></i> My Orders
                </a>
                @elseif(Auth::user()->user_type === 'buyer')
                <!-- Buyer Navigation -->
                <a href="{{ route('marketplace.shop') }}" class="nav-link">
                    <i class="fa-solid fa-fire"></i> Latest
                </a>
                <a href="{{ route('marketplace.index') }}" class="nav-link active">
                    <i class="fa-solid fa-store"></i> Marketplace & Forum
                </a>
                <a href="{{ route('marketplace.orders.index') }}" class="nav-link">
                    <i class="fa-solid fa-receipt"></i> My Orders
                </a>
                <a href="{{ route('forums.index') }}" class="nav-link">
                    <i class="fa-solid fa-comments"></i> Forum
                </a>
                @else
                <!-- Fisherman Navigation -->
                <a href="{{ route('fisherman.dashboard') }}" class="nav-link">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a href="{{ route('fisherman.products.index') }}" class="nav-link">
                    <i class="fa-solid fa-box"></i> My Products
                </a>
                <a href="{{ route('orders.index') }}" class="nav-link">
                    <i class="fa-solid fa-receipt"></i> Orders
                </a>
                <a href="{{ route('fisherman.offers.index') }}" class="nav-link">
                    <i class="fa-solid fa-handshake"></i> Offers
                </a>
                {{-- Safety Map removed from public/other roles; only fishermen can access via dashboard if needed --}}
                <a href="{{ route('marketplace.index') }}" class="nav-link active">
                    <i class="fa-solid fa-store"></i> Marketplace & Forum
                </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-link" style="background: none; border: none; cursor: pointer;">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}" class="nav-link">
                    <i class="fa-solid fa-right-to-bracket"></i> Login
                </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- REST OF YOUR PAGE: 100% UNCHANGED -->
    <div class="d-flex flex-column align-items-center center-div light-blue">
        <div>
            <span class="font-subtitle">{{ Auth::check() ? Auth::user()->name : 'Guest' }}, ready for today's catch?</span>
            <span class="font-title">SeaLedger</span>
            <div class="d-flex gap-3 justify-content-center mt-2">
                <a href="{{ route('marketplace.shop') }}" class="btn btn-text btn-size rounded-pill light-blue" style="background-color: #0075B5;">Marketplace</a>
                <a href="{{ route('forums.index') }}" class="btn btn-text btn-size rounded-pill dark-blue" style="background-color: #E7FAFE; border: 2px solid #0075B5;">Community Forum</a>
            </div>
        </div>

        <div class="page-indicators gap-1">
            <span class="indicator active"></span>
            <span class="indicator"></span>
        </div>
    </div>

    <script>
        document.addEventListener('click', () => {
            const indicators = document.querySelectorAll('.indicator');
            indicators.forEach(ind => ind.classList.toggle('active'));
        });
    </script>
</body>

</html>