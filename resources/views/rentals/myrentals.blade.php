<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>My Rentals - SeaLedger</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
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

        /* Navbar */
        .navbar { background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%); padding: 15px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-brand { color:#fff; font-size:28px; font-weight:bold; text-decoration:none; display: flex; align-items: center; gap: 10px; }
        .nav-logo { height: 40px; width: auto; }
        .nav-link { color: rgba(255,255,255,0.9); text-decoration:none; padding:10px 16px; border-radius:8px; transition: all .2s; }
        .nav-link:hover { color:#fff; background: rgba(255,255,255,0.15); }
        .nav-link.active { background: rgba(255,255,255,0.25); color:#fff; font-weight:600; }

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
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="nav-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="SeaLedger Logo" class="nav-logo">
                SeaLedger
            </a>
            <div class="d-flex align-items-center" style="gap:8px;">
                <a href="{{ route('rentals.index') }}" class="nav-link"><i class="fa-solid fa-toolbox"></i> Gear Rentals</a>
                <a href="{{ route('rentals.myrentals') }}" class="nav-link active"><i class="fa-solid fa-clipboard-list"></i> My Rentals</a>
                <a href="{{ route('dashboard') }}" class="nav-link"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container">

        <div class="header">
            <h1>📋 My Rentals</h1>
            <p>Track your gear rental requests and active rentals</p>
        </div>

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
                            <div class="detail-value">
                                ₱{{ number_format($rental->total_price, 2) }}
                                @if($rental->discount_amount > 0)
                                    <div style="font-size: 12px; color: #16a34a;">
                                        <i class="fa-solid fa-tag"></i> Saved ₱{{ number_format($rental->discount_amount, 2) }}
                                    </div>
                                @endif
                            </div>
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

                    @if($rental->status === 'approved' && $rental->pickup_otp)
                        <div style="padding: 14px; background: #e7f3ff; border-left: 4px solid #1e40af; border-radius: 6px; margin-bottom: 15px;">
                            <div style="font-weight: 700; color: #1e40af; margin-bottom: 6px;">
                                <i class="fa-solid fa-key"></i> Pickup OTP
                            </div>
                            <div style="font-size: 20px; letter-spacing: 4px; font-weight: 800; color: #1B5E88;">{{ $rental->pickup_otp }}</div>
                            <div style="font-size: 12px; color: #555; margin-top: 6px;">
                                Show this code to the admin when picking up your gear.
                                @if($rental->expires_at)
                                    <br>Expires: {{ $rental->expires_at->format('M d, Y h:i A') }}
                                @endif
                            </div>
                        </div>
                    @endif

                    @if(in_array($rental->status, ['approved','active','completed']))
                        <div class="mt-2">
                            <a href="{{ route('rentals.report.form', $rental) }}" class="browse-btn" style="background:#0ea5e9;">
                                <i class="fa-solid fa-flag"></i> Report a Problem
                            </a>
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
    @include('partials.toast-notifications')
    <script data-collect-dnt="true" async src="https://scripts.simpleanalyticscdn.com/latest.js"></script>
</body>
</html>
