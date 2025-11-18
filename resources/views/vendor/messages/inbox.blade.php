<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Defense-in-depth: discourage caching at the document level -->
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Vendor Inbox</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
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
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap;
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

        .card { border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .conv-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; border-bottom: 1px solid #eee; text-decoration: none; color: inherit; }
        .conv-item:hover { background: #f8fafc; }
        .badge-unread { background: #dc3545; }
        .empty { text-align: center; padding: 80px 20px; color: #6c757d; }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="container-fluid">
        <!-- Logo row -->
        <div class="w-100 text-start mb-2">
            <a class="nav-brand" href="{{ route('vendor.dashboard') }}" style="text-decoration:none;">🐟 SeaLedger</a>
        </div>
        
        <!-- Nav links row -->
        <div class="w-100 text-start">
            <div class="nav-links">
                <a href="{{ route('vendor.dashboard') }}" class="nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a href="{{ route('vendor.products.index') }}" class="nav-link {{ request()->routeIs('vendor.products.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-fish"></i> Browse
                </a>
                <a href="{{ route('vendor.inventory.index') }}" class="nav-link {{ request()->routeIs('vendor.inventory.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-box"></i> Inventory
                </a>
                <a href="{{ route('vendor.offers.index') }}" class="nav-link {{ request()->routeIs('vendor.offers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-handshake"></i> Offers
                </a>
                <a href="{{ route('marketplace.index') }}" class="nav-link {{ request()->routeIs('marketplace.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-store"></i> Marketplace
                </a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="nav-link" style="background:none;border:none;cursor:pointer;">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<div class="container my-4">
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fa-solid fa-inbox"></i> Inbox</h5>
        </div>
        <div class="list-group list-group-flush">
            @forelse($conversations as $c)
                <a class="list-group-item conv-item" href="{{ route('marketplace.message', $c->id) }}">
                    <div>
                        <div class="fw-bold">{{ $c->product->name ?? 'Conversation' }}</div>
                        <div class="text-muted small">with {{ $c->buyer->username ?? ('User #'.$c->buyer_id) }} • {{ $c->latestMessage?->created_at?->diffForHumans() }}</div>
                        @if($c->latestMessage)
                            <div class="text-truncate" style="max-width: 520px;">{{ $c->latestMessage->message }}</div>
                        @endif
                    </div>
                    <div class="text-end">
                        @if(($c->unread_count ?? 0) > 0)
                            <span class="badge rounded-pill badge-unread">{{ $c->unread_count }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="empty">No conversations yet.</div>
            @endforelse
        </div>
    </div>
</div>

<script>
    // Refresh conversation list when returning from conversation page
    window.addEventListener('focus', function() {
        const lastPath = document.referrer;
        if (lastPath && lastPath.includes('/marketplace/message/')) {
            window.location.reload();
        }
    });

    // Force reload if page is restored from BFCache (back/forward)
    (function() {
        function shouldReloadFromHistory(e) {
            if (e && e.persisted) return true;
            try {
                var navs = performance.getEntriesByType && performance.getEntriesByType('navigation');
                if (navs && navs[0] && navs[0].type === 'back_forward') return true;
            } catch (err) {}
            return false;
        }
        window.addEventListener('pageshow', function(e) {
            if (shouldReloadFromHistory(e)) {
                window.location.reload();
            }
        });
    })();
</script>

@include('partials.message-notification')

</body>
</html>
