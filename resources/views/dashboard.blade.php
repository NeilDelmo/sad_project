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

@php
// Calculate dashboard statistics
$totalUsers = \App\Models\User::count();
$newUsersThisMonth = \App\Models\User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
$totalRevenue = \App\Models\Order::sum('total_price');
$revenueThisMonth = \App\Models\Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_price');
$activeListings = \App\Models\MarketplaceListing::where('status', 'approved')->count();
$totalListings = \App\Models\MarketplaceListing::count();
$totalPredictions = \App\Models\RiskPredictionLog::count();
$predictionsThisMonth = \App\Models\RiskPredictionLog::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
@endphp

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600">Welcome back! Here's what's happening with SeaLedger today.</p>
        </div>

        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="stat-card bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Total Users</span>
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ isset($totalUsers) ? number_format($totalUsers) : '0' }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ isset($newUsersThisMonth) ? number_format($newUsersThisMonth) : '0' }} new this month</div>
            </div>

            <!-- Total Revenue -->
            <div class="stat-card bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Total Revenue</span>
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-peso-sign text-green-600"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-900">₱{{ isset($totalRevenue) ? number_format($totalRevenue, 2) : '0.00' }}</div>
                <div class="text-xs text-gray-500 mt-1">₱{{ isset($revenueThisMonth) ? number_format($revenueThisMonth, 2) : '0.00' }} this month</div>
            </div>

            <!-- Active Listings -->
            <div class="stat-card bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Active Listings</span>
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-store text-purple-600"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ isset($activeListings) ? number_format($activeListings) : '0' }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ isset($totalListings) ? number_format($totalListings) : '0' }} total</div>
            </div>

            <!-- Risk Predictions -->
            <div class="stat-card bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Risk Predictions</span>
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i class="fas fa-cloud-sun text-orange-600"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ isset($totalPredictions) ? number_format($totalPredictions) : '0' }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ isset($predictionsThisMonth) ? number_format($predictionsThisMonth) : '0' }} this month</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <a href="#" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                    <div class="p-3 bg-blue-100 rounded-full mb-2">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Manage Users</span>
                </a>
                <a href="#" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                    <div class="p-3 bg-green-100 rounded-full mb-2">
                        <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Orders</span>
                </a>
                <a href="#" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                    <div class="p-3 bg-purple-100 rounded-full mb-2">
                        <i class="fas fa-store text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Marketplace</span>
                </a>
                <a href="#" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                    <div class="p-3 bg-orange-100 rounded-full mb-2">
                        <i class="fas fa-ship text-orange-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Rentals</span>
                </a>
                <a href="#" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                    <div class="p-3 bg-indigo-100 rounded-full mb-2">
                        <i class="fas fa-comments text-indigo-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Forum</span>
                </a>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Overview (Last 30 Days)</h3>
                <canvas id="revenueChart" height="200"></canvas>
            </div>

            <!-- User Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">User Distribution</h3>
                <canvas id="userChart" height="200"></canvas>
            </div>
        </div>

        <!-- Activity Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Orders</h3>
                <div class="space-y-3">
                    @php
                    $recentOrders = \App\Models\Order::with('user')->latest()->take(5)->get();
                    @endphp
                    @forelse($recentOrders as $order)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $order->user->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">₱{{ number_format($order->total_price, 2) }}</span>
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
                    @php
                    $recentThreads = \App\Models\ForumThread::with('user')->latest()->take(5)->get();
                    @endphp
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
@php
$dailyRevenue = \App\Models\Order::selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
    ->where('created_at', '>=', now()->subDays(30))
    ->groupBy('date')
    ->orderBy('date')
    ->get();

$dates = $dailyRevenue->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))->toArray();
$revenues = $dailyRevenue->pluck('revenue')->toArray();

$usersByType = \App\Models\User::selectRaw('account_type, COUNT(*) as count')
    ->groupBy('account_type')
    ->get();
$userTypes = $usersByType->pluck('account_type')->toArray();
$userCounts = $usersByType->pluck('count')->toArray();
@endphp

const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: @json($dates),
        datasets: [{
            label: 'Daily Revenue (₱)',
            data: @json($revenues),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
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
                'rgb(59, 130, 246)',
                'rgb(168, 85, 247)',
                'rgb(249, 115, 22)',
                'rgb(236, 72, 153)',
                'rgb(34, 197, 94)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
</x-app-layout>