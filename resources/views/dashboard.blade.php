<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Admin Dashboard - SeaLedger</title>
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .growth-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
        }
        .growth-positive {
            background-color: #d1fae5;
            color: #065f46;
        }
        .growth-negative {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>

@include('admin.partials.nav')

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                <p class="mt-2 text-sm text-gray-600">Welcome back! Here's what's happening with SeaLedger today.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700">
                    <i class="fas fa-calendar-alt mr-2"></i> {{ now()->format('F d, Y') }}
                </span>
            </div>
        </div>

        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Users</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalUsers) }}</h3>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-sm">
                    @if(isset($userGrowth))
                        <span class="{{ $userGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium flex items-center">
                            <i class="fas fa-arrow-{{ $userGrowth >= 0 ? 'up' : 'down' }} mr-1"></i>
                            {{ abs(round($userGrowth, 1)) }}%
                        </span>
                        <span class="text-gray-400 ml-2">from last month</span>
                    @endif
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($totalRevenue, 2) }}</h3>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <i class="fas fa-peso-sign text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-sm">
                    @if(isset($revenueGrowth))
                        <span class="{{ $revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium flex items-center">
                            <i class="fas fa-arrow-{{ $revenueGrowth >= 0 ? 'up' : 'down' }} mr-1"></i>
                            {{ abs(round($revenueGrowth, 1)) }}%
                        </span>
                        <span class="text-gray-400 ml-2">from last month</span>
                    @endif
                </div>
            </div>

            <!-- Active Listings -->
            <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Active Listings</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($activeListings) }}</h3>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-lg">
                        <i class="fas fa-store text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-sm">
                    <span class="text-gray-500">{{ number_format($totalListings) }} total listings</span>
                </div>
            </div>

            <!-- Risk Predictions -->
            <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Risk Predictions</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalPredictions) }}</h3>
                    </div>
                    <div class="p-3 bg-orange-50 rounded-lg">
                        <i class="fas fa-cloud-sun text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-sm">
                    @if(isset($predictionGrowth))
                        <span class="{{ $predictionGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium flex items-center">
                            <i class="fas fa-arrow-{{ $predictionGrowth >= 0 ? 'up' : 'down' }} mr-1"></i>
                            {{ abs(round($predictionGrowth, 1)) }}%
                        </span>
                        <span class="text-gray-400 ml-2">from last month</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                    <div class="p-3 bg-blue-100 rounded-full mb-2">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Manage Users</span>
                </a>
                <a href="{{ route('admin.revenue.index') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                    <div class="p-3 bg-green-100 rounded-full mb-2">
                        <i class="fas fa-chart-line text-green-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Revenue</span>
                </a>
                <a href="{{ route('marketplace.index') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                    <div class="p-3 bg-purple-100 rounded-full mb-2">
                        <i class="fas fa-store text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Marketplace</span>
                </a>
                <a href="{{ route('rentals.admin.index') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                    <div class="p-3 bg-orange-100 rounded-full mb-2">
                        <i class="fas fa-ship text-orange-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Rentals</span>
                </a>
                <a href="{{ route('forums.index') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                    <div class="p-3 bg-indigo-100 rounded-full mb-2">
                        <i class="fas fa-comments text-indigo-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Forum</span>
                </a>
            </div>
        </div>

        <!-- Charts Row 1: Revenue & Traffic -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Revenue Overview (Last 30 Days)</h3>
                <div class="relative h-72">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Traffic Overview -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Traffic Overview (Last 30 Days)</h3>
                @if(isset($analyticsData) && isset($analyticsData['histogram']))
                    <div class="relative h-72">
                        <canvas id="analyticsChart"></canvas>
                    </div>
                @else
                    <div class="flex items-center justify-center h-72 text-gray-400">
                        <p>No traffic data available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Charts Row 2: Distributions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- User Distribution -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">User Distribution</h3>
                <div class="relative h-72">
                    <canvas id="userChart"></canvas>
                </div>
            </div>

            <!-- Order Status Distribution -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Order Status Distribution</h3>
                <div class="relative h-72">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>

        @if(isset($analyticsData) && $analyticsData)
        <!-- Web Analytics Summary Cards -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Web Analytics Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Visitors</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($analyticsData['visitors'] ?? 0) }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-full">
                            <i class="fas fa-user-group text-blue-500"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pageviews</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($analyticsData['pageviews'] ?? 0) }}</p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-full">
                            <i class="fas fa-eye text-red-500"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Avg. Time on Page</p>
                            <p class="text-2xl font-bold text-gray-900">{{ gmdate("i:s", $analyticsData['seconds_on_page'] ?? 0) }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-full">
                            <i class="fas fa-clock text-green-500"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Activity Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Orders</h3>
                <div class="space-y-3">
                    @forelse($recentOrders as $order)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $order->user->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">₱{{ number_format($order->total, 2) }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No recent orders</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Forum Activity -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Forum Posts</h3>
                <div class="space-y-3">
                    @forelse($recentThreads as $thread)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ Str::limit($thread->title, 40) }}</p>
                            <p class="text-xs text-gray-500">by {{ $thread->user->name ?? 'Unknown' }} • {{ $thread->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 bg-gray-100 rounded">{{ $thread->replies_count ?? 0 }} replies</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No recent forum activity</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Scripts -->
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const gradient = revenueCtx.createLinearGradient(0, 0, 0, 300);
gradient.addColorStop(0, 'rgba(34, 197, 94, 0.1)');
gradient.addColorStop(1, 'rgba(34, 197, 94, 0)');

new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: @json($revenueDates),
        datasets: [{
            label: 'Daily Revenue',
            data: @json($revenueValues),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: gradient,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: 'rgb(34, 197, 94)',
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
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                titleColor: '#1f2937',
                bodyColor: '#1f2937',
                borderColor: '#e5e7eb',
                borderWidth: 1,
                padding: 12,
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 13 },
                cornerRadius: 8,
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
                    font: { size: 11 },
                    color: '#9ca3af',
                    callback: function(value) {
                        return '₱' + value;
                    }
                }
            },
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    font: { size: 11 },
                    color: '#9ca3af'
                }
            }
        }
    }
});

