<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap5/css/bootstrap.min.css') }}" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>SeaLedger - Vendor Inventory</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');

        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
        }

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

        .container-main {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
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
            font-size: 42px;
            color: #1B5E88;
            margin: 0;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .inventory-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .inventory-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .inventory-table thead {
            background: #f8f9fa;
        }

        .inventory-table th {
            padding: 16px 24px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e9ecef;
        }

        .inventory-table td {
            padding: 20px 24px;
            border-bottom: 1px solid #f1f3f5;
        }

        .inventory-table tbody tr:hover {
            background: #f8f9fa;
        }

        .product-name {
            font-weight: 600;
            color: #1B5E88;
            font-size: 15px;
        }

        .product-category {
            color: #6c757d;
            font-size: 13px;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-secondary {
            background: #e2e3e5;
            color: #383d41;
        }

        .action-link {
            position: relative;
            color: #0d5c85;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px 8px 12px;
            border-radius: 9999px;
            background: linear-gradient(135deg,#E7FAFE 0%, #d4eef8 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            transition: all .25s ease;
            margin: 0 10px 8px 0;
        }

        .action-link:hover {
            color: #094766;
            background: linear-gradient(135deg,#dff4fa 0%, #c7e6f2 100%);
            box-shadow: 0 4px 10px rgba(0,0,0,0.12);
            text-decoration: none;
            transform: translateY(-2px);
        }

        .action-link .icon-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #0075B5;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
            flex-shrink: 0;
            transition: background .25s ease, transform .25s ease;
        }

        .action-link:hover .icon-circle {
            background: #1B5E88;
            transform: scale(1.05);
        }

        .action-link.success {
            background: linear-gradient(135deg,#d8f9e5 0%, #c2f1d4 100%);
            color: #166534;
        }
        .action-link.success .icon-circle { background: #16a34a; }
        .action-link.success:hover { background: linear-gradient(135deg,#c9f4d9 0%, #afecc4 100%); color:#14532d; }
        .action-link.success:hover .icon-circle { background:#15803d; }

        .action-link.track { background: linear-gradient(135deg,#fff3cd 0%, #ffe69c 100%); color:#7c5e00; }
        .action-link.track .icon-circle { background:#f0ad4e; }
        .action-link.track:hover { background: linear-gradient(135deg,#ffe8a1 0%, #ffd86a 100%); color:#5e4600; }
        .action-link.track:hover .icon-circle { background:#e69522; }

        /* details variant uses base styling */

        .empty-state {
            background: white;
            padding: 60px 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .empty-icon {
            background: #e7f5ff;
            border-radius: 50%;
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .empty-icon i {
            font-size: 48px;
            color: #0075B5;
        }

        .empty-title {
            font-size: 24px;
            font-weight: bold;
            color: #1B5E88;
            margin-bottom: 10px;
        }

        .empty-text {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .btn-primary-custom {
            background: #0075B5;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            background: #1B5E88;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,117,181,0.3);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    @include('vendor.partials.nav')

    <div class="container-main">
        <div class="page-header">
            <h1 class="page-title">Vendor Inventory</h1>
        </div>

        @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if($inventory->count() > 0)
        <div class="inventory-table">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Purchase Price</th>
                        <th>Status</th>
                        <th>Purchased</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventory as $item)
                    <tr>
                        <td>
                            <div class="product-name">{{ $item->product->name }}</div>
                            <div class="product-category">{{ $item->product->category->name }}</div>
                        </td>
                        <td>{{ $item->quantity }} kg</td>
                        <td>₱{{ number_format($item->purchase_price, 2) }}</td>
                        <td>
                            <span class="badge 
                                @if($item->status === 'in_stock') badge-success
                                @elseif($item->status === 'listed') badge-info
                                @elseif($item->status === 'pending_delivery') badge-warning
                                @elseif($item->status === 'refunded') badge-secondary
                                @else badge-secondary
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                            </span>
                            @if($item->order)
                                @php
                                    $orderStatus = $item->order->status;
                                    $statusDisplay = ucfirst(str_replace('_', ' ', $orderStatus));
                                    $statusColor = match($orderStatus) {
                                        'pending_payment' => '#6c757d',
                                        'in_transit' => '#0dcaf0',
                                        'delivered' => '#ffc107',
                                        'received' => '#198754',
                                        'refund_requested' => '#dc3545',
                                        'refunded' => '#6c757d',
                                        'refund_declined' => '#dc3545',
                                        default => '#6c757d'
                                    };
                                    $statusIcon = match($orderStatus) {
                                        'pending_payment' => 'fa-clock',
                                        'in_transit' => 'fa-truck',
                                        'delivered' => 'fa-box',
                                        'received' => 'fa-check-circle',
                                        'refund_requested' => 'fa-exclamation-triangle',
                                        'refunded' => 'fa-undo',
                                        'refund_declined' => 'fa-times-circle',
                                        default => 'fa-info-circle'
                                    };
                                @endphp
                                <div style="font-size: 11px; color: {{ $statusColor }}; margin-top: 4px;">
                                    <i class="fa-solid {{ $statusIcon }}"></i> Order #{{ $item->order->id }} - {{ $statusDisplay }}
                                </div>
                            @endif
                        </td>
                        <td style="color: #6c757d; font-size: 14px;">{{ $item->purchased_at->diffForHumans() }}</td>
                        <td>
                            @if($item->status === 'pending_delivery')
                                {{-- Item not yet received - show order tracking --}}
                                @if($item->order)
                                <a href="{{ route('orders.index') }}?order_id={{ $item->order->id }}" class="action-link track"><span class="icon-circle"><i class="fa-solid fa-truck"></i></span> <span>Track Delivery</span></a>
                                @else
                                <a href="{{ route('vendor.inventory.show', $item) }}" class="action-link details"><span class="icon-circle"><i class="fa-solid fa-eye"></i></span> <span>View Details</span></a>
                                @endif
                                @if($item->order && in_array($item->order->status, ['pending_payment', 'in_transit']))
                                    <span style="color: #856404; font-size: 13px; margin-left: 10px;"><i class="fa-solid fa-hourglass-half"></i> Awaiting Delivery</span>
                                @elseif($item->order && $item->order->status === 'delivered')
                                    <span style="color: #0dcaf0; font-size: 13px; margin-left: 10px;"><i class="fa-solid fa-box"></i> Delivered - Pending Confirmation</span>
                                @elseif($item->order && $item->order->status === 'refund_requested')
                                    <span style="color: #dc3545; font-size: 13px; margin-left: 10px;"><i class="fa-solid fa-exclamation-triangle"></i> Refund Requested</span>
                                @elseif($item->order && $item->order->status === 'refund_declined')
                                    <span style="color: #dc3545; font-size: 13px; margin-left: 10px;"><i class="fa-solid fa-times-circle"></i> Refund Declined</span>
                                @endif
                            @elseif($item->status === 'refunded')
                                {{-- Item was refunded back to fisherman --}}
                                <a href="{{ route('vendor.inventory.show', $item) }}" class="action-link details"><span class="icon-circle"><i class="fa-solid fa-eye"></i></span> <span>View Details</span></a>
                                <span style="color: #6c757d; font-size: 13px; margin-left: 10px;"><i class="fa-solid fa-undo"></i> Refunded</span>
                            @else
                                {{-- Item received (in_stock or listed) - show details only --}}
                                <a href="{{ route('vendor.inventory.show', $item) }}" class="action-link details"><span class="icon-circle"><i class="fa-solid fa-eye"></i></span> <span>View Details</span></a>
                                @if($item->status === 'in_stock')
                                <a href="{{ route('vendor.inventory.create-listing', $item) }}" class="action-link success"><span class="icon-circle"><i class="fa-solid fa-bullhorn"></i></span> <span>List on Marketplace</span></a>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 30px;">
            {{ $inventory->links() }}
        </div>
        @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <h3 class="empty-title">No Inventory Items Yet</h3>
            <p class="empty-text">Purchase products from fishermen to start building your inventory and selling on the marketplace.</p>
            <a href="{{ route('vendor.dashboard') }}" class="btn-primary-custom">
                <i class="fa-solid fa-gauge-high"></i>
                Go to Dashboard
            </a>
        </div>
        @endif
    </div>

@include('partials.message-notification')

</body>
</html>