<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Marketplace Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');
    body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
    .container-custom { max-width: 1200px; margin: 0 auto; padding: 32px 20px; }
    .page-title { font-family: "Koulen", sans-serif; font-size: 36px; color: #1B5E88; letter-spacing: .5px; }
    .filter-card { background:#fff; padding: 16px 20px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border:1px solid rgba(0,0,0,0.05); }
    .status-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom: 20px; }
    .status-tab { background:#fff; color:#64748b; border:2px solid #e2e8f0; padding:10px 20px; border-radius:12px; font-weight:600; transition:.2s; cursor:pointer; text-decoration:none; }
    .status-tab:hover { background:#f8fafc; color:#334155; }
    .status-tab.active { background:#0075B5; color:#fff; border-color:#0075B5; }
    .orders-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; margin-top: 20px; }
    .order-card { background:#fff; border-radius:16px; padding:16px; box-shadow: 0 6px 18px rgba(0,0,0,.08); border: 2px solid transparent; transition: .2s; }
    .order-card:hover { transform: translateY(-3px); border-color:#0075B5; box-shadow: 0 12px 28px rgba(0,0,0,.12); }
    .order-header { display:flex; align-items:center; gap:12px; border-bottom:1px solid #eef2f7; padding-bottom:12px; margin-bottom:12px; }
    .order-image { width:72px; height:72px; border-radius:12px; object-fit:cover; background:#f1f5f9; }
    .order-title { font-weight:800; color:#1B5E88; margin:0; font-size: 18px; }
    .order-meta { color:#64748b; font-size: 13px; }
    .order-badges { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
    .order-body { display:grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; margin-bottom: 12px; }
    .label { color:#6c757d; font-weight:500; font-size:12px; }
    .value { color:#2c3e50; font-weight:700; font-size:14px; }
    .order-actions { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
    .order-actions .btn { border-radius: 10px; }
    .empty-state { text-align:center; padding: 80px 20px; background:#fff; border-radius:16px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
    .navbar {
      background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
      padding: 18px 0;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
      position: sticky;
      top: 0;
      z-index: 1000;
      margin-bottom: 0;
    }
    .nav-brand {
      color: white;
      font-size: 32px;
      font-weight: bold;
      font-family: "Koulen", sans-serif;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
      letter-spacing: 1px;
      text-decoration: none;
    }
    .nav-links { display: flex; gap: 8px; align-items: center; }
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
    }
    .nav-link:hover { color: #fff; background: rgba(255,255,255,0.15); transform: translateY(-1px); }
    .nav-link.active { background: rgba(255,255,255,0.25); color: #fff; font-weight: 600; }
  </style>
  <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
  
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <a class="nav-brand" href="{{ route('marketplace.index') }}">🐟 SeaLedger</a>
    <div class="nav-links">
      <a href="{{ route('marketplace.shop') }}" class="nav-link">
        <i class="fa-solid fa-fire"></i> Latest
      </a>
      @auth
        @if(auth()->user()->user_type === 'buyer')
          <a href="{{ route('marketplace.orders.index') }}" class="nav-link active">
            <i class="fa-solid fa-receipt"></i> My Orders
          </a>
        @endif
      @endauth
      @if(Auth::check())
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
          @csrf
          <button type="submit" class="nav-link">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
          </button>
        </form>
      @else
        <a href="{{ route('login') }}" class="nav-link">
          <i class="fa-solid fa-right-to-bracket"></i> Login
        </a>
      @endif
    </div>
  </div>
</nav>

<div class="container-custom">
  <div class="mb-3">
    <h1 class="page-title mb-0">Marketplace Orders</h1>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @php
    $currentStatus = request('status');
    $statuses = [
      ['value' => '', 'label' => 'All', 'icon' => 'list'],
      ['value' => 'pending_payment', 'label' => 'Pending', 'icon' => 'clock'],
      ['value' => 'in_transit', 'label' => 'In Transit', 'icon' => 'truck-fast'],
      ['value' => 'delivered', 'label' => 'Delivered', 'icon' => 'box'],
      ['value' => 'received', 'label' => 'Received', 'icon' => 'circle-check'],
      ['value' => 'refund_requested', 'label' => 'Refund Req.', 'icon' => 'rotate-left'],
      ['value' => 'refunded', 'label' => 'Refunded', 'icon' => 'money-bill-transfer'],
    ];
  @endphp

  <div class="status-tabs">
    @foreach($statuses as $status)
      <a href="{{ route('marketplace.orders.index', ['status' => $status['value']]) }}" 
         class="status-tab {{ ($currentStatus === $status['value'] || ($currentStatus === null && $status['value'] === '')) ? 'active' : '' }}">
        <i class="fa-solid fa-{{ $status['icon'] }}"></i> {{ $status['label'] }}
      </a>
    @endforeach
  </div>

  <div class="orders-grid">
    @forelse($orders as $order)
      @php 
        $product = optional($order->listing->product ?? null);
        $image = $product?->image_path;
        $badge = match($order->status) {
          'pending_payment' => 'secondary',
          'in_transit' => 'info',
          'delivered' => 'warning',
          'received' => 'success',
          'refund_requested' => 'danger',
          'refunded' => 'secondary',
          'refund_declined' => 'secondary',
          default => 'secondary'
        };
        $user = auth()->user();
        $uom = $product?->unit_of_measure ?? 'kg';
      @endphp
      <div class="order-card">
        <div class="order-header">
          @if($image)
            <img src="{{ asset($image) }}" class="order-image" alt="{{ $product?->name }}">
          @else
            <div class="order-image d-flex align-items-center justify-content-center"><i class="fa-solid fa-fish" style="color:#0075B5;"></i></div>
          @endif
          <div class="flex-grow-1">
            <h5 class="order-title">{{ $product?->name ?? ('Product #'.$order->listing_id) }}</h5>
            <div class="order-meta">Order #{{ $order->id }}</div>
          </div>
          <div class="order-badges">
            <span class="badge bg-{{ $badge }} text-uppercase">{{ str_replace('_',' ', $order->status) }}</span>
          </div>
        </div>

        <div class="order-body">
          <div>
            <div class="label">Quantity</div>
            <div class="value">{{ $order->quantity }} {{ $uom }}</div>
          </div>
          <div>
            <div class="label">Total</div>
            <div class="value">₱{{ number_format($order->total, 2) }}</div>
          </div>
          <div>
            <div class="label">Buyer</div>
            <div class="value">{{ optional($order->buyer)->name ?? ('Buyer #'.$order->buyer_id) }}</div>
          </div>
          <div>
            <div class="label">Vendor</div>
            <div class="value">{{ optional($order->vendor)->name ?? ('Vendor #'.$order->vendor_id) }}</div>
          </div>
          <div>
            <div class="label">Placed</div>
            <div class="value">{{ $order->created_at?->diffForHumans() }}</div>
          </div>
          @if($order->delivered_at)
          <div>
            <div class="label">Delivered</div>
            <div class="value">{{ $order->delivered_at?->diffForHumans() }}</div>
          </div>
          @endif
          @if($order->delivered_at && $order->isRefundWindowOpen() && in_array($order->status, ['delivered','received']))
          <div>
            <div class="label">Refund Window</div>
            <div class="value" style="color: #dc3545; font-weight: 600;">
              <i class="fa-solid fa-clock"></i>
              <span class="refund-countdown" data-delivered="{{ $order->delivered_at->toIso8601String() }}">Calculating...</span>
            </div>
          </div>
          @endif
          @if($order->received_at)
          <div>
            <div class="label">Received</div>
            <div class="value">{{ $order->received_at?->diffForHumans() }}</div>
          </div>
          @endif
          <div>
            <div class="label">Proof</div>
            <div>
              @if($order->proof_photo_path)
                <a href="{{ asset('storage/'.$order->proof_photo_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
              @else
                <span class="text-muted">—</span>
              @endif
              @if($order->refund_proof_path)
                <a href="{{ asset('storage/'.$order->refund_proof_path) }}" target="_blank" class="btn btn-sm btn-outline-danger ms-1">Refund Proof</a>
              @endif
            </div>
          </div>
        </div>

        <div class="order-actions">
          @if($user && $user->id === $order->vendor_id && $order->status==='pending_payment')
            <form method="post" action="{{ route('marketplace.orders.intransit', $order) }}">
              @csrf
              <button class="btn btn-info"><i class="fa-solid fa-truck-fast"></i> In Transit</button>
            </form>
          @endif
          @if($user && $user->id === $order->vendor_id && in_array($order->status, ['pending_payment','in_transit']))
            <form method="post" action="{{ route('marketplace.orders.delivered', $order) }}" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
              @csrf
              <input type="file" name="proof" accept="image/*" class="form-control form-control-sm" style="max-width: 230px;" required>
              <button class="btn btn-warning"><i class="fa-solid fa-box"></i> Mark Delivered</button>
            </form>
          @endif
          @if($user && $user->id === $order->buyer_id && $order->status==='delivered')
            <form method="post" action="{{ route('marketplace.orders.received', $order) }}">
              @csrf
              <button class="btn btn-success"><i class="fa-solid fa-circle-check"></i> Confirm Received</button>
            </form>
          @endif
          @if($user && $user->id === $order->buyer_id && in_array($order->status, ['delivered','received']))
            @if($order->isRefundWindowOpen())
              <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#refundModal{{ $order->id }}"><i class="fa-solid fa-rotate-left"></i> Request Refund</button>
            @else
              <button class="btn btn-outline-secondary" disabled title="Refund window closed (3 hours after delivery)"><i class="fa-solid fa-ban"></i> Refund Window Closed</button>
            @endif
          @endif
          @if($user && $user->id === $order->buyer_id && $order->status === 'refund_declined')
            <span class="badge bg-danger" style="padding: 8px 16px; font-size: 13px;">
              <i class="fa-solid fa-circle-xmark"></i> Refund Declined by Vendor
            </span>
          @endif
          @if($user && $user->id === $order->vendor_id && $order->status==='refund_requested')
            <form method="post" action="{{ route('marketplace.orders.refund.approve', $order) }}">
              @csrf
              <button class="btn btn-outline-success"><i class="fa-solid fa-thumbs-up"></i> Approve</button>
            </form>
            <form method="post" action="{{ route('marketplace.orders.refund.decline', $order) }}">
              @csrf
              <button class="btn btn-outline-danger"><i class="fa-solid fa-thumbs-down"></i> Decline</button>
            </form>
          @endif
        </div>

        @if($user && $user->id === $order->buyer_id && in_array($order->status, ['delivered','received']))
        <div class="modal fade" id="refundModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Request Refund - Order #{{ $order->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="post" action="{{ route('marketplace.orders.refund.request', $order) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">Reason</label>
                    <select name="reason" class="form-select" required>
                      <option value="bad_delivery">Bad Delivery</option>
                      <option value="poor_quality">Poor Quality (smelly/rotten)</option>
                      <option value="never_received">Never Received</option>
                      <option value="damaged_on_arrival">Damaged on Arrival</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Upload Proof Photo</label>
                    <input type="file" name="proof" accept="image/*" class="form-control" required>
                    <div class="form-text">Max 4MB. JPG/PNG only.</div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Notes (optional)</label>
                    <textarea name="notes" class="form-control" rows="2" maxlength="500" placeholder="Describe the issue..."></textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-danger">Submit Request</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        @endif

      </div>
    @empty
      <div class="empty-state">
        <i class="fa-solid fa-box-open fa-3x" style="color:#d1d5db;"></i>
        <h3 class="mt-3" style="font-weight:800; color:#1B5E88;">No Orders Yet</h3>
        <p class="mb-0" style="color:#6b7280;">When you place or receive orders, they’ll appear here.</p>
      </div>
    @endforelse
  </div>

  <div class="mt-3">
    {{ $orders->links() }}
  </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Refund countdown timer (3-hour window from delivery)
function updateRefundCountdowns() {
  const countdowns = document.querySelectorAll('.refund-countdown');
  
  countdowns.forEach(countdown => {
    const deliveredAt = new Date(countdown.dataset.delivered);
    const now = new Date();
    const hoursSinceDelivery = (now - deliveredAt) / (1000 * 60 * 60);
    
    if (hoursSinceDelivery >= 3) {
      countdown.textContent = 'Window Closed';
      countdown.style.color = '#6c757d';
      return;
    }
    
    const totalMinutes = 3 * 60;
    const elapsedMinutes = Math.floor((now - deliveredAt) / (1000 * 60));
    const remainingMinutes = totalMinutes - elapsedMinutes;
    
    if (remainingMinutes <= 0) {
      countdown.textContent = 'Window Closed';
      countdown.style.color = '#6c757d';
      return;
    }
    
    const hours = Math.floor(remainingMinutes / 60);
    const minutes = remainingMinutes % 60;
    
    if (hours > 0) {
      countdown.textContent = `${hours}h ${minutes}m remaining`;
    } else {
      countdown.textContent = `${minutes}m remaining`;
    }
    
    // Red alert if less than 30 minutes
    if (remainingMinutes < 30) {
      countdown.style.color = '#dc3545';
      countdown.style.fontWeight = '700';
    }
  });
}

// Update every minute
updateRefundCountdowns();
setInterval(updateRefundCountdowns, 60000);
</script>
</body>
</html>
