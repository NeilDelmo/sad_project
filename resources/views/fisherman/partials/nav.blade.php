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
    <a class="nav-brand" href="{{ route('marketplace.index') }}" style="text-decoration: none;">SeaLedger</a>
    
    <div class="nav-links">
      <a href="{{ route('fisherman.dashboard') }}" class="nav-link {{ request()->routeIs('fisherman.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge-high"></i> Dashboard
      </a>
      <a href="{{ route('fisherman.products.index') }}" class="nav-link {{ request()->routeIs('fisherman.products.*') ? 'active' : '' }}">
        <i class="fa-solid fa-box"></i> My Products
      </a>
      <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
        <i class="fa-solid fa-receipt"></i> Orders
      </a>
      <a href="{{ route('fisherman.offers.index') }}" class="nav-link {{ request()->routeIs('fisherman.offers.*') ? 'active' : '' }}">
        <i class="fa-solid fa-handshake"></i> Offers
      </a>
      <a href="{{ route('fishing-safety.public') }}" class="nav-link {{ request()->routeIs('fishing-safety.*') ? 'active' : '' }}">
        <i class="fa-solid fa-life-ring"></i> Safety Map
      </a>
      <a href="{{ route('marketplace.index') }}" class="nav-link {{ request()->routeIs('marketplace.*') ? 'active' : '' }}">
        <i class="fa-solid fa-store"></i> Marketplace & Forum
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

@include('partials.toast-notifications')
