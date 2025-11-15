<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Marketplace Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Marketplace Orders</h1>
    <form class="d-flex" method="get">
      <select name="status" class="form-select" style="max-width:220px" onchange="this.form.submit()">
        <option value="">All statuses</option>
        <option value="pending_payment" {{ request('status')==='pending_payment' ? 'selected' : '' }}>Pending Payment</option>
        <option value="in_transit" {{ request('status')==='in_transit' ? 'selected' : '' }}>In Transit</option>
        <option value="delivered" {{ request('status')==='delivered' ? 'selected' : '' }}>Delivered</option>
        <option value="received" {{ request('status')==='received' ? 'selected' : '' }}>Received</option>
        <option value="refund_requested" {{ request('status')==='refund_requested' ? 'selected' : '' }}>Refund Requested</option>
        <option value="refunded" {{ request('status')==='refunded' ? 'selected' : '' }}>Refunded</option>
        <option value="refund_declined" {{ request('status')==='refund_declined' ? 'selected' : '' }}>Refund Declined</option>
      </select>
    </form>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
      <tr>
        <th>#</th>
        <th>Product</th>
        <th>Qty</th>
        <th>Total</th>
        <th>Status</th>
        <th>Proof</th>
        <th>Actions</th>
      </tr>
      </thead>
      <tbody>
      @foreach($orders as $order)
        <tr>
          <td>{{ $order->id }}</td>
          <td>{{ optional($order->listing->product ?? null)->name ?? 'Product #'.$order->listing_id }}</td>
          <td>{{ $order->quantity }}</td>
          <td>₱{{ number_format($order->total, 2) }}</td>
          <td>
            @php 
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
            @endphp
            <span class="badge bg-{{ $badge }} text-uppercase">{{ str_replace('_',' ', $order->status) }}</span>
          </td>
          <td>
            @if($order->proof_photo_path)
              <a href="{{ asset('storage/'.$order->proof_photo_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
            @else
              <span class="text-muted">—</span>
            @endif
            @if($order->refund_proof_path)
              <a href="{{ asset('storage/'.$order->refund_proof_path) }}" target="_blank" class="btn btn-sm btn-outline-danger ms-1">Refund Proof</a>
            @endif
          </td>
          <td>
            @php $user = auth()->user(); @endphp
            @if($user && $user->id === $order->vendor_id && $order->status==='pending_payment')
              <form class="d-inline" method="post" action="{{ route('marketplace.orders.intransit', $order) }}">
                @csrf
                <button class="btn btn-sm btn-info">Mark In Transit</button>
              </form>
            @endif
            @if($user && $user->id === $order->vendor_id && in_array($order->status, ['pending_payment','in_transit']))
              <form class="d-inline" method="post" action="{{ route('marketplace.orders.delivered', $order) }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="proof" accept="image/*" class="form-control form-control-sm d-inline-block" style="width: 210px;" required>
                <button class="btn btn-sm btn-warning">Mark Delivered</button>
              </form>
            @endif
            @if($user && $user->id === $order->buyer_id && $order->status==='delivered')
              <form class="d-inline" method="post" action="{{ route('marketplace.orders.received', $order) }}">
                @csrf
                <button class="btn btn-sm btn-success">Confirm Received</button>
              </form>
            @endif
            @if($user && $user->id === $order->buyer_id && in_array($order->status, ['delivered','received']))
              <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#refundModal{{ $order->id }}">Request Refund</button>
            @endif
            @if($user && $user->id === $order->vendor_id && $order->status==='refund_requested')
              <form class="d-inline" method="post" action="{{ route('marketplace.orders.refund.approve', $order) }}">
                @csrf
                <button class="btn btn-sm btn-outline-success">Approve Refund</button>
              </form>
              <form class="d-inline" method="post" action="{{ route('marketplace.orders.refund.decline', $order) }}">
                @csrf
                <button class="btn btn-sm btn-outline-danger">Decline Refund</button>
              </form>
            @endif
          </td>
        </tr>

        <!-- Refund Modal -->
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
      @endforeach
      </tbody>
    </table>
  </div>

  {{ $orders->links() }}

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
