<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>My Reports - SeaLedger</title>
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

        .report-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-top: 4px solid #1B5E88; /* Blue accent */
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .report-id {
            font-size: 18px;
            font-weight: bold;
            color: #1B5E88;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-open {
            background: #fff3cd;
            color: #856404;
        }

        .status-under_review {
            background: #cff4fc;
            color: #055160;
        }

        .status-resolved {
            background: #d1e7dd;
            color: #0f5132;
        }

        .severity-badge {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .severity-high { color: #dc3545; background: #f8d7da; }
        .severity-medium { color: #fd7e14; background: #ffe5d0; }
        .severity-low { color: #198754; background: #d1e7dd; }

        .report-details {
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
                <a href="{{ route('rentals.myrentals') }}" class="nav-link"><i class="fa-solid fa-clipboard-list"></i> My Rentals</a>
                <a href="{{ route('rentals.myreports') }}" class="nav-link active"><i class="fa-solid fa-flag"></i> My Reports</a>
                @if(Auth::user()->user_type !== 'buyer')
                    <a href="{{ route('dashboard') }}" class="nav-link"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                @endif
            </div>
        </div>
    </nav>

    <div class="container">

        <div class="header">
            <h1>🚩 My Issue Reports</h1>
            <p>Track the status of your reported issues</p>
        </div>

        @if($reports->count() > 0)
            @foreach($reports as $report)
                <div class="report-card">
                    <div class="report-header">
                        <div class="report-id">Report #{{ $report->id }}</div>
                        <span class="status-badge status-{{ $report->status }}">
                            {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                        </span>
                    </div>

                    <div class="report-details">
                        <div class="detail-item">
                            <div class="detail-label">Rental ID</div>
                            <div class="detail-value">#{{ $report->rental_id }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Issue Type</div>
                            <div class="detail-value">{{ ucfirst(str_replace('_', ' ', $report->issue_type)) }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Severity</div>
                            <div class="detail-value">
                                @php
                                    $sev = strtolower($report->severity ?? 'low');
                                    $sevClass = match($sev) {
                                        'high' => 'severity-high',
                                        'medium' => 'severity-medium',
                                        default => 'severity-low'
                                    };
                                @endphp
                                <span class="severity-badge {{ $sevClass }}">
                                    {{ ucfirst($report->severity ?? 'N/A') }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Reported On</div>
                            <div class="detail-value">{{ $report->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>

                    <div style="padding: 15px; background: #f8f9fa; border-radius: 6px; margin-bottom: 15px;">
                        <h5 style="color: #1B5E88; margin-bottom: 10px;">{{ $report->title ?? 'No Title' }}</h5>
                        <p style="color: #444;">{{ $report->description }}</p>
                    </div>

                    @if($report->photos)
                        <div style="margin-bottom: 15px;">
                            <h6 style="color: #666; margin-bottom: 10px;">Attached Photos:</h6>
                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                @foreach($report->photos as $photo)
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $photo) }}" alt="Issue Photo" style="height: 80px; border-radius: 4px; border: 1px solid #ddd;">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach
        @else
            <div class="empty-state">
                <h2>No Reports Found</h2>
                <p>You haven't reported any issues with your rentals.</p>
                <a href="{{ route('rentals.myrentals') }}" class="browse-btn">Go to My Rentals</a>
            </div>
        @endif
    </div>
    @include('partials.toast-notifications')
    <script data-collect-dnt="true" async src="https://scripts.simpleanalyticscdn.com/latest.js"></script>
</body>
</html>
