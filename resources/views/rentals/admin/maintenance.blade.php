<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Maintenance - SeaLedger</title>
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

        .section-title {
            font-size: 28px;
            font-weight: bold;
            color: #1B5E88;
            margin: 30px 0 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #0075B5;
        }

        .equipment-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
            border-left: 5px solid #ffc107;
        }

        .equipment-card.retired {
            border-left-color: #6c757d;
            opacity: 0.8;
        }

        .equipment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .equipment-name {
            font-size: 24px;
            font-weight: bold;
            color: #1B5E88;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-maintenance {
            background: #fff3cd;
            color: #856404;
        }

        .status-retired {
            background: #e2e3e5;
            color: #383d41;
        }

        .damage-history {
            background: #fee2e2;
            border-left: 4px solid #dc3545;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }

        .damage-item {
            padding: 10px 0;
            border-bottom: 1px solid #fca5a5;
        }

        .damage-item:last-child {
            border-bottom: none;
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

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
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

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a class="nav-brand" href="{{ route('dashboard') }}">🐟 SeaLedger</a>
            <div>
                <a href="{{ route('rentals.admin.index') }}" class="nav-link">
                    <i class="fa-solid fa-toolbox"></i> Rentals
                </a>
                <a href="{{ route('rentals.admin.maintenance') }}" class="nav-link">
                    <i class="fa-solid fa-wrench"></i> Maintenance
                </a>
                <a href="{{ route('rentals.admin.reports') }}" class="nav-link">
                    <i class="fa-solid fa-flag"></i> Reports
                </a>
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
            <div class="page-title">🔧 Equipment Maintenance</div>
            <div class="page-subtitle">Manage damaged equipment and track repair status</div>
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

        <!-- Equipment Needing Maintenance -->
        <h2 class="section-title">
            <i class="fa-solid fa-wrench"></i> Equipment Needing Repair
        </h2>

        @if($maintenanceEquipment->count() > 0)
            @foreach($maintenanceEquipment as $equipment)
                <div class="equipment-card">
                    <div class="equipment-header">
                        <div class="equipment-name">
                            <i class="fa-solid fa-toolbox"></i> {{ $equipment->name }}
                        </div>
                        <span class="status-badge status-maintenance">
                            <i class="fa-solid fa-tools"></i> In Maintenance
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                        <div>
                            <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 5px;">Rental Price</div>
                            <div style="font-size: 18px; color: #1B5E88; font-weight: bold;">₱{{ number_format($equipment->rental_price_per_day, 2) }}/day</div>
                        </div>
                        <div>
                            <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 5px;">Current Stock</div>
                            <div style="font-size: 18px; color: #1B5E88; font-weight: bold;">{{ $equipment->rental_stock }}</div>
                        </div>
                        <div>
                            <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 5px;">In Maintenance</div>
                            <div style="font-size: 18px; color: #d97706; font-weight: bold;">{{ $equipment->maintenance_count ?? 0 }}</div>
                        </div>
                    </div>

                    @if($equipment->rentalItems->where('condition_in', 'damaged')->count() > 0)
                        <div class="damage-history">
                            <strong style="color: #dc3545; font-size: 16px;">
                                <i class="fa-solid fa-triangle-exclamation"></i> Damage History
                            </strong>
                            @foreach($equipment->rentalItems->take(5) as $item)
                                <div class="damage-item">
                                    <div style="font-size: 14px;">
                                        <strong>Rental #{{ $item->rental_id }}</strong> - 
                                        Returned by <strong>{{ $item->rental->user->username ?? $item->rental->user->email }}</strong>
                                    </div>
                                    <div style="font-size: 12px; color: #666; margin-top: 5px;">
                                        {{ $item->updated_at->format('M d, Y h:i A') }} ({{ $item->updated_at->diffForHumans() }})
                                    </div>
                                    @if(!is_null($item->good_count) || !is_null($item->fair_count) || !is_null($item->damaged_count) || !is_null($item->lost_count))
                                        <div style="font-size: 12px; color:#1B5E88; margin-top: 5px;">
                                            Good: <strong>{{ $item->good_count ?? 0 }}</strong>,
                                            Fair: <strong>{{ $item->fair_count ?? 0 }}</strong>,
                                            Damaged: <strong>{{ $item->damaged_count ?? 0 }}</strong>,
                                            Lost: <strong>{{ $item->lost_count ?? 0 }}</strong>
                                        </div>
                                    @endif
                                    @if($item->condition_in_photo)
                                        <div style="margin-top: 10px;">
                                            <img src="{{ asset('storage/' . $item->condition_in_photo) }}" alt="Damage photo" style="max-width: 240px; border-radius: 8px; border: 1px solid #eee; cursor: zoom-in;" data-src="{{ asset('storage/' . $item->condition_in_photo) }}" onclick="openLightbox(this.dataset.src)">
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="action-buttons">
                        <form action="{{ route('equipment.repair', $equipment) }}" method="POST" style="display: inline; max-width: 600px;">
                            @csrf
                            <div style="display:flex; gap:10px; align-items:center; margin-bottom:10px; flex-wrap:wrap;">
                                <div style="flex:0 0 180px;">
                                    <label style="display:block; font-weight:bold; font-size: 13px; color:#1B5E88;">Repair Cost (₱)</label>
                                    <input type="number" step="0.01" min="0" name="repair_cost" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
                                </div>
                                <div style="flex:1;">
                                    <label style="display:block; font-weight:bold; font-size: 13px; color:#1B5E88;">Repair Notes</label>
                                    <textarea name="repair_notes" rows="2" required placeholder="What was repaired/replaced?" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;"></textarea>
                                </div>
                                <div style="flex:0 0 180px;">
                                    <label style="display:block; font-weight:bold; font-size: 13px; color:#1B5E88;">Repaired Units</label>
                                    <input type="number" min="1" max="{{ $equipment->maintenance_count ?? 0 }}" name="repaired_count" value="{{ $equipment->maintenance_count ?? 0 }}" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
                                    <small style="color:#666;">Up to {{ $equipment->maintenance_count ?? 0 }}</small>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success" onclick="return confirm('Mark this equipment as repaired and available for rent?')">
                                <i class="fa-solid fa-check-circle"></i>
                                Mark as Repaired
                            </button>
                        </form>
                        <form action="{{ route('equipment.retire', $equipment) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Retire this equipment permanently? This cannot be undone.')">
                                <i class="fa-solid fa-ban"></i>
                                Retire Equipment
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fa-solid fa-check-circle"></i>
                <h2 style="color: #10b981; margin-bottom: 10px;">All Equipment in Good Condition</h2>
                <p style="color: #666;">No equipment currently needs maintenance.</p>
            </div>
        @endif

        <!-- Retired Equipment -->
        @if($retiredEquipment->count() > 0)
            <h2 class="section-title" style="margin-top: 50px;">
                <i class="fa-solid fa-archive"></i> Retired Equipment
            </h2>

            @foreach($retiredEquipment as $equipment)
                <div class="equipment-card retired">
                    <div class="equipment-header">
                        <div class="equipment-name">
                            <i class="fa-solid fa-box-archive"></i> {{ $equipment->name }}
                        </div>
                        <span class="status-badge status-retired">
                            <i class="fa-solid fa-archive"></i> Retired
                        </span>
                    </div>
                    <div style="color: #666; font-size: 14px;">
                        This equipment has been permanently retired from service.
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <script>
        function openLightbox(src) {
            let overlay = document.getElementById('photo-lightbox');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'photo-lightbox';
                overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.85);display:flex;align-items:center;justify-content:center;z-index:9999;';
                overlay.innerHTML = `
                    <div style="position:relative; max-width:90%; max-height:90%;">
                        <img id="lightbox-img" src="" alt="Damage photo" style="max-width:100%; max-height:100%; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.5);">
                        <button type="button" onclick="closeLightbox()" style="position:absolute; top:-12px; right:-12px; background:#fff; border:none; border-radius:9999px; width:36px; height:36px; cursor:pointer; font-weight:bold;">×</button>
                    </div>
                `;
                overlay.addEventListener('click', (e) => { if (e.target.id === 'photo-lightbox') closeLightbox(); });
                document.body.appendChild(overlay);
            }
            overlay.querySelector('#lightbox-img').src = src;
            overlay.style.display = 'flex';
        }
        function closeLightbox() {
            const overlay = document.getElementById('photo-lightbox');
            if (overlay) overlay.style.display = 'none';
        }
    </script>
</body>
</html>
