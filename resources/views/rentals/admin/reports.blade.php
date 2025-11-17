<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Issue Reports - SeaLedger</title>
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; background-color: #f8f9fa; min-height: 100vh; }
        .navbar { background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%); padding: 15px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-container { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .nav-brand { color: white; font-size: 28px; font-weight: bold; font-family: "Koulen", sans-serif; text-shadow: 2px 2px 4px rgba(0,0,0,0.2); text-decoration: none; }
        .nav-link { color: rgba(255, 255, 255, 0.9); text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 16px; font-weight: 500; transition: all 0.3s ease; }
        .nav-link:hover { color: white; background: rgba(255, 255, 255, 0.15); }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .page-header { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .page-title { font-family: "Koulen", sans-serif; font-size: 48px; color: #1B5E88; margin-bottom: 10px; }
        .page-subtitle { color: #666; font-size: 16px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .stat-icon { font-size: 36px; margin-bottom: 10px; }
        .stat-number { font-size: 32px; font-weight: bold; color: #1B5E88; }
        .stat-label { font-size: 14px; color: #666; margin-top: 5px; }
        .report-card { background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 25px; margin-bottom: 20px; border-left: 5px solid #ffc107; }
        .report-card.severity-high { border-left-color: #dc3545; }
        .report-card.severity-medium { border-left-color: #ff9800; }
        .report-card.severity-low { border-left-color: #17a2b8; }
        .report-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0; }
        .report-title { font-size: 20px; font-weight: bold; color: #1B5E88; margin-bottom: 8px; }
        .report-meta { font-size: 14px; color: #666; }
        .status-badge { padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: bold; text-transform: uppercase; display: inline-block; }
        .status-open { background: #fff3cd; color: #856404; }
        .status-under_review { background: #d1ecf1; color: #0c5460; }
        .status-resolved { background: #d4edda; color: #155724; }
        .severity-badge { padding: 6px 12px; border-radius: 14px; font-size: 12px; font-weight: 700; margin-left: 8px; }
        .severity-high { background: #fee2e2; color: #991b1b; }
        .severity-medium { background: #fed7aa; color: #9a3412; }
        .severity-low { background: #dbeafe; color: #1e40af; }
        .issue-type-badge { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; background: #e5e7eb; color: #374151; margin-left: 6px; }
        .report-description { padding: 15px; background: #f8f9fa; border-radius: 8px; margin: 15px 0; color: #333; line-height: 1.6; }
        .photo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin: 15px 0; }
        .photo-thumb { width: 100%; height: 120px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid #e5e7eb; transition: all 0.2s; }
        .photo-thumb:hover { border-color: #0075B5; transform: scale(1.05); }
        .rental-info { background: #f0f9ff; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; color: #1e40af; }
        .btn { padding: 10px 20px; border: none; border-radius: 8px; font-size: 14px; font-weight: bold; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-primary { background: #0075B5; color: white; }
        .btn-primary:hover { background: #1B5E88; transform: translateY(-2px); }
        .btn-success { background: #10b981; color: white; }
        .btn-success:hover { background: #059669; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .empty-state { background: white; padding: 60px 20px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .empty-state i { font-size: 64px; color: #ddd; margin-bottom: 20px; }
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; }
        .modal.show { display: flex; }
        .modal-content { background: white; border-radius: 12px; padding: 30px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .modal-title { font-size: 22px; font-weight: bold; color: #1B5E88; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-weight: 600; margin-bottom: 8px; color: #333; }
        .form-control { width: 100%; padding: 10px 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
        .form-control:focus { outline: none; border-color: #0075B5; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a class="nav-brand" href="{{ route('dashboard') }}">🐟 SeaLedger</a>
            <div>
                <a href="{{ route('rentals.admin.index') }}" class="nav-link"><i class="fa-solid fa-toolbox"></i> Rentals</a>
                <a href="{{ route('rentals.admin.reports') }}" class="nav-link"><i class="fa-solid fa-flag"></i> Reports</a>
                <a href="{{ route('dashboard') }}" class="nav-link"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
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
        <div class="page-header">
            <div class="page-title">🚩 Rental Issue Reports</div>
            <div class="page-subtitle">Review and manage equipment issues reported by fishermen</div>
        </div>

        @if(session('success'))
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="color: #ffc107;"><i class="fa-solid fa-flag"></i></div>
                <div class="stat-number">{{ $stats['open'] }}</div>
                <div class="stat-label">Open Reports</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #0c5460;"><i class="fa-solid fa-eye"></i></div>
                <div class="stat-number">{{ $stats['under_review'] }}</div>
                <div class="stat-label">Under Review</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #10b981;"><i class="fa-solid fa-check-circle"></i></div>
                <div class="stat-number">{{ $stats['resolved'] }}</div>
                <div class="stat-label">Resolved</div>
            </div>
        </div>

        @if($reports->count() > 0)
            @foreach($reports as $report)
                <div class="report-card @if($report->severity) severity-{{ $report->severity }} @endif">
                    <div class="report-header">
                        <div style="flex: 1;">
                            <div class="report-title">
                                {{ $report->title ?: 'Issue Report #' . $report->id }}
                                <span class="issue-type-badge">{{ ucfirst(str_replace('_', ' ', $report->issue_type)) }}</span>
                                @if($report->severity)
                                    <span class="severity-badge severity-{{ $report->severity }}">{{ strtoupper($report->severity) }}</span>
                                @endif
                            </div>
                            <div class="report-meta">
                                Reported by <strong>{{ $report->user->username ?? $report->user->email }}</strong>
                                {{ $report->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <span class="status-badge status-{{ $report->status }}">{{ ucfirst(str_replace('_', ' ', $report->status)) }}</span>
                    </div>

                    <div class="rental-info">
                        <strong><i class="fa-solid fa-receipt"></i> Rental #{{ $report->rental_id }}</strong> 
                        | Status: <strong>{{ ucfirst($report->rental->status) }}</strong>
                        | Items: 
                        @foreach($report->rental->rentalItems as $item)
                            {{ $item->product->name }} (×{{ $item->quantity }})@if(!$loop->last), @endif
                        @endforeach
                    </div>

                    <div class="report-description">
                        {{ $report->description }}
                    </div>

                    @if($report->photos && count($report->photos) > 0)
                        <div style="margin: 15px 0;">
                            <strong style="color: #1B5E88; font-size: 14px; margin-bottom: 8px; display: block;">
                                <i class="fa-solid fa-images"></i> Attached Photos ({{ count($report->photos) }})
                            </strong>
                            <div class="photo-grid">
                                @foreach($report->photos as $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" alt="Issue photo" class="photo-thumb" onclick="openLightbox('{{ asset('storage/' . $photo) }}')">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($report->status === 'open')
                        <div style="display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 2px solid #f0f0f0; flex-wrap: wrap;">
                            <button type="button" class="btn btn-primary" onclick='openMaintenanceModal({{ $report->id }}, @json($report->rental->rentalItems))'>
                                <i class="fa-solid fa-wrench"></i> Move to Maintenance
                            </button>
                            <form method="POST" action="{{ route('reports.resolve', $report) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Mark this report as resolved?')">
                                    <i class="fa-solid fa-check"></i> Mark Resolved
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($report->status === 'under_review')
                        <div style="display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 2px solid #f0f0f0;">
                            <form method="POST" action="{{ route('reports.resolve', $report) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fa-solid fa-check"></i> Mark Resolved
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fa-solid fa-inbox"></i>
                <h2 style="color: #1B5E88; margin-bottom: 10px;">No Issue Reports</h2>
                <p style="color: #666;">No fishermen have reported any issues yet.</p>
            </div>
        @endif
    </div>

    <!-- Maintenance Modal -->
    <div id="maintenanceModal" class="modal">
        <div class="modal-content">
            <h2 class="modal-title"><i class="fa-solid fa-wrench"></i> Move to Maintenance</h2>
            <form id="maintenanceForm" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Select Item</label>
                    <select name="product_id" id="productSelect" class="form-control" required></select>
                </div>
                <div class="form-group">
                    <label class="form-label">Number of Units</label>
                    <input type="number" name="units" class="form-control" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes (optional)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes about the issue..."></textarea>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Confirm</button>
                    <button type="button" class="btn btn-secondary" onclick="closeMaintenanceModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lightbox -->
    <div id="photo-lightbox" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:99999; align-items:center; justify-content:center;" onclick="closeLightbox()">
        <div style="position:relative; max-width:90%; max-height:90%;">
            <img id="lightbox-img" src="" alt="Issue photo" style="max-width:100%; max-height:100%; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.5);">
            <button type="button" onclick="closeLightbox(); event.stopPropagation();" style="position:absolute; top:-12px; right:-12px; background:#fff; border:none; border-radius:9999px; width:36px; height:36px; cursor:pointer; font-weight:bold;">×</button>
        </div>
    </div>

    <script>
        function openMaintenanceModal(reportId, items) {
            const modal = document.getElementById('maintenanceModal');
            const form = document.getElementById('maintenanceForm');
            const select = document.getElementById('productSelect');
            
            form.action = `/admin/reports/${reportId}/mark-maintenance`;
            
            select.innerHTML = '';
            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.product_id;
                option.textContent = `${item.product.name} (×${item.quantity})`;
                select.appendChild(option);
            });
            
            modal.classList.add('show');
        }
        
        function closeMaintenanceModal() {
            document.getElementById('maintenanceModal').classList.remove('show');
        }
        
        function openLightbox(src) {
            const lightbox = document.getElementById('photo-lightbox');
            document.getElementById('lightbox-img').src = src;
            lightbox.style.display = 'flex';
        }
        
        function closeLightbox() {
            document.getElementById('photo-lightbox').style.display = 'none';
        }
    </script>
</body>
</html>
