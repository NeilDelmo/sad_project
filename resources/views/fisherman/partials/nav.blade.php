<nav class="navbar">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <a class="nav-brand" href="{{ route('marketplace.index') }}" style="text-decoration: none;">🐟 SeaLedger</a>
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
        <i class="fa-solid fa-store"></i> Marketplace
      </a>
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
