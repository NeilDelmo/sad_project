<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
  <title>Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Koulen&display=swap">
  <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
    }

    /* Fisherman navbar styling to match dashboard */
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
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .nav-logo {
      height: 40px;
      width: auto;
    }

    .nav-links {
      display: flex;
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

    /* Fix navbar layout - brand left, links right */
    .navbar .container-fluid {
      display: flex !important;
      justify-content: space-between !important;
      align-items: center !important;
      flex-wrap: nowrap !important;
    }

    .navbar .nav-brand {
      flex-shrink: 0;
    }

    .navbar .nav-links {
      flex-shrink: 0;
      margin-left: auto;
    }

    /* Order card styling */
    .order-card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
      border-left: 4px solid #0075B5;
    }

    .order-card:hover {
      box-shadow: 0 4px 16px rgba(0,0,0,0.12);
      transform: translateY(-2px);
    }

    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      padding-bottom: 15px;
      border-bottom: 2px solid #f0f0f0;
    }

    .order-id {
      font-size: 20px;
      font-weight: bold;
      color: #1B5E88;
    }

    .order-body {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 15px;
    }

    .order-detail {
      display: flex;
      flex-direction: column;
    }

    .order-label {
      font-size: 12px;
      color: #666;
      text-transform: uppercase;
      font-weight: 600;
      margin-bottom: 5px;
    }

    .order-value {
      font-size: 16px;
      color: #333;
      font-weight: 500;
    }

    .order-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      padding-top: 15px;
      border-top: 1px solid #f0f0f0;
    }

    .status-badge {
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
    }

    .status-pending_payment { background: #e2e3e5; color: #383d41; }
    .status-in_transit { background: #d1ecf1; color: #0c5460; }
    .status-delivered { background: #fff3cd; color: #856404; }
    .status-received { background: #d4edda; color: #155724; }
    .status-refund_requested { background: #f8d7da; color: #721c24; }
    .status-refunded { background: #e2e3e5; color: #383d41; }
    .status-refund_declined { background: #f8d7da; color: #721c24; }
    
    /* Status filter tabs */
    .status-filter-tabs {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      padding: 15px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .status-tab {
      padding: 10px 20px;
      border-radius: 8px;
      border: 2px solid #e0e0e0;
      background: white;
      color: #666;
      text-decoration: none;
      font-weight: 600;
      font-size: 14px;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .status-tab:hover {
      border-color: var(--tab-color, #0075B5);
      background: rgba(0, 117, 181, 0.05);
      color: var(--tab-color, #0075B5);
      transform: translateY(-2px);
    }
    
    .status-tab.active {
      background: var(--tab-color, #0075B5);
      color: white;
      border-color: var(--tab-color, #0075B5);
      box-shadow: 0 4px 12px rgba(0, 117, 181, 0.3);
    }
    
    .status-tab i {
      font-size: 16px;
    }
    
    /* Transaction timeline */
    .transaction-timeline {
      margin: 15px 0;
      padding: 15px;
      background: #f8f9fa;
      border-radius: 8px;
      border-left: 4px solid #0075B5;
    }
    
    .timeline-title {
      font-size: 14px;
      font-weight: 700;
      color: #1B5E88;
      margin-bottom: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .timeline-steps {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    
    .timeline-step {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 8px;
      border-radius: 6px;
      transition: background 0.2s ease;
    }
    
    .timeline-step:hover {
      background: rgba(0, 117, 181, 0.05);
    }
    
    .timeline-step.completed {
      color: #198754;
    }
    
    .timeline-step.active {
      color: #0075B5;
      font-weight: 600;
      background: rgba(0, 117, 181, 0.1);
    }
    
    .timeline-step.pending {
      color: #adb5bd;
    }
    
    .timeline-icon {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 14px;
    }
    
    .timeline-step.completed .timeline-icon {
      background: #198754;
      color: white;
    }
    
    .timeline-step.active .timeline-icon {
      background: #0075B5;
      color: white;
    }
    
    .timeline-step.pending .timeline-icon {
      background: #e9ecef;
      color: #adb5bd;
    }
    
    .timeline-text {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    
    .timeline-label {
      font-size: 14px;
      font-weight: 600;
    }
    
    .timeline-time {
      font-size: 12px;
      opacity: 0.8;
    }
  </style>
</head>
<body>
@php $user = Auth::user(); @endphp
@if($user && $user->user_type === 'fisherman')
  @include('fisherman.partials.nav')
@elseif($user && $user->user_type === 'vendor')
  @include('vendor.partials.nav')
@endif
<div class="container py-4">
  <div class="mb-4">
    <h1 class="h3 mb-3">My Orders</h1>
    
    @php
      $currentStatus = request('status');
      $statuses = [
        ['value' => '', 'label' => 'All Orders', 'icon' => 'list', 'color' => '#6c757d'],
        ['value' => 'pending_payment', 'label' => 'Pending', 'icon' => 'clock', 'color' => '#6c757d'],
        ['value' => 'in_transit', 'label' => 'In Transit', 'icon' => 'truck-fast', 'color' => '#0dcaf0'],
        ['value' => 'delivered', 'label' => 'Delivered', 'icon' => 'box-open', 'color' => '#ffc107'],
        ['value' => 'received', 'label' => 'Received', 'icon' => 'check-circle', 'color' => '#198754'],
        ['value' => 'refund_requested', 'label' => 'Refund Req.', 'icon' => 'exclamation-triangle', 'color' => '#dc3545'],
        ['value' => 'refunded', 'label' => 'Refunded', 'icon' => 'undo', 'color' => '#6c757d'],
        ['value' => 'refund_declined', 'label' => 'Declined', 'icon' => 'times-circle', 'color' => '#dc3545'],
      ];
    @endphp
    
    <div class="status-filter-tabs">
      @foreach($statuses as $status)
        <a href="{{ route('orders.index', ['status' => $status['value']]) }}" 
           class="status-tab {{ ($currentStatus === $status['value'] || ($currentStatus === null && $status['value'] === '')) ? 'active' : '' }}"
           style="--tab-color: {{ $status['color'] }}">
          <i class="fa-solid fa-{{ $status['icon'] }}"></i>
          <span>{{ $status['label'] }}</span>
        </a>
      @endforeach
    </div>
  </div>

  @foreach($orders as $order)
    @php 
      $user = auth()->user();
      $statusIcon = match($order->status) {
        'pending_payment' => 'fa-clock',
        'in_transit' => 'fa-truck',
        'delivered' => 'fa-box-open',
        'received' => 'fa-check-circle',
        'refund_requested' => 'fa-exclamation-triangle',
        'refunded' => 'fa-undo',
        'refund_declined' => 'fa-times-circle',
        default => 'fa-question-circle'
      };
    @endphp

    <div class="order-card">
      <div class="order-header">
        <div>
          <span class="order-id"><i class="fa-solid fa-receipt"></i> Order #{{ $order->formatted_order_number }}</span>
          <div style="font-size: 14px; color: #666; margin-top: 5px;">
            <strong>{{ optional($order->product)->name ?? 'Product #'.$order->product_id }}</strong>
          </div>
          <div style="font-size: 13px; color: #888; margin-top: 3px;">
            @if($user->user_type === 'vendor')
              <i class="fa-solid fa-user-tie"></i> Fisherman: <strong>{{ optional($order->fisherman)->name ?? optional($order->fisherman)->username ?? 'N/A' }}</strong>
            @else
              <i class="fa-solid fa-store"></i> Vendor: <strong>{{ optional($order->vendor)->name ?? optional($order->vendor)->username ?? 'N/A' }}</strong>
            @endif
          </div>
        </div>
        <span class="status-badge status-{{ $order->status }}">
          <i class="fa-solid {{ $statusIcon }}"></i> {{ str_replace('_',' ', ucfirst($order->status)) }}
        </span>
      </div>

      <div class="order-body">
        <div class="order-detail">
          <span class="order-label">Order Number</span>
          <span class="order-value" style="color: #1B5E88; font-weight: 700; font-family: monospace; letter-spacing: 1px;">#{{ $order->formatted_order_number }}</span>
        </div>
        <div class="order-detail">
          <span class="order-label">Quantity</span>
          <span class="order-value">{{ $order->quantity }} {{ $order->product->unit_of_measure ?? 'kg' }}</span>
        </div>
        <div class="order-detail">
          <span class="order-label">Unit Price</span>
          <span class="order-value">₱{{ number_format($order->unit_price, 2) }}</span>
        </div>
        <div class="order-detail">
          <span class="order-label">Total Amount</span>
          <span class="order-value" style="color: #16a34a; font-weight: 700;">₱{{ number_format($order->total, 2) }}</span>
        </div>
        <div class="order-detail">
          <span class="order-label">Order Date</span>
          <span class="order-value">{{ $order->created_at->format('M d, Y g:i A') }}</span>
        </div>
        @if($order->proof_photo_path)
        <div class="order-detail">
          <span class="order-label">Delivery Proof</span>
          <a href="{{ asset('storage/'.$order->proof_photo_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
            <i class="fa-solid fa-image"></i> View Photo
          </a>
        </div>
        @endif
        @if($order->refund_proof_path)
        <div class="order-detail">
          <span class="order-label">Refund Proof</span>
          <a href="{{ asset('storage/'.$order->refund_proof_path) }}" target="_blank" class="btn btn-sm btn-outline-danger mt-1">
            <i class="fa-solid fa-image"></i> View Proof
          </a>
        </div>
        @endif
      </div>

      <!-- Transaction Timeline -->
      <div class="transaction-timeline">
        <div class="timeline-title"><i class="fa-solid fa-timeline"></i> Transaction Flow</div>
        <div class="timeline-steps">
          @php
            $steps = [
              ['status' => 'pending_payment', 'label' => 'Order Placed', 'icon' => 'fa-receipt', 'time' => $order->created_at],
              ['status' => 'in_transit', 'label' => 'In Transit', 'icon' => 'fa-truck-fast', 'time' => null],
              ['status' => 'delivered', 'label' => 'Delivered', 'icon' => 'fa-box-open', 'time' => $order->delivered_at],
              ['status' => 'received', 'label' => 'Received & Confirmed', 'icon' => 'fa-check-circle', 'time' => $order->received_at],
            ];
            
            // Add refund steps if applicable
            if (in_array($order->status, ['refund_requested', 'refunded', 'refund_declined'])) {
              if ($order->status === 'refund_requested') {
                $steps[] = ['status' => 'refund_requested', 'label' => 'Refund Requested', 'icon' => 'fa-exclamation-triangle', 'time' => $order->updated_at];
              } elseif ($order->status === 'refunded') {
                $steps[] = ['status' => 'refund_requested', 'label' => 'Refund Requested', 'icon' => 'fa-exclamation-triangle', 'time' => null];
                $steps[] = ['status' => 'refunded', 'label' => 'Refunded', 'icon' => 'fa-undo', 'time' => $order->refund_at];
              } else {
                $steps[] = ['status' => 'refund_requested', 'label' => 'Refund Requested', 'icon' => 'fa-exclamation-triangle', 'time' => null];
                $steps[] = ['status' => 'refund_declined', 'label' => 'Refund Declined', 'icon' => 'fa-times-circle', 'time' => $order->refund_at];
              }
            }
            
            $currentIndex = collect($steps)->search(fn($s) => $s['status'] === $order->status);
          @endphp
          
          @foreach($steps as $index => $step)
            @php
              $stepClass = $index < $currentIndex ? 'completed' : ($index === $currentIndex ? 'active' : 'pending');
            @endphp
            <div class="timeline-step {{ $stepClass }}">
              <div class="timeline-icon">
                <i class="fa-solid {{ $step['icon'] }}"></i>
              </div>
              <div class="timeline-text">
                <span class="timeline-label">{{ $step['label'] }}</span>
                @if($step['time'])
                  <span class="timeline-time">{{ $step['time']->format('M d, Y g:i A') }}</span>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>

      @if(in_array($order->status, ['refund_requested', 'refunded', 'refund_declined']))
      <div class="order-detail">
        <span class="order-label">Refund Status</span>
        <span class="order-value">
          @if($order->status === 'refund_requested')
            <span style="color: #dc2626;"><i class="fa-solid fa-hourglass-half"></i> Requested</span>
          @elseif($order->status === 'refunded')
            <span style="color: #666;"><i class="fa-solid fa-check"></i> Refunded</span>
          @else
            <span style="color: #666;"><i class="fa-solid fa-times"></i> Declined</span>
          @endif
        </span>
      </div>
      @endif
      @if($order->delivered_at && $order->isRefundWindowOpen() && in_array($order->status, ['delivered','received']))
      <div class="order-detail">
        <span class="order-label">Refund Period</span>
        <span class="order-value" style="color: #dc3545; font-weight: 600;">
          <i class="fa-solid fa-clock"></i>
          <span class="refund-countdown" data-order-id="{{ $order->id }}" data-delivered="{{ $order->delivered_at->toIso8601String() }}">Calculating...</span>
        </span>
      </div>
      @endif

      <div class="order-actions">
        @if($user && $user->id === $order->fisherman_id)
          @if($order->status === 'pending_payment')
            <form class="d-inline" method="post" action="{{ route('orders.in-transit', $order) }}">
              @csrf
              <button class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-truck"></i> Mark In Transit
              </button>
            </form>
          @endif
          @if($order->status === 'in_transit')
            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#deliverModal{{ $order->id }}">
              <i class="fa-solid fa-box-open"></i> Mark Delivered
            </button>
          @endif
          @if($order->status === 'refund_requested')
            <form class="d-inline" method="post" action="{{ route('orders.refund.approve', $order) }}">
              @csrf
              <button class="btn btn-sm btn-success">
                <i class="fa-solid fa-check"></i> Approve Refund
              </button>
            </form>
            <form class="d-inline" method="post" action="{{ route('orders.refund.decline', $order) }}">
              @csrf
              <button class="btn btn-sm btn-danger">
                <i class="fa-solid fa-times"></i> Decline Refund
              </button>
            </form>
          @endif
        @elseif($user && $user->id === $order->vendor_id)
          @if(in_array($order->status, ['delivered','refund_declined']))
            <form class="d-inline" method="post" action="{{ route('orders.received', $order) }}">
              @csrf
              <button class="btn btn-sm btn-success">
                <i class="fa-solid fa-check-circle"></i> Confirm Received
              </button>
            </form>
          @endif
          @if(in_array($order->status, ['delivered','received']))
            @if($order->isRefundWindowOpen())
              <button class="btn btn-sm btn-outline-danger refund-button" data-order-id="{{ $order->id }}" data-bs-toggle="modal" data-bs-target="#refundModal{{ $order->id }}">
                <i class="fa-solid fa-exclamation-triangle"></i> Request Refund
              </button>
            @else
              <button class="btn btn-sm btn-outline-secondary" disabled title="Refund period closed (3 hours after delivery)">
                <i class="fa-solid fa-ban"></i> Refund Period Closed
              </button>
            @endif
          @endif
          @if($order->status === 'refund_declined')
            <span class="badge bg-danger" style="padding: 8px 16px; font-size: 13px;">
              <i class="fa-solid fa-circle-xmark"></i> Refund Declined by Fisherman
            </span>
          @endif
        @endif
      </div>
    </div>

        @if($order->status === 'in_transit')
        <!-- Deliver Modal -->
        <div class="modal fade" id="deliverModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Mark Delivered - Order #{{ $order->formatted_order_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="post" action="{{ route('orders.delivered', $order) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">Upload Proof Photo</label>
                    <input type="file" name="proof" accept="image/*" class="form-control" required>
                    <div class="form-text">Max 4MB. JPG/PNG only.</div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Notes (optional)</label>
                    <textarea name="notes" class="form-control" rows="2" maxlength="500" placeholder="Any delivery notes..."></textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        @endif
        <!-- Refund Modal -->
        @if($order->isRefundWindowOpen())
        <div class="modal fade" id="refundModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Request Refund - Order #{{ $order->formatted_order_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="post" action="{{ route('orders.refund.request', $order) }}" enctype="multipart/form-data">
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
  @endforeach

  <div class="mt-4">
    {{ $orders->links() }}
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function disableRefundButton(orderId) {
  const button = document.querySelector(`.refund-button[data-order-id="${orderId}"]`);
  if (!button) return;
  button.setAttribute('disabled', 'disabled');
  button.classList.remove('btn-outline-danger');
  button.classList.add('btn-outline-secondary');
  button.innerHTML = '<i class="fa-solid fa-ban"></i> Refund Period Closed';
  button.removeAttribute('data-bs-toggle');
  button.removeAttribute('data-bs-target');
  button.title = 'Refund period closed (3 hours after delivery)';
}

// Refund countdown timer (3-hour window from delivery)
function updateRefundCountdowns() {
  const countdowns = document.querySelectorAll('.refund-countdown');
  
  countdowns.forEach(countdown => {
    const deliveredAt = new Date(countdown.dataset.delivered);
    const orderId = countdown.dataset.orderId;
    const now = new Date();
    const hoursSinceDelivery = (now - deliveredAt) / (1000 * 60 * 60);
    
    if (hoursSinceDelivery >= 3) {
      countdown.textContent = 'Period Closed';
      countdown.style.color = '#6c757d';
      disableRefundButton(orderId);
      return;
    }
    
    const totalMinutes = 3 * 60;
    const elapsedMinutes = Math.floor((now - deliveredAt) / (1000 * 60));
    const remainingMinutes = totalMinutes - elapsedMinutes;
    
    if (remainingMinutes <= 0) {
      countdown.textContent = 'Period Closed';
      countdown.style.color = '#6c757d';
      disableRefundButton(orderId);
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
