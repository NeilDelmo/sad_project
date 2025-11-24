<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Revenue Dashboard - SeaLedger Admin</title>
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>

@include('admin.partials.nav')

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Platform Revenue Dashboard</h1>
            <a href="{{ route('admin.revenue.export') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
                <i class="fas fa-download mr-2"></i> Export CSV
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
                <h2 class="text-sm font-medium uppercase text-gray-500 mb-1">Total Revenue</h2>
                <p class="text-3xl font-bold text-gray-900">₱{{ number_format($total,2) }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-6 md:col-span-2 border border-gray-100">
                <h2 class="text-sm font-medium uppercase text-gray-500 mb-4">Daily Revenue (Last 30 Days)</h2>
                <div class="mb-4 h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Daily Data Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Daily Breakdown</h3>
                </div>
                <div class="overflow-x-auto max-h-96">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($daily as $d)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $d->day }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($d->total,2) }}</td>
                            </tr>
                            @endforeach
                            @if($daily->isEmpty())
                            <tr><td colspan="2" class="px-6 py-4 text-center text-gray-500">No data yet</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Vendors Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Top Vendors by Fees</h3>
                </div>
                <div class="overflow-x-auto max-h-96">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Fees</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($vendors as $v)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $v->vendor?->name ?? 'ID '.$v->vendor_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($v->total,2) }}</td>
                            </tr>
                            @endforeach
                            @if($vendors->isEmpty())
                            <tr><td colspan="2" class="px-6 py-4 text-center text-gray-500">No vendor data yet</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dailyData = @json($daily);
        const labels = dailyData.map(d => d.day).reverse();
        const dataPoints = dailyData.map(d => parseFloat(d.total)).reverse();
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Create gradient
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Daily Revenue',
                    data: dataPoints,
                    borderColor: '#4f46e5',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#1f2937',
                        bodyColor: '#1f2937',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 4],
                            color: '#f3f4f6',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return '₱' + value;
                            },
                            font: {
                                size: 11
                            },
                            color: '#6b7280'
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#6b7280'
                        }
                    }
                }
            }
        });
    });
</script>
<script data-collect-dnt="true" async src="https://scripts.simpleanalyticscdn.com/latest.js"></script>
</body>
</html>