// User Distribution Chart
const userCtx = document.getElementById('userChart').getContext('2d');
new Chart(userCtx, {
    type: 'doughnut',
    data: {
        labels: @json($userTypes),
        datasets: [{
            data: @json($userCounts),
            backgroundColor: [
                '#3b82f6', // Blue
                '#10b981', // Emerald
                '#f59e0b', // Amber
                '#ef4444', // Red
                '#8b5cf6'  // Violet
            ],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 20,
                    font: { size: 12 }
                }
            }
        }
    }
});

// Order Status Chart
const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
new Chart(orderStatusCtx, {
    type: 'bar',
    data: {
        labels: @json($orderStatuses),
        datasets: [{
            label: 'Orders',
            data: @json($orderStatusCounts),
            backgroundColor: '#3b82f6',
            borderRadius: 4,
            barThickness: 20
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                titleColor: '#1f2937',
                bodyColor: '#1f2937',
                borderColor: '#e5e7eb',
                borderWidth: 1,
                padding: 12,
                cornerRadius: 8,
                displayColors: false
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
                    font: { size: 11 },
                    color: '#9ca3af',
                    stepSize: 1
                }
            },
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    font: { size: 11 },
                    color: '#9ca3af'
                }
            }
        }
    }
});

@if(isset($analyticsData) && isset($analyticsData['histogram']))
    const analyticsCtx = document.getElementById('analyticsChart').getContext('2d');
    const analyticsData = @json($analyticsData['histogram']);
    
    const analyticsDates = analyticsData.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });
    const analyticsVisitors = analyticsData.map(item => item.visitors);
    const analyticsPageviews = analyticsData.map(item => item.pageviews);

    new Chart(analyticsCtx, {
        type: 'line',
        data: {
            labels: analyticsDates,
            datasets: [
                {
                    label: 'Pageviews',
                    data: analyticsPageviews,
                    borderColor: '#ef4444', // Red-500
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5
                },
                {
                    label: 'Visitors',
                    data: analyticsVisitors,
                    borderColor: '#3b82f6', // Blue-500
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { 
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#1f2937',
                    bodyColor: '#1f2937',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    padding: 12,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 13 },
                    cornerRadius: 8,
                    displayColors: true,
                    boxPadding: 4
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
                        font: { size: 11 },
                        color: '#9ca3af'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: { size: 11 },
                        color: '#9ca3af'
                    }
                }
            }
        }
    });
@endif
</script>
</body>
</html>