<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>SeaLedger Marketplace</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
            padding: 18px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-brand {
            color: white;
            font-size: 32px;
            font-weight: bold;
            font-family: "Koulen", sans-serif;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            letter-spacing: 1px;
        }

        .nav-links { display: flex; gap: 8px; align-items: center; }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
            white-space: nowrap;
            background: transparent;
        }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.15); transform: translateY(-1px); }
        .nav-link.active { background: rgba(255,255,255,0.25); color: #fff; font-weight: 600; }

        .container-custom { max-width: 1400px; margin: 0 auto; padding: 40px 30px; }
        .page-header { margin-bottom: 35px; }
        .page-title { font-family: "Koulen", sans-serif; font-size: 42px; color: #1B5E88; margin-bottom: 8px; letter-spacing: .5px; }
        .page-subtitle { color: #6c757d; font-size: 16px; }

        .filter-card { background:#fff; padding: 20px 24px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border:1px solid rgba(0,0,0,0.05); margin-bottom: 24px; }
        .filter-card .form-label { font-weight:600; color:#2c3e50; margin-bottom:8px; font-size:14px; }
        .filter-card .form-control { padding:12px 16px; border:2px solid #e9ecef; border-radius:8px; }
        .filter-card .form-control:focus { border-color:#0075B5; box-shadow: 0 0 0 3px rgba(0,117,181,0.1); }

        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 28px; margin-bottom: 40px; }
        .product-card { background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); transition: all .3s ease; border: 2px solid transparent; height: fit-content; }
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(0,0,0,0.12); border-color: #0075B5; }

        .product-image { width: 100%; height: 220px; object-fit: cover; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); background: #f8f9fa; }
        .product-image-placeholder { width: 100%; height: 220px; background: linear-gradient(135deg,#e9ecef 0%,#f8f9fa 100%); border-radius: 12px; margin-bottom: 20px; display:flex; align-items:center; justify-content:center; color:#adb5bd; font-size:48px; }

        .product-header { margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #f0f0f0; display:flex; justify-content: space-between; align-items:center; }
        .product-name { font-size: 22px; font-weight: 700; color:#1B5E88; margin-bottom: 10px; line-height:1.3; display:flex; align-items:center; gap:10px; }
        .product-category { display:inline-block; background: linear-gradient(135deg, #E7FAFE 0%, #d4f4fa 100%); color:#0075B5; padding:6px 14px; border-radius:20px; font-size:13px; font-weight:600; letter-spacing:.3px; }

        .product-details { background:#f8f9fa; padding:18px; border-radius:12px; margin-bottom:20px; }
        .product-detail-row { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e9ecef; }
        .product-detail-row:last-child { border-bottom:none; }
        .detail-label { color:#6c757d; font-weight:500; font-size:14px; }
        .detail-value { color:#2c3e50; font-weight:700; font-size:14px; }

        .product-price { font-size:32px; font-weight:800; color:#1B5E88; margin:24px 0; padding:20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius:12px; text-align:center; border:2px dashed #dee2e6; }
        .price-label { font-size:12px; color:#6c757d; font-weight:600; display:block; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px; }

        .product-details { background:#f8f9fa; padding:14px; border-radius:12px; margin-bottom:16px; }
        .product-detail-row { display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #e9ecef; }
        .product-detail-row:last-child { border-bottom:none; }
        .detail-label { color:#6c757d; font-weight:500; font-size:13px; }
        .detail-value { color:#2c3e50; font-weight:700; font-size:13px; }

        .contact-btn { background:#0075B5; color:#fff; border:1px solid #0075B5; padding:10px 16px; border-radius:8px; font-weight:600; cursor:pointer; transition: all .2s ease; }
        .contact-btn:hover { background:#1B5E88; border-color:#1B5E88; }
        .buy-btn { background:#16a34a; color:#fff; border:1px solid #16a34a; padding:10px 16px; border-radius:8px; font-weight:700; transition: all .2s ease; }
        .buy-btn:hover { background:#15803d; border-color:#15803d; }
        .qty-input { text-align:center; max-width:100px; }
        .qty-group { max-width: 220px; }

        .card-actions { display:flex; gap:10px; align-items:center; flex-wrap: wrap; }

        .contact-row { display:flex; gap:8px; align-items:center; margin: 6px 0 10px 0; }
        .contact-phone { font-size: 14px; color:#0075B5; background:#E7FAFE; padding:6px 10px; border-radius:6px; border:1px solid #0075B5; }
        .copy-btn { background:#ffffff; color:#0075B5; border:1px solid #0075B5; padding:6px 10px; border-radius:6px; font-weight:600; cursor:pointer; transition: all .2s ease; display:flex; align-items:center; gap:6px; }
        .copy-btn:hover { background:#0075B5; color:#fff; }
        .copy-btn.copied { background:#16a34a; color:#fff; border-color:#16a34a; }

        .empty-state { text-align:center; padding: 100px 40px; background:#fff; border-radius:16px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .empty-state i { font-size:80px; color:#dee2e6; margin-bottom:24px; display:block; }
        .empty-state h3 { color:#1B5E88; margin-bottom:12px; font-size:28px; font-weight:700; }
        .empty-state p { color:#6c757d; font-size:16px; }

        @media (max-width: 768px) {
            .nav-links { flex-direction: column; gap: 4px; }
            .product-grid { grid-template-columns: 1fr; gap: 20px; }
            .container-custom { padding: 30px 20px; }
            .page-title { font-size: 32px; }
        }
    </style>
</head>

<body>
    @include('partials.toast-notifications')
    @include('partials.message-notification')

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="nav-brand" href="{{ route('marketplace.index') }}" style="text-decoration: none;">🐟 SeaLedger</a>
            <div class="nav-links">
                <a href="{{ route('marketplace.shop') }}" class="nav-link {{ (!isset($filter) || $filter == 'all') ? 'active' : '' }}">
                    <i class="fa-solid fa-fire"></i> Latest
                </a>
                @auth
                    @if(auth()->user()->user_type === 'buyer')
                        <a href="{{ route('marketplace.orders.index') }}" class="nav-link">
                            <i class="fa-solid fa-receipt"></i> My Orders
                        </a>
                    @endif
                @endauth
                @if(Auth::check())
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
      setTimeout(function(){ var t=document.getElementById('flash-toast'); if(t){ t.remove(); } }, 4000);
    </script>
    @endif

    <div class="container-custom">
        <div class="page-header">
            <div class="page-title">Browse Marketplace</div>
            <div class="page-subtitle">Discover fresh catches and shop directly</div>
        </div>

        <!-- Search Bar -->
        <form class="filter-card" method="get" action="{{ route('marketplace.shop') }}">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-8 col-lg-6">
                    <label class="form-label">Search Products</label>
                    <input type="text" name="q" id="searchInput" class="form-control" placeholder="Product name or seller" value="{{ $q ?? '' }}">
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <button class="btn btn-primary w-100" style="background: linear-gradient(135deg, #0075B5 0%, #1B5E88 100%); border:none; padding:12px 16px; border-radius:8px; font-weight:700;">
                        <i class="fa-solid fa-magnifying-glass"></i> Search
                    </button>
                </div>
            </div>
        </form>

        @if(isset($recommendations))
        <!-- Recommended For You -->
        @php
            $priceOf = function($l){ return $l->final_price ?? $l->dynamic_price ?? $l->asking_price ?? $l->base_price; };
        @endphp
        @if(($recommendations['cheapest'] ?? collect())->isNotEmpty())
        <div class="page-subtitle" style="font-weight:600; margin: 20px 0 10px;">Cheapest Picks</div>
        <div class="product-grid">
            @foreach($recommendations['cheapest'] as $listing)
            @php $product = $listing->product; $uom = $product->unit_of_measure ?? 'kg'; @endphp
            <div class="product-card">
                <div class="product-name">{{ $product->name }}</div>
                <div class="product-price">
                    <span class="price-label">From</span>
                    ₱{{ number_format($priceOf($listing), 2) }}/{{ $uom }}
                </div>
                <div class="product-details">
                    <div class="product-detail-row">
                        <span class="detail-label">Freshness</span>
                        <span class="detail-value">{{ $listing->freshness_score ? ($listing->freshness_score.'/100') : 'N/A' }}</span>
                    </div>
                    <div class="product-detail-row">
                        <span class="detail-label">Listed</span>
                        <span class="detail-value">{{ optional($listing->listing_date)->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if(($recommendations['freshest'] ?? collect())->isNotEmpty())
        <div class="page-subtitle" style="font-weight:600; margin: 20px 0 10px;">Freshest Today</div>
        <div class="product-grid">
            @foreach($recommendations['freshest'] as $listing)
            @php $product = $listing->product; $uom = $product->unit_of_measure ?? 'kg'; @endphp
            <div class="product-card">
                <div class="product-name">{{ $product->name }}</div>
                <div class="product-price">
                    <span class="price-label">Price</span>
                    ₱{{ number_format($priceOf($listing), 2) }}/{{ $uom }}
                </div>
                <div class="product-details">
                    <div class="product-detail-row">
                        <span class="detail-label">Freshness</span>
                        <span class="detail-value">{{ $listing->freshness_score ? ($listing->freshness_score.'/100') : 'N/A' }}</span>
                    </div>
                    <div class="product-detail-row">
                        <span class="detail-label">Demand</span>
                        <span class="detail-value">{{ $listing->demand_factor ? ($listing->demand_factor.'x') : '—' }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        @endif

        <!-- Fresh Fish Section -->
        @if(!isset($filter) || $filter == 'all' || $filter == 'fish')
        <div class="page-subtitle" id="fish-section" style="font-weight:600; margin-bottom: 12px;">Fresh Fish</div>
        <div class="product-grid">
        @forelse($fishProducts as $listing)
        @php $product = $listing->product ?? $listing @endphp
        <div class="product-card">
            <div class="product-image">
                @if($product->image_path)
                    <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;">
                @else
                    <i class="fa-solid fa-fish fa-2x" style="color: #0075B5;"></i>
                @endif
            </div>
            @php
                $uom = $product->unit_of_measure ?? 'kg';
                $stock = isset($stocks) ? ($stocks[$listing->id] ?? null) : null;
            @endphp
            <div class="product-name">{{ $product->name }}</div>
            <div class="product-price">
                <span class="price-label">Seller's Price</span>
                ₱{{ number_format($listing->final_price ?? $product->unit_price, 2) }}/{{ $uom }}
            </div>
            <div class="product-details">
                <div class="product-detail-row">
                    <span class="detail-label">Available</span>
                    <span class="detail-value">
                        @if($stock === null)
                            N/A
                        @elseif($stock === 0)
                            Out of stock
                        @else
                            {{ $stock }} {{ $uom }}
                        @endif
                    </span>
                </div>
                <div class="product-detail-row">
                    <span class="detail-label">Listed</span>
                    <span class="detail-value">{{ ($listing->listing_date ?? $product->created_at)->diffForHumans() }}</span>
                </div>
            </div>
            @php $seller = $listing->seller ?? $product->supplier @endphp
            @if($seller && $seller->phone)
            <div class="contact-row">
                <span class="contact-phone">📞 {{ $seller->phone }}</span>
                <button type="button" class="copy-btn" data-contact="{{ $seller->phone }}" onclick="copyContact(this)">
                    <i class="fa-regular fa-clipboard"></i> Copy
                </button>
            </div>
            @endif
            @auth
                @php $isOwner = ($listing->seller_id ?? $product->supplier_id) === auth()->id(); $userType = auth()->user()->user_type ?? null; @endphp
                @if($isOwner)
                    <button class="contact-btn" type="button" disabled style="opacity: 0.6; cursor: not-allowed;">This is your listing</button>
                @else
                    <div class="card-actions">
                        @php $hasListingSeller = isset($listing->seller_id) && $listing->seller_id; @endphp
                        <form action="{{ route('marketplace.buy', ['listing' => $listing->id]) }}" method="POST" style="margin: 0; display:flex; gap:8px; align-items:center;">
                            @csrf
                            <div class="input-group qty-group">
                                <button class="btn btn-outline-secondary" type="button" onclick="decQty(this)">-</button>
                                <input type="number" name="quantity" min="1" @if(isset($stock) && $stock !== null) max="{{ $stock }}" @endif value="1" class="form-control qty-input" @if(isset($stock) && $stock === 0) disabled @endif>
                                <span class="input-group-text">{{ $uom }}</span>
                                <button class="btn btn-outline-secondary" type="button" onclick="incQty(this)">+</button>
                            </div>
                            @if($userType === 'buyer')
                                <button type="submit" class="buy-btn" @if(isset($stock) && $stock === 0) disabled style="opacity:.6; cursor:not-allowed;" @endif><i class="fa-solid fa-cart-shopping"></i> Buy</button>
                            @else
                                <button type="button" class="buy-btn" disabled title="Only buyers can purchase" style="opacity:.6; cursor:not-allowed;"><i class="fa-solid fa-cart-shopping"></i> Buy</button>
                            @endif
                        </form>
                    </div>
                @endif
            @else
                <div style="display:flex; gap:10px;">
                    <button class="buy-btn" onclick="showLoginPrompt()"><i class="fa-solid fa-cart-shopping"></i> Buy</button>
                </div>
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
        @endif
    </div>

    <!-- Login Prompt Modal -->
    <div id="loginModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 12px; max-width: 400px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
            <h2 style="color: #1B5E88; margin-bottom: 15px; font-size: 24px;">Login Required</h2>
            <p style="color: #666; margin-bottom: 25px;">Please login or create an account to continue.</p>
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
        function incQty(btn){
            const input = btn.parentElement.querySelector('input[name="quantity"]');
            const val = parseInt(input.value || '1', 10);
            const max = parseInt(input.getAttribute('max') || '0', 10);
            let next = isNaN(val) ? 1 : val + 1;
            if (!isNaN(max) && max > 0) next = Math.min(next, max);
            input.value = next;
        }
        function decQty(btn){
            const input = btn.parentElement.querySelector('input[name="quantity"]');
            const val = parseInt(input.value || '1', 10);
            const next = (isNaN(val) ? 1 : Math.max(1, val - 1));
            input.value = next;
        }
        // messaging removed from marketplace

        function copyContact(btn) {
            const contact = btn.getAttribute('data-contact');
            if (!contact) return;
            navigator.clipboard.writeText(contact).then(() => {
                const original = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied';
                btn.classList.add('copied');
                setTimeout(() => { btn.innerHTML = original; btn.classList.remove('copied'); }, 1500);
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>