<nav class="navbar">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <a class="nav-brand" href="{{ route('vendor.dashboard') }}" style="text-decoration:none;">🐟 SeaLedger</a>
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
      <a href="{{ route('vendor.messages') }}" class="nav-link {{ request()->routeIs('vendor.messages') ? 'active' : '' }}">
        <i class="fa-solid fa-envelope"></i> Messages
        @if(isset($vendorUnread) && $vendorUnread > 0)
          <span style="background:#dc3545;color:#fff;padding:2px 8px;border-radius:12px;font-size:12px;">{{ $vendorUnread }}</span>
        @endif
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
