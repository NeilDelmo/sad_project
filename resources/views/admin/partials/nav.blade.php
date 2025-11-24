<style>
  .navbar {
    background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
    padding: 15px 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    width: 100%;
    margin: 0;
  }

  .navbar .container-fluid {
    width: 100%;
    max-width: 100%;
    padding: 0 40px;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    gap: 16px;
    flex-wrap: nowrap;
  }

  .nav-brand {
    color: white;
    font-size: 24px;
    font-weight: bold;
    font-family: "Koulen", sans-serif;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
  }

  .nav-brand:hover {
    color: white;
  }

  .nav-logo {
    height: 40px;
    width: auto;
  }

  .nav-links {
    display: flex;
    gap: 5px;
    align-items: center;
    flex-wrap: nowrap;
    white-space: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .nav-links::-webkit-scrollbar {
    display: none;
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
    white-space: nowrap;
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

  .navbar .nav-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
    margin-left: 12px;
  }
</style>

<nav class="navbar">
  <div class="container-fluid">
    <a class="nav-brand" href="{{ route('dashboard') }}">
      <img src="{{ asset('images/logo.png') }}" alt="SeaLedger Logo" class="nav-logo">
      SeaLedger Admin
    </a>
    
    <div class="nav-links">
      <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge-high"></i> Dashboard
      </a>
      <a href="{{ route('rentals.admin.index') }}" class="nav-link {{ request()->routeIs('rentals.admin.index') ? 'active' : '' }}">
        <i class="fa-solid fa-toolbox"></i> Rentals
      </a>
      <a href="{{ route('rentals.admin.maintenance') }}" class="nav-link {{ request()->routeIs('rentals.admin.maintenance') ? 'active' : '' }}">
        <i class="fa-solid fa-wrench"></i> Maintenance
      </a>
      <a href="{{ route('rentals.admin.reports') }}" class="nav-link {{ request()->routeIs('rentals.admin.reports') ? 'active' : '' }}">
        <i class="fa-solid fa-flag"></i> Reports
      </a>
      <a href="{{ route('admin.revenue.index') }}" class="nav-link {{ request()->routeIs('admin.revenue.*') ? 'active' : '' }}">
        <i class="fa-solid fa-chart-line"></i> Revenue
      </a>
      <a href="{{ route('admin.ml.analytics') }}" class="nav-link {{ request()->routeIs('admin.ml.analytics') ? 'active' : '' }}">
        <i class="fa-solid fa-brain"></i> ML Analytics
      </a>
      <a href="https://dashboard.simpleanalytics.com" target="_blank" class="nav-link">
        <i class="fa-solid fa-chart-simple"></i> Web Analytics
      </a>
      <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="fa-solid fa-users"></i> Users
      </a>
    </div>

    <div class="nav-actions">
      <form method="POST" action="{{ route('logout') }}" style="display:inline;">
        @csrf
        <button type="submit" class="nav-link" style="background:none;border:none;cursor:pointer;">
          <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
      </form>
    </div>
  </div>
</nav>
