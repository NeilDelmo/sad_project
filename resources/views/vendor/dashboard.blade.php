<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('bootstrap5/css/bootstrap.min.css') }}" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>SeaLedger - Vendor Dashboard</title>
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

        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
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

        .section-title {
            font-family: "Koulen", sans-serif;
            font-size: 28px;
            color: #1B5E88;
            margin-bottom: 20px;
            border-bottom: 3px solid #0075B5;
            padding-bottom: 10px;
        }

        .offer-list {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .offer-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .offer-item:last-child {
            border-bottom: none;
        }

        .offer-info {
            flex-grow: 1;
        }

        .offer-name {
            font-size: 18px;
            font-weight: bold;
            color: #1B5E88;
            margin-bottom: 5px;
        }

        .offer-details {
            font-size: 14px;
            color: #666;
        }

        .offer-price {
            font-size: 20px;
            font-weight: bold;
            color: #16a34a;
            margin-right: 20px;
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
    @include('vendor.partials.nav')

    <div class="dashboard-container">
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-title">Vendor Dashboard</div>
            <p style="font-size: 18px; color: #666; margin-bottom: 0;">Welcome back, {{ Auth::user()->username ?? Auth::user()->email }}!</p>
            <p style="font-size: 16px; color: #999;">Browse fisherman products and manage your inventory</p>

            <div class="action-buttons">
                <a href="{{ route('vendor.products.index') }}" class="btn-primary-custom">
                    <i class="fa-solid fa-fish"></i>
                    Browse Products
                </a>
                <a href="{{ route('vendor.inventory.index') }}" class="btn-secondary-custom">
                    <i class="fa-solid fa-box"></i>
                    My Inventory
                </a>
                <a href="{{ route('orders.index') }}" class="btn-secondary-custom">
                    <i class="fa-solid fa-clipboard-list"></i>
                    Orders
                </a>
                <a href="{{ route('vendor.onboarding') }}" class="btn-secondary-custom">
                    <i class="fa-solid fa-gear"></i>
                    Preferences
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-peso-sign"></i>
                </div>
                <div class="stat-number">₱{{ number_format($totalSpending ?? 0, 2) }}</div>
                <div class="stat-label">Total Spending (Accepted Offers)</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <div class="stat-number">{{ $acceptedOffersCount ?? 0 }}</div>
                <div class="stat-label">Accepted Offers</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="stat-number" id="unread-message-count">{{ $unreadCount ?? 0 }}</div>
                <div class="stat-label">Unread Messages</div>
            </div>
        </div>

        <!-- Recent Accepted Offers -->
        @if(isset($recentAcceptedOffers) && $recentAcceptedOffers->count() > 0)
        <div class="section-title">Recent Accepted Offers</div>
        <div class="offer-list">
            @foreach($recentAcceptedOffers as $offer)
            <div class="offer-item">
                <div class="offer-info">
                    <div class="offer-name">
                        <i class="fa-solid fa-check-circle" style="color: #16a34a; margin-right: 8px;"></i>
                        {{ $offer->product->name ?? 'Product' }}
                    </div>
                    <div class="offer-details">
                        Fisherman: {{ $offer->fisherman->username ?? $offer->fisherman->email }}
                        • {{ $offer->quantity }} kg @ ₱{{ number_format($offer->offered_price, 2) }}/kg
                        <span style="color: #999; margin-left: 10px;">Accepted {{ $offer->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="offer-price">₱{{ number_format($offer->offered_price * $offer->quantity, 2) }}</div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Counter Offers Awaiting Response -->
        @if(isset($recentCounterOffers) && $recentCounterOffers->count() > 0)
        <div class="section-title" style="margin-top:30px;">Counter Offers Awaiting Your Response</div>
        <div class="offer-list">
            @foreach($recentCounterOffers as $offer)
            <div class="offer-item" style="background:#fff8e6;">
                <div class="offer-info">
                    <div class="offer-name">
                        <i class="fa-solid fa-hourglass-half" style="color: #d97706; margin-right: 8px;"></i>
                        {{ $offer->product->name ?? 'Product' }}
                    </div>
                    <div class="offer-details">
                        Fisherman: {{ $offer->fisherman->username ?? $offer->fisherman->email }} • Counter: ₱{{ number_format($offer->fisherman_counter_price, 2) }}
                        <span style="color:#999; margin-left:10px;">Sent {{ $offer->responded_at?->diffForHumans() }}</span>
                        @if($offer->expires_at)
                        <span style="color:#b91c1c; margin-left:10px;">Expires {{ $offer->expires_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
                <div style="display:flex; gap:8px; align-items:center;">
                    <form method="POST" action="{{ route('vendor.offers.accept-counter', $offer) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm" style="background:#16a34a; color:#fff; border:none; padding:8px 12px; border-radius:6px;">Accept Counter</button>
                    </form>
                    <form method="POST" action="{{ route('vendor.offers.decline-counter', $offer) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm" style="background:#dc2626; color:#fff; border:none; padding:8px 12px; border-radius:6px;">Reject</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <script>
        // Refresh unread message count when window gains focus
        let lastUnreadCount = {{ $unreadCount ?? 0 }};
        let hasNotified = false; // Track if we already played sound for current unread count
        let isFirstPoll = true; // Track if this is the first poll after page load
        
        // Sound notification for new messages
        const notifAudio = new Audio('/audio/notify.mp3');
        
        function refreshUnreadCount() {
            fetch('/api/messages/unread-count')
                .then(response => response.json())
                .then(data => {
                    console.log('Unread count check:', data.unread_count, 'Last:', lastUnreadCount, 'HasNotified:', hasNotified, 'FirstPoll:', isFirstPoll);
                    const unreadBadge = document.getElementById('unread-message-count');
                    if (unreadBadge) {
                        // On first poll: play sound if there are any unread messages
                        // On subsequent polls: play sound only if count increased
                        const shouldPlaySound = (isFirstPoll && data.unread_count > 0 && !hasNotified) || 
                                                (!isFirstPoll && data.unread_count > lastUnreadCount && !hasNotified);
                        
                        if (shouldPlaySound) {
                            console.log('Playing notification sound!');
                            notifAudio.currentTime = 0;
                            notifAudio.play().catch(err => console.error('Sound play failed:', err));
                            hasNotified = true;
                        }
                        
                        // Reset notification flag when count goes back to 0 (user read messages)
                        if (data.unread_count === 0) {
                            hasNotified = false;
                        }
                        
                        if (data.unread_count !== lastUnreadCount) {
                            unreadBadge.textContent = data.unread_count;
                            lastUnreadCount = data.unread_count;
                        }
                        
                        if (isFirstPoll) {
                            isFirstPoll = false;
                        }
                    }
                })
                .catch(err => console.error('Failed to refresh unread count:', err));
        }

        // Refresh on window focus (when returning from conversation page)
        window.addEventListener('focus', refreshUnreadCount);
        
        // Poll every 5 seconds to detect new messages and play sound
        setInterval(refreshUnreadCount, 3000);
    </script>

</body>
</html>
