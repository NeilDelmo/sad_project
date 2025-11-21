<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Admin Dashboard - SeaLedger</title>
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
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

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Admin Dashboard</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Rental Management Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Equipment Rental Management</h3>
                    <p class="text-gray-600 mb-4">Manage fishermen rental requests for organization equipment.</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('rentals.admin.index') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                            <span>🔧</span>
                            <span>Manage Rentals</span>
                        </a>
                        <a href="{{ route('rentals.admin.maintenance') }}" class="inline-flex items-center gap-2 bg-amber-600 text-white px-4 py-2 rounded-md hover:bg-amber-700 transition">
                            <span>🛠️</span>
                            <span>Maintenance</span>
                        </a>
                        <a href="{{ route('rentals.admin.reports') }}" class="inline-flex items-center gap-2 bg-rose-600 text-white px-4 py-2 rounded-md hover:bg-rose-700 transition">
                            <span>🚩</span>
                            <span>Issue Reports</span>
                        </a>
                    </div>
                </div>
            </div>

                <!-- Rental Management Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Equipment Rental Management</h3>
                        <p class="text-gray-600 mb-4">Manage fishermen rental requests for organization equipment.</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('rentals.admin.index') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                                <span>🔧</span>
                                <span>Manage Rentals</span>
                            </a>
                            <a href="{{ route('rentals.admin.maintenance') }}" class="inline-flex items-center gap-2 bg-amber-600 text-white px-4 py-2 rounded-md hover:bg-amber-700 transition">
                                <span>🛠️</span>
                                <span>Maintenance</span>
                            </a>
                            <a href="{{ route('rentals.admin.reports') }}" class="inline-flex items-center gap-2 bg-rose-600 text-white px-4 py-2 rounded-md hover:bg-rose-700 transition">
                                <span>🚩</span>
                                <span>Issue Reports</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Platform Revenue Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Platform Revenue</h3>
                        <p class="text-gray-600 mb-4">Monitor marketplace transaction fees and platform earnings.</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('admin.revenue.index') }}" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                                <span>💰</span>
                                <span>View Revenue</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ML Analytics Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">ML Analytics</h3>
                        <p class="text-gray-600 mb-4">View machine learning model performance and analytics data.</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('admin.ml.analytics') }}" class="inline-flex items-center gap-2 bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition">
                                <span>🤖</span>
                                <span>View Analytics</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Risk Prediction Logs Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Fishing Risk Prediction Logs</h3>
                        <p class="text-gray-600 mb-4">View historical risk prediction logs from fishermen.</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('risk-history') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                                <span>📚</span>
                                <span>View Logs</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>