<style>
  .navbar .nav-layout {
    width: 100%;
    max-width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: nowrap;
  }

  .navbar .nav-links {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: nowrap;
    white-space: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    flex: 0 1 auto;
    min-width: 0;
  }

  .navbar .nav-links::-webkit-scrollbar {
    display: none;
  }

  .navbar .nav-link {
    white-space: nowrap;
  }

  .navbar .nav-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
    margin-left: 12px;
  }
</style>

<nav class="navbar">
  <div class="nav-layout">
    <a class="nav-brand" href="{{ route('vendor.dashboard') }}" style="text-decoration:none;">SeaLedger</a>
    @if(Auth::check() && Auth::user()->user_type === 'vendor')
      <div style="margin-left:8px;">
        @include('components.trust-badge', ['score'=>Auth::user()->trust_score,'tier'=>Auth::user()->trust_tier,'compact'=>false])
      </div>
    @endif
    
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
        <i class="fa-solid fa-shopping-cart"></i> My Orders
      </a>
    </div>

    <div class="nav-actions">
      @include('partials.notification-bell')
      <form method="POST" action="{{ route('logout') }}" style="display:inline;">
        @csrf
        <button type="submit" class="nav-link" style="background:none;border:none;cursor:pointer;">
          <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
      </form>
    </div>
  </div>
</nav>

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
