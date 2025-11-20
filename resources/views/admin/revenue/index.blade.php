@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6">Platform Revenue Dashboard</h1>
    <div class="mb-4 flex items-center gap-4">
        <a href="{{ route('admin.revenue.export') }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded shadow">
            Export CSV
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm uppercase text-gray-500">Total Revenue</h2>
            <p class="text-3xl font-bold mt-2">₱{{ number_format($total,2) }}</p>
        </div>
        <div class="bg-white shadow rounded p-4 md:col-span-2">
            <h2 class="text-sm uppercase text-gray-500 mb-4">Daily (Last 30 Days)</h2>
            <div class="mb-4">
                <canvas id="revenueChart" height="120"></canvas>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="px-2 py-1">Day</th>
                            <th class="px-2 py-1">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daily as $d)
                        <tr class="border-t">
                            <td class="px-2 py-1">{{ $d->day }}</td>
                            <td class="px-2 py-1">₱{{ number_format($d->total,2) }}</td>
                        </tr>
                        @endforeach
                        @if($daily->isEmpty())
                        <tr><td colspan="2" class="px-2 py-4 text-center text-gray-500">No data yet</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded p-4">
        <h2 class="text-sm uppercase text-gray-500 mb-2">Top Vendors by Platform Fees</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="px-2 py-1">Vendor</th>
                        <th class="px-2 py-1">Total Fees</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vendors as $v)
                    <tr class="border-t">
                        <td class="px-2 py-1">{{ $v->vendor?->name ?? 'ID '.$v->vendor_id }}</td>
                        <td class="px-2 py-1">₱{{ number_format($v->total,2) }}</td>
                    </tr>
                    @endforeach
                    @if($vendors->isEmpty())
                    <tr><td colspan="2" class="px-2 py-4 text-center text-gray-500">No vendor data yet</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function() {
        const dailyData = @json($daily);
        const labels = dailyData.map(d => d.day).reverse();
        const dataPoints = dailyData.map(d => parseFloat(d.total)).reverse();
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Daily Fees (₱)',
                    data: dataPoints,
                    borderColor: 'rgba(79,70,229,0.9)',
                    backgroundColor: 'rgba(79,70,229,0.15)',
                    tension: 0.25,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return '₱' + value; }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(ctx) { return '₱' + ctx.parsed.y.toFixed(2); }
                        }
                    }
                }
            }
        });
    })();
</script>
@endsection