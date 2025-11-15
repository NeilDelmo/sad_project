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
        <option value="delivered" {{ request('status')==='delivered' ? 'selected' : '' }}>Delivered</option>
        <option value="received" {{ request('status')==='received' ? 'selected' : '' }}>Received</option>
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
            @php $badge = $order->status==='pending_payment'?'secondary':($order->status==='delivered'?'warning':($order->status==='received'?'success':'secondary')); @endphp
            <span class="badge bg-{{ $badge }} text-uppercase">{{ $order->status }}</span>
          </td>
          <td>
            @if($order->proof_photo_path)
              <a href="{{ asset('storage/'.$order->proof_photo_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td>
            @php $user = auth()->user(); @endphp
            @if($user && $user->id === $order->vendor_id && $order->status==='pending_payment')
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
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

  {{ $orders->links() }}

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
