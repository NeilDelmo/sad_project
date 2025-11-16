<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rentals - SeaLedger</title>
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #1B5E88;
            margin-bottom: 10px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 6px;
            transition: background 0.2s;
        }

        .back-link:hover {
            background: rgba(255,255,255,0.3);
        }

        .rental-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .rental-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .rental-id {
            font-size: 18px;
            font-weight: bold;
            color: #1B5E88;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-completed {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-cancelled, .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .rental-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-item {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .detail-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 16px;
            font-weight: bold;
            color: #1B5E88;
        }

        .rental-items {
            margin-bottom: 20px;
        }

        .rental-items h3 {
            color: #1B5E88;
            margin-bottom: 10px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: #f8f9fa;
            margin-bottom: 8px;
            border-radius: 6px;
        }

        .cancel-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .cancel-btn:hover {
            background: #dc2626;
        }

        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #86efac;
        }

        .empty-state {
            background: white;
            padding: 60px 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .empty-state h2 {
            color: #1B5E88;
            margin-bottom: 10px;
        }

        .browse-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: #1B5E88;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .browse-btn:hover {
            background: #0075B5;
        }

        /* Circular progress loader */
        .progress-container {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #fff9e6;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
            margin-top: 15px;
        }

        .progress-circle {
            width: 60px;
            height: 60px;
            position: relative;
            flex-shrink: 0;
        }

        .progress-svg {
            transform: rotate(-90deg);
            width: 100%;
            height: 100%;
        }

        .progress-bg {
            fill: none;
            stroke: #f0f0f0;
            stroke-width: 6;
        }

        .progress-spinner {
            fill: none;
            stroke: #ffc107;
            stroke-width: 6;
            stroke-linecap: round;
            stroke-dasharray: 157;
            animation: spin 1.5s linear infinite;
            transform-origin: center;
        }

        @keyframes spin {
            0% {
                stroke-dashoffset: 157;
            }
            50% {
                stroke-dashoffset: 39.25;
            }
            100% {
                stroke-dashoffset: 157;
            }
        }

        .progress-info {
            flex-grow: 1;
        }

        .progress-title {
            font-size: 16px;
            font-weight: bold;
            color: #856404;
            margin-bottom: 5px;
        }

        .progress-text {
            font-size: 14px;
            color: #666;
        }

        .waiting-time {
            font-weight: bold;
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('rentals.index') }}" class="back-link">← Back to Catalog</a>

        <div class="header">
            <h1>📋 My Rentals</h1>
            <p>Track your gear rental requests and active rentals</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($rentals->count() > 0)
            @foreach($rentals as $rental)
                <div class="rental-card">
                    <div class="rental-header">
                        <div class="rental-id">Rental #{{ $rental->id }}</div>
                        <span class="status-badge status-{{ $rental->status }}">
                            {{ ucfirst($rental->status) }}
                        </span>
                    </div>

                    <div class="rental-details">
                        <div class="detail-item">
                            <div class="detail-label">Rental Date</div>
                            <div class="detail-value">{{ $rental->rental_date->format('M d, Y') }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Return Date</div>
                            <div class="detail-value">{{ $rental->return_date->format('M d, Y') }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Duration</div>
                            <div class="detail-value">{{ $rental->duration_in_days }} days</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Total Price</div>
                            <div class="detail-value">₱{{ number_format($rental->total_price, 2) }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Deposit</div>
                            <div class="detail-value">₱{{ number_format($rental->deposit_amount, 2) }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Requested</div>
                            <div class="detail-value">{{ $rental->created_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    <div class="rental-items">
                        <h3>Items</h3>
                        @foreach($rental->rentalItems as $item)
                            <div class="item-row">
                                <div>
                                    <strong>{{ $item->product->name }}</strong>
                                    <span style="color: #666;">× {{ $item->quantity }}</span>
                                </div>
                                <div>₱{{ number_format($item->subtotal, 2) }}</div>
                            </div>
                        @endforeach
                    </div>

                    @if($rental->notes)
                        <div style="padding: 10px; background: #f8f9fa; border-radius: 6px; margin-bottom: 15px;">
                            <strong style="color: #1B5E88;">Notes:</strong> {{ $rental->notes }}
                        </div>
                    @endif

                    @if($rental->status === 'pending')
                        <!-- Circular progress indicator for pending rentals -->
                        <div class="progress-container">
                            <div class="progress-circle">
                                <svg class="progress-svg" viewBox="0 0 60 60">
                                    <circle class="progress-bg" cx="30" cy="30" r="25"></circle>
                                    <circle class="progress-spinner" cx="30" cy="30" r="25"></circle>
                                </svg>
                            </div>
                            <div class="progress-info">
                                <div class="progress-title">
                                    <i class="fa-solid fa-clock"></i> Awaiting Admin Approval
                                </div>
                                <div class="progress-text">
                                    Waiting for <span class="waiting-time">{{ $rental->created_at->diffForHumans() }}</span>
                                    <br>
                                    <small style="color: #999;">Your rental request is being reviewed by the administrator.</small>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('rentals.cancel', $rental) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this rental request?')">
                            @csrf
                            <button type="submit" class="cancel-btn">Cancel Request</button>
                        </form>
                    @endif

                    @if($rental->isOverdue())
                        <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-top: 10px;">
                            ⚠️ This rental is overdue! Please return the items as soon as possible.
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <h2>No Rentals Yet</h2>
                <p>You haven't made any rental requests yet.</p>
                <a href="{{ route('rentals.index') }}" class="browse-btn">Browse Gear Catalog</a>
            </div>
        @endif
    </div>
</body>
</html>
