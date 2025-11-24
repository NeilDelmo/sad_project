<table>
    <thead>
    <tr>
        <th colspan="2" style="font-size: 16px; font-weight: bold; text-align: center;">SeaLedger Admin Report</th>
    </tr>
    <tr>
        <th colspan="2" style="text-align: center;">Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</th>
    </tr>
    <tr>
        <th colspan="2"></th>
    </tr>
    <tr>
        <th style="font-weight: bold;">Metric</th>
        <th style="font-weight: bold;">Value</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Total Revenue</td>
        <td>{{ $data['total_revenue'] }}</td>
    </tr>
    <tr>
        <td>Total Orders</td>
        <td>{{ $data['orders_count'] }}</td>
    </tr>
    <tr>
        <td>New Users</td>
        <td>{{ $data['new_users'] }}</td>
    </tr>
    <tr>
        <td>New Listings</td>
        <td>{{ $data['new_listings'] }}</td>
    </tr>
    <tr>
        <td>Risk Predictions</td>
        <td>{{ $data['predictions_count'] }}</td>
    </tr>
    <tr>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">User Distribution</td>
    </tr>
    @foreach($data['users_by_type'] as $type => $count)
        <tr>
            <td>{{ ucfirst($type) }}</td>
            <td>{{ $count }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">Daily Revenue</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Date</td>
        <td style="font-weight: bold;">Revenue</td>
    </tr>
    @foreach($data['daily_revenue'] as $day)
        <tr>
            <td>{{ \Carbon\Carbon::parse($day->date)->format('Y-m-d') }}</td>
            <td>{{ $day->revenue }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
