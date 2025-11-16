<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Rental Management - SeaLedger</title>
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            color: white;
            font-size: 28px;
            font-weight: bold;
            font-family: "Koulen", sans-serif;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            text-decoration: none;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
        }

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .page-title {
            font-family: "Koulen", sans-serif;
            font-size: 48px;
            color: #1B5E88;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #666;
            font-size: 16px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-icon {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #1B5E88;
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        .rental-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .rental-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .rental-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .rental-id {
            font-size: 14px;
            color: #666;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-completed {
            background: #e2e3e5;
            color: #383d41;
        }

        .status-cancelled, .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .rental-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 16px;
            color: #1B5E88;
            font-weight: bold;
        }

        .rental-items {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }

        .items-title {
            font-size: 18px;
            color: #1B5E88;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-approve {
            background: #10b981;
            color: white;
        }

        .btn-approve:hover {
            background: #059669;
            transform: translateY(-2px);
        }

        .btn-reject {
            background: #ef4444;
            color: white;
        }

        .btn-reject:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .btn-disabled {
            background: #e5e7eb;
            color: #9ca3af;
            cursor: not-allowed;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #86efac;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .empty-state {
            background: white;
            padding: 60px 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .empty-state i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a class="nav-brand" href="{{ route('dashboard') }}">🐟 SeaLedger</a>
            <div>
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-link" style="background: none; border: none; cursor: pointer;">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">🔧 Rental Management</div>
            <div class="page-subtitle">Approve or reject equipment rental requests from fishermen</div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="color: #ffc107;">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="stat-number">{{ $stats['pending'] }}</div>
                <div class="stat-label">Pending Approval</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #0c5460;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="stat-number">{{ $stats['approved'] }}</div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #10b981;">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div class="stat-number">{{ $stats['active'] }}</div>
                <div class="stat-label">Active Rentals</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #6c757d;">
                    <i class="fa-solid fa-check-double"></i>
                </div>
                <div class="stat-number">{{ $stats['completed'] }}</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>

        <!-- Rentals List -->
        @if($rentals->count() > 0)
            @foreach($rentals as $rental)
                <div class="rental-card">
                    <div class="rental-header">
                        <div>
                            <div class="rental-id">Rental #{{ $rental->id }}</div>
                            <div style="font-size: 14px; color: #999; margin-top: 5px;">
                                Requested by: <strong>{{ $rental->user->username ?? $rental->user->email }}</strong>
                            </div>
                        </div>
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
                            <div class="detail-label">Deposit (30%)</div>
                            <div class="detail-value">₱{{ number_format($rental->deposit_amount, 2) }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Requested</div>
                            <div class="detail-value">{{ $rental->created_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    <div class="rental-items">
                        <h3 class="items-title">
                            <i class="fa-solid fa-toolbox"></i> Equipment Items
                        </h3>
                        @foreach($rental->rentalItems as $item)
                            <div class="item-row">
                                <div>
                                    <strong>{{ $item->product->name }}</strong>
                                    <span style="color: #666;">× {{ $item->quantity }}</span>
                                    <span style="color: #999; margin-left: 10px;">
                                        (₱{{ number_format($item->price_per_day, 2) }}/day)
                                    </span>
                                </div>
                                <div style="font-weight: bold;">₱{{ number_format($item->subtotal, 2) }}</div>
                            </div>
                        @endforeach
                    </div>

                    @if($rental->notes)
                        <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; margin-top: 15px;">
                            <strong style="color: #1B5E88;">
                                <i class="fa-solid fa-note-sticky"></i> Notes:
                            </strong> 
                            {{ $rental->notes }}
                        </div>
                    @endif

                    @if($rental->approved_by)
                        <div style="padding: 10px; background: #e7f3ff; border-radius: 8px; margin-top: 15px; font-size: 14px;">
                            <i class="fa-solid fa-user-shield"></i>
                            {{ $rental->status === 'approved' ? 'Approved' : 'Rejected' }} by 
                            <strong>{{ $rental->approvedBy->username ?? $rental->approvedBy->email }}</strong>
                            on {{ $rental->approved_at->format('M d, Y h:i A') }}
                        </div>
                    @endif

                    @if($rental->status === 'pending')
                        <div class="action-buttons">
                            <form action="{{ route('rentals.approve', $rental) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-approve" onclick="return confirm('Approve this rental request? Stock will be decremented.')">
                                    <i class="fa-solid fa-check"></i>
                                    Approve Rental
                                </button>
                            </form>
                            <form action="{{ route('rentals.reject', $rental) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-reject" onclick="return confirm('Reject this rental request?')">
                                    <i class="fa-solid fa-times"></i>
                                    Reject Rental
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fa-solid fa-toolbox"></i>
                <h2 style="color: #1B5E88; margin-bottom: 10px;">No Rental Requests</h2>
                <p style="color: #666;">There are currently no rental requests to manage.</p>
            </div>
        @endif
    </div>
</body>
</html>
