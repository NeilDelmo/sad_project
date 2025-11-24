<nav class="navbar">
  <div class="container-fluid">
    <div class="nav-left-group">
        <a class="nav-brand" href="{{ route('vendor.dashboard') }}" style="text-decoration: none;">
            <img src="{{ asset('images/logo.png') }}" alt="SeaLedger Logo" class="nav-logo">
            SeaLedger
        </a>
        @if(Auth::check() && Auth::user()->user_type === 'vendor')
            @include('components.trust-badge', ['score'=>Auth::user()->trust_score,'tier'=>Auth::user()->trust_tier,'compact'=>false])
            
            @if(Auth::user()->hasVerifiedEmail())
                <div class="verification-badge verified" title="Account Verified">
                    <i class="fa-solid fa-circle-check"></i> Verified
                </div>
            @else
                <div class="verification-badge unverified" title="Email Unverified">
                    <i class="fa-solid fa-circle-exclamation"></i> Unverified
                </div>
            @endif
        @endif
    </div>

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
      <a href="{{ route('marketplace.index') }}" class="nav-link {{ request()->routeIs('marketplace.index') || request()->routeIs('marketplace.shop') ? 'active' : '' }}">
        <i class="fa-solid fa-store"></i> Marketplace & Forum
      </a>
      <a href="{{ route('marketplace.orders.index') }}" class="nav-link {{ request()->routeIs('marketplace.orders.*') ? 'active' : '' }}">
        <i class="fa-solid fa-shopping-cart"></i> Marketplace Orders
      </a>
    </div>
      
    <div class="nav-right-actions">
        @include('partials.notification-bell')
        <form method="POST" action="{{ route('logout') }}" class="nav-link logout-link" style="padding:0; margin-left: 8px;">
        @csrf
        <button type="submit">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
        </form>
    </div>
  </div>
</nav>

<style>
  .navbar {
    background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
    padding: 18px 0;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    position: sticky;
    top: 0;
    z-index: 1000;
    margin-bottom: 0;
  }

  .navbar .container-fluid {
    width: 100%;
    max-width: 1400px;
    padding: 0 20px;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    gap: 16px;
    flex-wrap: nowrap;
    margin: 0 auto;
  }

  .nav-left-group {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .nav-right-actions {
    display: flex;
    align-items: center;
  }

  .nav-brand {
    flex-shrink: 0;
    color: white;
    font-size: 32px;
    font-weight: bold;
    font-family: "Koulen", sans-serif;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .nav-logo {
    height: 40px;
    width: auto;
  }

  .verification-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: white;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .verification-badge.verified {
    background: rgba(34, 197, 94, 0.2);
    border-color: rgba(34, 197, 94, 0.4);
    color: #dcfce7;
  }

  .verification-badge.unverified {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.4);
    color: #fee2e2;
  }

  .nav-links {
    display: flex !important;
    gap: 8px !important;
    align-items: center !important;
    flex-wrap: nowrap !important;
    overflow-x: auto !important;
    flex: 0 1 auto !important;
    min-width: 0 !important;
  }

  .nav-links::-webkit-scrollbar {
    display: none;
  }

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
    border: none;
    cursor: pointer;
    font-family: Arial, sans-serif;
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
    transform: translateY(-1px);
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

  .logout-link {
    padding: 0;
  }

  .logout-link button {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.9);
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .logout-link button:hover {
    color: white;
    background: rgba(255, 255, 255, 0.15);
  }
</style>

<script>
  // Force reload when page is restored from back/forward cache
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

@include('partials.toast-notifications')
