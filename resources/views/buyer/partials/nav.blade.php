<nav class="navbar">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <a class="nav-brand" href="{{ route('marketplace.index') }}" style="text-decoration: none;">
      <img src="{{ asset('images/logo.png') }}" alt="SeaLedger Logo" class="nav-logo">
      SeaLedger
    </a>
    <div class="nav-links">
      <a href="{{ route('marketplace.index') }}" class="nav-link">
        <i class="fa-solid fa-store"></i> Marketplace
      </a>
      <a href="{{ route('marketplace.cart.index') }}" class="nav-link">
        <i class="fa-solid fa-cart-shopping"></i> Cart
      </a>
      <a href="{{ route('marketplace.orders.index') }}" class="nav-link">
        <i class="fa-solid fa-receipt"></i> Orders
      </a>
      <a href="{{ route('forums.index') }}" class="nav-link">
        <i class="fa-solid fa-comments"></i> Forum
      </a>

      <form method="POST" action="{{ route('logout') }}" class="nav-link logout-link">
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

@include('partials.toast-notifications')
