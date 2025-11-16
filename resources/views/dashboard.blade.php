<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Risk Prediction Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Fishing Risk Prediction</h3>
                        <p class="text-gray-600 mb-4">Predict fishing safety risks using ML based on weather conditions and location.</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('risk-form') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                                <span>⚡</span>
                                <span>Run Prediction</span>
                            </a>
                            <a href="{{ route('risk-history') }}" class="inline-flex items-center gap-2 bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition">
                                <span>📚</span>
                                <span>View Logs</span>
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
                        </div>
                    </div>
                </div>

                <!-- Welcome Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        {{ __("You're logged in!") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>