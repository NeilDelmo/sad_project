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
      <a href="{{ route('fisherman.messages') }}" class="nav-link {{ request()->routeIs('fisherman.messages') ? 'active' : '' }}">
        <i class="fa-solid fa-envelope"></i> Messages
        <span id="unread-message-count" style="background:#dc3545;color:#fff;padding:2px 8px;border-radius:12px;font-size:12px;display: {{ isset($unreadCount) && $unreadCount > 0 ? 'inline-block' : 'none' }};">{{ $unreadCount ?? 0 }}</span>
      </a>
      <a href="{{ route('fishing-safety.public') }}" class="nav-link {{ request()->routeIs('fishing-safety.*') ? 'active' : '' }}">
        <i class="fa-solid fa-life-ring"></i> Safety Map
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
</nav>
