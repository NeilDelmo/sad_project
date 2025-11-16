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
                            {{ in_array($rental->status, ['approved', 'active', 'completed']) ? 'Approved' : 'Rejected' }} by 
                            <strong>{{ $rental->approvedBy->username ?? $rental->approvedBy->email }}</strong>
                            on {{ $rental->approved_at->format('M d, Y h:i A') }}
                        </div>
                    @endif

                    @if($rental->picked_up_at)
                        <div style="padding: 10px; background: #d4edda; border-radius: 8px; margin-top: 10px; font-size: 14px;">
                            <i class="fa-solid fa-box"></i>
                            Equipment picked up on {{ $rental->picked_up_at->format('M d, Y h:i A') }}
                        </div>
                    @endif

                    @if($rental->returned_at)
                        <div style="padding: 10px; background: #e2e3e5; border-radius: 8px; margin-top: 10px; font-size: 14px;">
                            <i class="fa-solid fa-check-circle"></i>
                            Equipment returned on {{ $rental->returned_at->format('M d, Y h:i A') }}
                            @if($rental->late_fee > 0)
                                <br>
                                <strong style="color: #dc3545;">Late Fee: ₱{{ number_format($rental->late_fee, 2) }}</strong>
                            @endif
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

                    @if($rental->status === 'approved')
                        <div class="action-buttons">
                            <button type="button" class="btn btn-approve" onclick="showOtpModal({{ $rental->id }}, '{{ $rental->pickup_otp }}')">
                                <i class="fa-solid fa-play"></i>
                                Mark as Picked Up
                            </button>
                            @if($rental->pickup_otp)
                                <span style="background: #d1ecf1; color: #0c5460; padding: 10px 15px; border-radius: 8px; font-size: 14px;">
                                    <i class="fa-solid fa-key"></i> OTP: {{ $rental->pickup_otp }}
                                </span>
                            @endif
                        </div>
                    @endif

                    @if($rental->status === 'active')
                        <div class="action-buttons">
                            <button type="button" class="btn btn-approve" onclick="showReturnModal({{ $rental->id }}, {{ $rental->rentalItems->toJson() }})">
                                <i class="fa-solid fa-rotate-left"></i>
                                Process Return
                            </button>
                            @if($rental->isOverdue())
                                <span style="background: #fee2e2; color: #991b1b; padding: 10px 15px; border-radius: 8px; font-size: 14px;">
                                    <i class="fa-solid fa-exclamation-triangle"></i> OVERDUE
                                </span>
                            @endif
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

    <!-- Return Equipment Modal -->
    <div id="returnModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; padding: 30px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;">
            <h2 style="color: #1B5E88; margin-bottom: 20px;">
                <i class="fa-solid fa-rotate-left"></i> Process Equipment Return
            </h2>
            
            <form id="returnForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="returnItems"></div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 2px solid #f0f0f0;">
                    <button type="submit" class="btn btn-approve">
                        <i class="fa-solid fa-check"></i> Complete Return
                    </button>
                    <button type="button" class="btn btn-reject" onclick="closeReturnModal()">
                        <i class="fa-solid fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- OTP Verification Modal -->
    <div id="otpModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; padding: 30px; max-width: 400px; width: 90%;">
            <h2 style="color: #1B5E88; margin-bottom: 20px;">
                <i class="fa-solid fa-key"></i> Verify Pickup OTP
            </h2>
            
            <form id="otpForm" method="POST">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px;">Enter 6-digit OTP:</label>
                    <input type="text" name="otp" maxlength="6" pattern="[0-9]{6}" required
                        style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 24px; text-align: center; letter-spacing: 8px;">
                    <p style="margin-top: 8px; color: #666; font-size: 14px;">
                        <i class="fa-solid fa-info-circle"></i> Ask the fisherman for their OTP
                    </p>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-approve">
                        <i class="fa-solid fa-check"></i> Verify & Activate
                    </button>
                    <button type="button" class="btn btn-reject" onclick="closeOtpModal()">
                        <i class="fa-solid fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showOtpModal(rentalId, expectedOtp) {
            const modal = document.getElementById('otpModal');
            const form = document.getElementById('otpForm');
            form.action = `/rentals/${rentalId}/activate`;
            modal.style.display = 'flex';
        }
        
        function closeOtpModal() {
            document.getElementById('otpModal').style.display = 'none';
        }

        function showReturnModal(rentalId, items) {
            const modal = document.getElementById('returnModal');
            const form = document.getElementById('returnForm');
            const itemsContainer = document.getElementById('returnItems');
            
            // Set form action
            form.action = `/rentals/${rentalId}/return`;
            
            // Build items HTML (per-quantity return)
            let html = '';
            items.forEach(item => {
                const iid = item.id;
                const qty = item.quantity;
                html += `
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 15px;">
                        <strong style="color: #1B5E88;">${item.product.name}</strong>
                        <span style="color: #666; margin-left: 10px;">× ${qty}</span>
                        <input type="hidden" name="items[${iid}][rental_item_id]" value="${iid}">
                        <div style="margin-top: 10px; display:grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap:10px; align-items:end;">
                            <div>
                                <label style="display:block; font-weight:bold;">Good</label>
                                <input type="number" name="items[${iid}][good]" min="0" max="${qty}" value="${qty}" oninput="updateCounts(${iid}, ${qty})" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
                            </div>
                            <div>
                                <label style="display:block; font-weight:bold;">Fair</label>
                                <input type="number" name="items[${iid}][fair]" min="0" max="${qty}" value="0" oninput="updateCounts(${iid}, ${qty})" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
                            </div>
                            <div>
                                <label style="display:block; font-weight:bold;">Damaged</label>
                                <input type="number" name="items[${iid}][damaged]" min="0" max="${qty}" value="0" oninput="updateCounts(${iid}, ${qty})" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
                            </div>
                            <div>
                                <label style="display:block; font-weight:bold;">Lost</label>
                                <input type="number" name="items[${iid}][lost]" min="0" max="${qty}" value="0" oninput="updateCounts(${iid}, ${qty})" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
                            </div>
                        </div>
                        <div style="margin-top:8px; color:#666; font-size:13px;">
                            <span id="sum-note-${iid}">Total: ${qty} / ${qty}</span>
                        </div>
                        <div id="photo-upload-${iid}" style="display: none; margin-top: 10px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #dc3545;">
                                <i class="fa fa-camera"></i> Upload Photos (Required if Damaged/Lost > 0, max 5):
                            </label>
                            <input type="file" name="items[${iid}][photos][]" accept="image/jpeg,image/jpg,image/png,image/webp" multiple
                                style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                            <p style="margin-top: 5px; color: #666; font-size: 12px;">
                                <i class="fa fa-info-circle"></i> Max 5 photos, 5MB each. JPEG, PNG, WebP only.
                            </p>
                        </div>
                    </div>
                `;
            });
            
            itemsContainer.innerHTML = html;
            modal.style.display = 'flex';

                function updateCounts(itemId, maxQty) {
                    const g = parseInt(document.querySelector(`[name="items[${itemId}][good]"]`).value || '0');
                    const f = parseInt(document.querySelector(`[name="items[${itemId}][fair]"]`).value || '0');
                    const d = parseInt(document.querySelector(`[name="items[${itemId}][damaged]"]`).value || '0');
                    const l = parseInt(document.querySelector(`[name="items[${itemId}][lost]"]`).value || '0');
                    const sum = g + f + d + l;
                    const note = document.getElementById(`sum-note-${itemId}`);
                    note.textContent = `Total: ${sum} / ${maxQty}`;
                    note.style.color = (sum === maxQty) ? '#10b981' : '#dc2626';

                    const photoDiv = document.getElementById(`photo-upload-${itemId}`);
                    const photoInput = photoDiv.querySelector('input[type="file"]');
                    if ((d + l) > 0) {
                        photoDiv.style.display = 'block';
                        photoInput.required = true;
                    } else {
                        photoDiv.style.display = 'none';
                        photoInput.required = false;
                        photoInput.value = '';
                    }
                }
        }
        
        function closeReturnModal() {
            document.getElementById('returnModal').style.display = 'none';
        }
    </script>
</body>
</html>
