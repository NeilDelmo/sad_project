<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Admin Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 { margin: 0; color: #1a56db; font-size: 24px; }
        .header p { margin: 5px 0 0; color: #666; }
        
        .section { margin-bottom: 20px; }
        .section h2 { 
            border-bottom: 1px solid #ddd; 
            padding-bottom: 5px; 
            margin-bottom: 10px;
            font-size: 16px;
            color: #444;
        }
        
        .metrics-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .metrics-table td {
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #eee;
            width: 25%;
            text-align: center;
        }
        .metric-label { font-size: 10px; color: #666; text-transform: uppercase; }
        .metric-value { font-size: 18px; font-weight: bold; color: #111; margin-top: 5px; }
        
        table.data-table { width: 100%; border-collapse: collapse; }
        table.data-table th, table.data-table td { text-align: left; padding: 8px; border-bottom: 1px solid #eee; }
        table.data-table th { background-color: #f3f4f6; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>SeaLedger Admin Report</h1>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        <p>Generated on: {{ now()->format('M d, Y H:i') }}</p>
    </div>

    <div class="section">
        <h2>Key Metrics</h2>
        <table class="metrics-table">
            <tr>
                <td>
                    <div class="metric-label">Total Revenue</div>
                    <div class="metric-value">₱{{ number_format($data['total_revenue'], 2) }}</div>
                </td>
                <td>
                    <div class="metric-label">Total Orders</div>
                    <div class="metric-value">{{ number_format($data['orders_count']) }}</div>
                </td>
                <td>
                    <div class="metric-label">New Users</div>
                    <div class="metric-value">{{ number_format($data['new_users']) }}</div>
                </td>
                <td>
                    <div class="metric-label">New Listings</div>
                    <div class="metric-value">{{ number_format($data['new_listings']) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>User Distribution (New Users)</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>User Type</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['users_by_type'] as $type => $count)
                <tr>
                    <td>{{ ucfirst($type) }}</td>
                    <td>{{ $count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Daily Revenue</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['daily_revenue'] as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                    <td>₱{{ number_format($day->revenue, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
