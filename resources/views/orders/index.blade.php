<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
  </style>
</head>
<body>
@php $user = Auth::user(); @endphp
@if($user && $user->user_type === 'fisherman')
  @include('fisherman.partials.nav')
  @include('partials.toast-notifications')
@elseif($user && $user->user_type === 'vendor')
  @include('vendor.partials.nav')
@endif
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">My Orders</h1>
    <form class="d-flex" method="get">
      <select name="status" class="form-select me-2" onchange="this.form.submit()">
        <option value="">All statuses</option>
        <option value="pending_payment" {{ request('status')==='pending_payment' ? 'selected' : '' }}>Pending Payment</option>
        <option value="in_transit" {{ request('status')==='in_transit' ? 'selected' : '' }}>In Transit</option>
        <option value="delivered" {{ request('status')==='delivered' ? 'selected' : '' }}>Delivered</option>
        <option value="received" {{ request('status')==='received' ? 'selected' : '' }}>Received</option>
        <option value="refund_requested" {{ request('status')==='refund_requested' ? 'selected' : '' }}>Refund Requested</option>
        <option value="refunded" {{ request('status')==='refunded' ? 'selected' : '' }}>Refunded</option>
        <option value="refund_declined" {{ request('status')==='refund_declined' ? 'selected' : '' }}>Refund Declined</option>
      </select>
      <noscript><button class="btn btn-outline-secondary" type="submit">Filter</button></noscript>
    </form>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

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
          <span class="order-id"><i class="fa-solid fa-receipt"></i> Order #{{ $order->id }}</span>
          <div style="font-size: 14px; color: #666; margin-top: 5px;">
            {{ optional($order->product)->name ?? 'Product #'.$order->product_id }}
          </div>
        </div>
        <span class="status-badge status-{{ $order->status }}">
          <i class="fa-solid {{ $statusIcon }}"></i> {{ str_replace('_',' ', ucfirst($order->status)) }}
        </span>
      </div>

      <div class="order-body">
        <div class="order-detail">
          <span class="order-label">Quantity</span>
          <span class="order-value">{{ $order->quantity }} kg</span>
        </div>
        <div class="order-detail">
          <span class="order-label">Total Amount</span>
          <span class="order-value" style="color: #16a34a; font-weight: 700;">₱{{ number_format($order->total, 2) }}</span>
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
      </div>

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
          @if(in_array($order->status, ['pending_payment','in_transit']))
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
            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#refundModal{{ $order->id }}">
              <i class="fa-solid fa-exclamation-triangle"></i> Request Refund
            </button>
          @endif
        @endif
      </div>
    </div>

        <!-- Deliver Modal -->
        <div class="modal fade" id="deliverModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Mark Delivered - Order #{{ $order->id }}</h5>
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
        <!-- Refund Modal -->
        <div class="modal fade" id="refundModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Request Refund - Order #{{ $order->id }}</h5>
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
  @endforeach

  <div class="mt-4">
    {{ $orders->links() }}
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
