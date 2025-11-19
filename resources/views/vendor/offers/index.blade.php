<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Offers - Vendor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Koulen&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { font-family: 'Roboto', sans-serif; background: #f8f9fa; }
    .container-custom { max-width: 1200px; margin: 30px auto; }
    .page-title { font-family: 'Koulen', cursive; font-size: 34px; color: #1B5E88; }
    .offers-table { background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; }
    .table thead { background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%); color: white; }
    .table thead th { border: none; padding: 14px; }
    .table tbody td { vertical-align: middle; padding: 14px; }
    .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-countered { background: #dbeafe; color: #1e40af; }
    .status-accepted { background: #dcfce7; color: #166534; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    .status-auto_rejected { background: #ffeaa7; color: #d63031; }
    .status-withdrawn { background: #dfe6e9; color: #2d3436; }
    .status-closed { background: #b2bec3; color: #2d3436; }
    .status-expired { background: #e2e3e5; color: #383d41; }
    .btn-accept { background: #16a34a; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; }
    .btn-accept:hover { background: #15803d; }
    .btn-counter { background: #0075B5; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; }
    .btn-counter:hover { background: #1B5E88; }
    .btn-modify { background: #f59e0b; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; }
    .btn-modify:hover { background: #d97706; }
    .btn-withdraw { background: #6b7280; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; }
    .btn-withdraw:hover { background: #4b5563; }
    .bid-rank-badge { background: #0075B5; color: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; }
    .modify-form { background: #f8f9fa; padding: 15px; margin-top: 10px; border-radius: 8px; display: none; }
    .modify-form.show { display: block; }

    /* === VENDOR NAVBAR STYLES (COPY FROM WORKING PAGES) === */
    .navbar {
      background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
      padding: 15px 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin: 0;
    }
    .navbar .container-fluid {
      display: flex !important;
      justify-content: space-between !important;
      align-items: center !important;
      flex-wrap: nowrap !important;
      padding: 0;
    }
    .nav-brand {
      color: white;
      font-size: 28px;
      font-weight: bold;
      font-family: "Koulen", cursive;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
      flex-shrink: 0;
    }
    .nav-links {
      display: flex !important;
      gap: 10px !important;
      align-items: center;
      margin-left: auto;
      flex-shrink: 0;
      overflow-x: auto;
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
    /* === END NAVBAR STYLES === */
  </style>
</head>
<body>
  @include('vendor.partials.nav')
  <div class="container-custom">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div class="page-title">My Offers</div>
    </div>
    <div class="mb-3 d-flex gap-2">
      <a class="btn btn-sm {{ request('status') === 'pending' || !request('status') ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('vendor.offers.index', ['status' => 'pending']) }}">Pending</a>
      <a class="btn btn-sm {{ request('status') === 'countered' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('vendor.offers.index', ['status' => 'countered']) }}">Countered</a>
      <a class="btn btn-sm {{ request('status') === 'accepted' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('vendor.offers.index', ['status' => 'accepted']) }}">Accepted</a>
      <a class="btn btn-sm {{ request('status') === 'rejected' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('vendor.offers.index', ['status' => 'rejected']) }}">Rejected</a>
      <a class="btn btn-sm {{ request('status') === 'auto_rejected' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('vendor.offers.index', ['status' => 'auto_rejected']) }}">Auto-Rejected</a>
      <a class="btn btn-sm {{ request('status') === 'withdrawn' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('vendor.offers.index', ['status' => 'withdrawn']) }}">Withdrawn</a>
      <a class="btn btn-sm {{ request('status') === 'all' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('vendor.offers.index', ['status' => 'all']) }}">All</a>
    </div>
    <div class="offers-table">
      @if($offers->count() > 0)
      <table class="table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Fisherman</th>
            <th>Quantity</th>
            <th>Your Offer</th>
            <th>Counter Price</th>
            <th>Bid Rank</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($offers as $offer)
          <tr>
            <td>
              <strong>{{ $offer->product->name }}</strong><br>
              <small class="text-muted">{{ $offer->product->category->name ?? 'N/A' }}</small><br>
              @if($offer->status === 'pending')
                <small class="text-info">
                  <i class="fas fa-box"></i> {{ $offer->product->stock_quantity }} {{ $offer->product->unit_of_measure }} available
                </small>
              @endif
            </td>
            <td>{{ $offer->fisherman->username ?? $offer->fisherman->email }}</td>
            <td>{{ $offer->quantity }} {{ $offer->product->unit_of_measure }}</td>
            <td>₱{{ number_format($offer->offered_price, 2) }}</td>
            <td>
              @if($offer->fisherman_counter_price)
                ₱{{ number_format($offer->fisherman_counter_price, 2) }}
              @else - @endif
            </td>
            <td>
              @if($offer->status === 'pending')
                @php $bidRank = $offer->getBidRank(); @endphp
                <span class="bid-rank-badge">
                  #{{ $bidRank }}
                  @if($bidRank === 1)
                    <i class="fas fa-trophy"></i>
                  @endif
                </span>
                @if($offer->canBeFulfilled())
                  <br><small class="text-success"><i class="fas fa-check-circle"></i> Can fulfill</small>
                @else
                  <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Insufficient stock</small>
                @endif
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td><span class="status-badge status-{{ $offer->status }}">{{ ucfirst(str_replace('_', ' ', $offer->status)) }}</span></td>
            <td>{{ $offer->created_at->format('M d, Y') }}</td>
            <td>
              @if($offer->status === 'pending' && $offer->canModify())
                <button type="button" class="btn btn-modify btn-sm mb-1" onclick="toggleModifyForm('modify-{{ $offer->id }}')">
                  <i class="fas fa-edit"></i> Modify
                </button>
                <form method="POST" action="{{ route('offers.withdraw', $offer) }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-withdraw btn-sm mb-1" onclick="return confirm('Withdraw this bid?')">
                    <i class="fas fa-undo"></i> Withdraw
                  </button>
                </form>
              @elseif($offer->status === 'countered')
                <form method="POST" action="{{ route('vendor.offers.accept-counter', $offer->id) }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-accept btn-sm" onclick="return confirm('Accept this counter offer?')">
                    <i class="fa-solid fa-check"></i> Accept Counter
                  </button>
                </form>
              @elseif($offer->status === 'auto_rejected')
                <small class="text-muted"><i class="fas fa-robot"></i> Auto-rejected</small>
              @elseif($offer->status === 'withdrawn')
                <small class="text-muted"><i class="fas fa-undo"></i> Withdrawn</small>
              @else
                <span class="text-muted">No actions</span>
              @endif
            </td>
          </tr>
          @if($offer->status === 'pending' && $offer->canModify())
          <tr id="modify-{{ $offer->id }}" class="modify-form">
            <td colspan="9">
              <form method="POST" action="{{ route('offers.modify', $offer) }}">
                @csrf
                <div class="row g-3">
                  <div class="col-md-3">
                    <label class="form-label fw-bold">New Price (per {{ $offer->product->unit_of_measure }})</label>
                    <input type="number" name="offered_price" class="form-control" step="0.01" min="0.01" 
                           value="{{ $offer->offered_price }}" required>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label fw-bold">New Quantity</label>
                    <input type="number" name="quantity" class="form-control" min="1" 
                           max="{{ $offer->product->available_quantity }}"
                           value="{{ $offer->quantity }}" required>
                    <small class="text-muted">Max: {{ $offer->product->available_quantity }} kg</small>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-bold">Reason (Optional)</label>
                    <input type="text" name="reason" class="form-control" placeholder="Why modify this bid?">
                  </div>
                  <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                      <i class="fas fa-save"></i> Save
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="toggleModifyForm('modify-{{ $offer->id }}')">
                      Cancel
                    </button>
                  </div>
                </div>
              </form>
            </td>
          </tr>
          @endif
          @endforeach
        </tbody>
      </table>
      <div class="d-flex justify-content-center p-3">
        {{ $offers->links() }}
      </div>
      @else
        <div class="p-5 text-center text-muted">No offers yet.</div>
      @endif
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function toggleModifyForm(id) {
      const form = document.getElementById(id);
      form.classList.toggle('show');
    }
  </script>
  @include('partials.toast-notifications')
</body>
</html>
