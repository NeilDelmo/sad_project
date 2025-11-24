<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Admin Reports - SeaLedger</title>
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

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-6 text-gray-900">Generate System Report</h2>
                <p class="mb-6 text-gray-600">Select a date range to generate a report on users, revenue, and system activity.</p>

                <form action="{{ route('admin.reports.generate') }}" method="GET" target="_blank" class="space-y-6 max-w-2xl">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" id="start_date" 
                                value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" id="end_date" 
                                value="{{ now()->format('Y-m-d') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 pt-4">
                        <button type="submit" name="type" value="pdf" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-file-pdf mr-2"></i> Download PDF
                        </button>
                        
                        <button type="submit" name="type" value="excel" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-file-excel mr-2 text-green-600"></i> Export to Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
