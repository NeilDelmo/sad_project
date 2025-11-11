<x-app-layout>
    @php
        $compactLayout = $compactLayout ?? false;
        $allowHistory = auth()->check() && in_array(auth()->user()->user_type, ['admin', 'regulator']);
        $enableOutcomeLogging = auth()->check();
    @endphp
    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Ensure map renders properly */
        #fishing-map {
            height: 100%;
            width: 100%;
            z-index: 1;
        }

        /* Fix Leaflet marker icons */
        .leaflet-default-icon-path {
            background-image: url(https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png);
        }

        /* Improve text visibility */
        .text-improved {
            color: #1f2937 !important;
        }

        .dark .text-improved {
            color: #f3f4f6 !important;
        }

        /* Better contrast for cards */
        .card-light {
            background-color: #ffffff;
        }

        .dark .card-light {
            background-color: #1f2937;
        }

        .on-land-card {
            background-color: #f3f4f6;
            border-color: #d1d5db;
            color: #374151;
        }

        .dark .on-land-card {
            background-color: rgba(55, 65, 81, 0.7);
            border-color: rgba(75, 85, 99, 0.7);
            color: #d1d5db;
        }
    </style>
    @if($compactLayout)
    <style>
        body.compact-map main {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        body.compact-map .compact-container {
            max-width: none !important;
            width: 100% !important;
        }

        body.compact-map .compact-surface {
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        body.compact-map header {
            display: none !important;
        }

        body.compact-map .min-h-screen > .flex > div:first-child {
            display: none !important;
        }

        body.compact-map .min-h-screen > .flex > div.flex-1 {
            width: 100% !important;
        }
    </style>
    @endif
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @endpush

    @if($compactLayout)
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.body.classList.add('compact-map');
            });
        </script>
        @endpush
    @endif

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl text-gray-900 dark:text-white leading-tight">
                    🌊 Fishing Safety Map
                </h2>
                <p class="text-gray-700 dark:text-gray-300 text-sm mt-1 font-medium">Click on the map to check conditions, hold the right mouse button to pan around</p>
            </div>
            <div class="flex items-center space-x-3">
                @unless($compactLayout)
                <button id="current-location-btn" class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    My Location
                </button>
                @endunless
                @if($allowHistory && ! $compactLayout)
                <a href="{{ route('risk-history') }}" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    History
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="{{ $compactLayout ? 'py-4 sm:py-6' : 'py-6' }}">
        <div class="{{ $compactLayout ? 'compact-container w-full px-3 sm:px-6 lg:px-8' : 'max-w-7xl mx-auto sm:px-6 lg:px-8' }}">
            <div class="space-y-6">
                <!-- Map Section -->
                <div class="bg-white dark:bg-gray-800 shadow-xl {{ $compactLayout ? 'compact-surface rounded-none sm:rounded-2xl' : 'rounded-2xl' }} overflow-hidden">
                    <div class="p-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                        <h3 class="text-xl font-bold flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Interactive Safety Map
                        </h3>
                        <p class="text-white font-medium mt-1">Click anywhere on the map to check fishing safety conditions, hold right-click to move the map</p>
                    </div>

                    <div class="{{ $compactLayout ? 'relative h-[calc(100vh-160px)] md:h-[calc(100vh-140px)]' : 'relative h-96 md:h-[600px]' }}">
                        <div id="fishing-map" class="w-full h-full"></div>

                        @if($compactLayout)
                        <div class="absolute top-4 left-4 z-20">
                            <button id="current-location-btn" class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-lg transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                My Location
                            </button>
                        </div>
                        @endif

                        <!-- Loading overlay -->
                        <div id="loading-overlay" class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-90 dark:bg-opacity-90 flex items-center justify-center hidden">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                                <p class="mt-4 text-gray-900 dark:text-white font-bold">Checking conditions...</p>
                                <p class="text-sm text-gray-700 dark:text-gray-200 font-medium">Fetching weather data and analyzing safety</p>
                            </div>
                        </div>

                        <!-- Map legend -->
                        <div class="absolute bottom-4 left-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 z-10 border border-gray-200 dark:border-gray-600">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Safety Levels</h4>
                            <div class="flex flex-col space-y-2">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded-full bg-green-500 mr-2 border-2 border-white"></div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Safe</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded-full bg-yellow-500 mr-2 border-2 border-white"></div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Caution</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded-full bg-red-500 mr-2 border-2 border-white"></div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Dangerous</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sample locations -->
                        <div class="absolute top-4 right-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 z-10 max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Popular Fishing Spots</h4>
                            <div class="space-y-2">
                                @foreach([
                                ['name' => 'Talisay Point', 'lat' => 13.901804, 'lng' => 120.621940],
                                ['name' => 'Balibago Beach', 'lat' => 13.931485, 'lng' => 120.618735],
                                ['name' => 'Matabungkay Beach', 'lat' => 13.947251, 'lng' => 120.615741],
                                ['name' => 'Calatagan Fishing Spot', 'lat' => 13.866245640009993, 'lng' => 120.6176208450655],
                                ] as $spot)
                                <button
                                    class="flex items-center w-full text-left px-3 py-2 hover:bg-blue-50 dark:hover:bg-gray-700 rounded transition"
                                    data-lat="{{ $spot['lat'] }}"
                                    data-lng="{{ $spot['lng'] }}">
                                    <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $spot['name'] }}</span>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex flex-wrap gap-2 justify-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Real-time data
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                Historical incidents
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M12 19.394V20m4.647-6.364l.707.707M6.343 17.657l.707.707m0-12.728l-.707.707m0 0l.707.707"></path>
                                </svg>
                                Tide & moon phases
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Welcome Panel -->
                <div id="default-panel" class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <div class="inline-block p-4 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-4">
                                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Welcome to Fishing Safety Map</h3>
                                <p class="text-gray-600 dark:text-gray-400 mt-2">Click anywhere on the map to check real-time fishing safety conditions and use right-click drag to pan</p>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                    <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-2">How to Use</h4>
                                    <ol class="list-decimal list-inside space-y-1 text-sm text-blue-800 dark:text-blue-200">
                                        <li>Click on the map to check safety conditions</li>
                                        <li>Hold the right mouse button and drag to pan the map</li>
                                        <li>Use the "My Location" button to find your current position</li>
                                        <li>Select pre-defined fishing spots from the right panel</li>
                                        <li>View detailed safety recommendations and weather conditions</li>
                                    </ol>
                                </div>

                                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                    <h4 class="font-semibold text-green-800 dark:text-green-200 mb-2">Safety Features</h4>
                                    <ul class="list-disc list-inside space-y-1 text-sm text-green-700 dark:text-green-200">
                                        <li>Real-time weather and marine conditions</li>
                                        <li>Wave height and wind speed analysis</li>
                                        <li>Historical incident data</li>
                                        <li>Tide levels and moon phase impact</li>
                                        <li>Personalized safety recommendations</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Result Panel (hidden by default) -->
                    <div id="result-panel" class="hidden bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 shadow-xl rounded-2xl overflow-hidden">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Safety Assessment
                                </h3>
                                <button id="close-result-btn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid gap-6 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)]">
                                <div class="space-y-6">
                                    <div id="safety-result-card" class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md border border-blue-200/60 dark:border-blue-900/40">
                                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                            <div>
                                                <p id="location-name" class="text-sm font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wide">Calatagan, Batangas</p>
                                                <p id="coordinates" class="text-xs text-gray-400 dark:text-gray-500 mt-1">13.8500, 120.6167</p>
                                                <p id="marine-notice" class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-2 hidden">This point is on land; marine data limited.</p>
                                            </div>
                                            <div class="text-center md:text-right">
                                                <div id="safety-icon" class="text-4xl md:text-5xl mb-1">✅</div>
                                                <h2 id="safety-verdict" class="text-2xl md:text-3xl font-bold text-green-600 dark:text-green-400">Safe</h2>
                                                <p id="confidence-level" class="text-xs md:text-sm text-gray-600 dark:text-gray-300">Confidence: 92%</p>
                                            </div>
                                        </div>

                                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                            <div class="rounded-xl border border-blue-100 dark:border-blue-900/50 bg-blue-50/60 dark:bg-blue-900/20 p-4">
                                                <p class="text-xs font-semibold uppercase text-blue-700 dark:text-blue-200 tracking-wide">Wind Speed</p>
                                                <p id="wind-speed" class="mt-2 text-xl font-semibold text-blue-900 dark:text-blue-100">12.5 km/h</p>
                                            </div>
                                            <div class="rounded-xl border border-purple-100 dark:border-purple-900/50 bg-purple-50/60 dark:bg-purple-900/20 p-4">
                                                <p class="text-xs font-semibold uppercase text-purple-700 dark:text-purple-200 tracking-wide">Wave Height</p>
                                                <p id="wave-height" class="mt-2 text-xl font-semibold text-purple-900 dark:text-purple-100">0.8 m</p>
                                            </div>
                                            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/50 bg-emerald-50/60 dark:bg-emerald-900/20 p-4">
                                                <p class="text-xs font-semibold uppercase text-emerald-700 dark:text-emerald-200 tracking-wide">Visibility</p>
                                                <p id="visibility" class="mt-2 text-xl font-semibold text-emerald-900 dark:text-emerald-100">10.0 km</p>
                                            </div>
                                            <div class="rounded-xl border border-amber-100 dark:border-amber-900/50 bg-amber-50/60 dark:bg-amber-900/20 p-4">
                                                <p class="text-xs font-semibold uppercase text-amber-700 dark:text-amber-200 tracking-wide">Tide State</p>
                                                <p id="tide-state" class="mt-2 text-xl font-semibold text-amber-900 dark:text-amber-100">Rising</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md border border-yellow-200/60 dark:border-yellow-900/40">
                                        <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-3 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Risk Areas Nearby
                                        </h4>
                                        <div id="risk-areas" class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg">
                                            <p class="text-sm text-yellow-700 dark:text-yellow-200">No high-risk areas detected within 10km radius</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div id="weather-alerts-section" class="hidden bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md border border-red-200/60 dark:border-red-900/40">
                                        <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-3 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L4.34 17c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                            Active Weather Alerts
                                        </h4>
                                        <div id="weather-alerts" class="space-y-3"></div>
                                    </div>

                                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md border border-blue-200/60 dark:border-blue-900/40">
                                        <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-3 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                            </svg>
                                            Action Plan
                                        </h4>
                                        <div id="recommendations" class="space-y-2">
                                            <div class="flex items-start">
                                                <span class="flex-shrink-0 w-5 h-5 text-green-500">
                                                    <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                                <p class="ml-2 text-sm text-gray-700 dark:text-gray-300">Conditions are generally safe for fishing</p>
                                            </div>
                                            <div class="flex items-start">
                                                <span class="flex-shrink-0 w-5 h-5 text-green-500">
                                                    <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                                <p class="ml-2 text-sm text-gray-700 dark:text-gray-300">Good conditions for recreational fishing</p>
                                            </div>
                                            <div class="flex items-start">
                                                <span class="flex-shrink-0 w-5 h-5 text-blue-500">
                                                    <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                                <p class="ml-2 text-sm text-gray-700 dark:text-gray-300">Rising tide - good for fishing near structures</p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($enableOutcomeLogging)
                                    <div id="outcome-card" class="hidden bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md border border-emerald-200/60 dark:border-emerald-900/40 space-y-4">
                                        <div>
                                            <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Log Trip Outcome
                                            </h4>
                                            <p class="text-xs text-gray-600 dark:text-gray-300">Every spot you tap on the map is saved below. Pick the one you actually sailed to, then tell us what really happened so the model keeps learning the right lessons.</p>
                                        </div>

                                        <div class="rounded-xl border border-emerald-100 dark:border-emerald-800/60 bg-emerald-50/60 dark:bg-emerald-900/20 p-3 space-y-3">
                                            <div id="planned-trips-empty" class="text-xs text-emerald-800 dark:text-emerald-200">
                                                Check a location on the map to add it here, then choose it before logging your outcome.
                                            </div>
                                            <ul id="planned-trips-list" class="hidden space-y-2"></ul>
                                        </div>

                                        <form id="outcome-form" class="space-y-3">
                                            <div>
                                                <label for="planned-trip-select" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">Which saved spot did you fish?</label>
                                                <select id="planned-trip-select" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900/60 text-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 dark:focus:border-emerald-400 dark:focus:ring-emerald-900/40" required>
                                                    <option value="">Select a saved spot…</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="outcome-select" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">Actual outcome offshore</label>
                                                <select id="outcome-select" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900/60 text-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 dark:focus:border-emerald-400 dark:focus:ring-emerald-900/40" required>
                                                    <option value="">What really happened…</option>
                                                    <option value="Safe">✅ Safe</option>
                                                    <option value="Caution">⚠️ Caution</option>
                                                    <option value="Dangerous">🚨 Dangerous</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="outcome-notes" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">Brief notes (optional)</label>
                                                <textarea id="outcome-notes" rows="3" placeholder="Quickly jot what you observed or changed on board…" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900/60 text-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 dark:focus:border-emerald-400 dark:focus:ring-emerald-900/40"></textarea>
                                            </div>
                                            <p id="outcome-status" class="text-xs text-gray-500 dark:text-gray-400">Select the saved spot you actually visited, then record the real conditions.</p>
                                            <button type="submit" id="outcome-submit" class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition disabled:opacity-70 disabled:cursor-not-allowed">
                                                Save Outcome
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md border border-emerald-200/60 dark:border-emerald-900/40">
                                        <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-3 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Log Trip Outcome
                                        </h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-300">Sign in to record trip outcomes and help improve the safety model.</p>
                                    </div>
                                    @endif

                                    @if($allowHistory)
                                    <div id="history-insights" class="hidden bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md border border-indigo-200/60 dark:border-indigo-900/40">
                                        <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-3 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2m0 2H7m0 0H2v-2a3 3 0 015.356-1.857M7 20v-2m0-5a3 3 0 116 0 3 3 0 01-6 0zm5-3V5a3 3 0 00-6 0v5"></path>
                                            </svg>
                                            Local History Insights
                                        </h4>
                                        <p id="history-status" class="text-sm text-gray-600 dark:text-gray-300">Searching previous checks near this location...</p>

                                        <div id="history-summary" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4 hidden">
                                            <div class="p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800">
                                                <p class="text-xs font-semibold uppercase text-green-700 dark:text-green-300">Safe</p>
                                                <p id="history-safe-count" class="text-xl font-bold text-green-900 dark:text-green-100">0</p>
                                            </div>
                                            <div class="p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-700">
                                                <p class="text-xs font-semibold uppercase text-yellow-700 dark:text-yellow-300">Caution</p>
                                                <p id="history-caution-count" class="text-xl font-bold text-yellow-900 dark:text-yellow-100">0</p>
                                            </div>
                                            <div class="p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800">
                                                <p class="text-xs font-semibold uppercase text-red-700 dark:text-red-300">Dangerous</p>
                                                <p id="history-danger-count" class="text-xl font-bold text-red-900 dark:text-red-100">0</p>
                                            </div>
                                            <div class="p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 sm:col-span-3">
                                                <p class="text-xs font-semibold uppercase text-blue-700 dark:text-blue-300">Average Confidence</p>
                                                <p id="history-average-confidence" class="text-xl font-bold text-blue-900 dark:text-blue-100">—</p>
                                                <p id="history-last-danger" class="text-xs text-blue-600 dark:text-blue-300 mt-1"></p>
                                            </div>
                                        </div>

                                        <div id="history-list" class="mt-4 space-y-3"></div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 text-center" id="last-updated">
                                    Last updated: <span id="update-time"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    @if($allowHistory && isset($recentLogs) && $recentLogs->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Recent Checks</h4>
                            <a href="{{ route('risk-history') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">View all</a>
                        </div>

                        <div class="space-y-3 max-h-80 overflow-y-auto pr-2">
                            @foreach ($recentLogs->take(5) as $log)
                            <div class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition">
                                <div class="mr-3">
                                    @php
                                    $riskColor = match(true) {
                                    str_contains(strtolower($log->result), 'high') => 'bg-red-500',
                                    str_contains(strtolower($log->result), 'medium') => 'bg-yellow-500',
                                    str_contains(strtolower($log->result), 'low') => 'bg-green-500',
                                    default => 'bg-gray-500'
                                    };
                                    @endphp
                                    <div class="{{ $riskColor }} w-3 h-3 rounded-full"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ $log->location }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->predicted_at->format('M d, g:i A') }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        @class([
                                            'bg-red-100 text-red-700 dark:bg-red-900/60 dark:text-red-200' => str_contains(strtolower($log->result), 'high'),
                                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/60 dark:text-yellow-200' => str_contains(strtolower($log->result), 'medium'),
                                            'bg-green-100 text-green-700 dark:bg-green-900/60 dark:text-green-200' => str_contains(strtolower($log->result), 'low'),
                                            'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' => ! str_contains(strtolower($log->result), 'high') && ! str_contains(strtolower($log->result), 'medium') && ! str_contains(strtolower($log->result), 'low'),
                                        ])">
                                    {{ $log->result }}
                                </span>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Check Another Location
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Safety Tips -->
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-white">
                        <h4 class="text-lg font-bold mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Safety Tips
                        </h4>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-start">
                                <span class="flex-shrink-0 mt-1">•</span>
                                <span class="ml-2">Always check the weather forecast before heading out</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 mt-1">•</span>
                                <span class="ml-2">Inform someone of your fishing plans and expected return time</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 mt-1">•</span>
                                <span class="ml-2">Carry essential safety equipment including life jackets and communication devices</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 mt-1">•</span>
                                <span class="ml-2">When in doubt, stay on shore - no fish is worth your life</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- API Status -->
            <div class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400">
                <span id="api-status" class="inline-flex items-center">
                    <svg class="w-3 h-3 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Weather API: Online
                </span>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mapContainer = document.getElementById('fishing-map');
            if (!mapContainer) {
                console.error('Fishing map container not found');
                return;
            }

            if (typeof L === 'undefined') {
                mapContainer.innerHTML = `<div class="flex items-center justify-center h-full text-sm font-semibold text-red-600 dark:text-red-400">
                    Unable to load map tiles. Check your internet connection and allow access to unpkg.com.
                </div>`;
                return;
            }

            const enableHistoryApi = "{{ $allowHistory ? '1' : '0' }}" === '1';
            const outcomeEnabled = "{{ $enableOutcomeLogging ? '1' : '0' }}" === '1';

            // Initialize map
            const map = L.map(mapContainer).setView([13.85, 120.62], 11);
            map.dragging.disable();

            // Add OpenStreetMap tiles with improved styling
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18,
            }).addTo(map);

            setTimeout(() => map.invalidateSize(), 250);

            // Allow right-click dragging for navigation by handling manual pan events
            const mapCanvas = map.getContainer();
            let isRightDragging = false;
            let lastPointerPosition = null;

            mapCanvas.addEventListener('contextmenu', event => event.preventDefault());

            mapCanvas.addEventListener('mousedown', event => {
                if (event.button === 2) {
                    isRightDragging = true;
                    lastPointerPosition = {
                        x: event.clientX,
                        y: event.clientY
                    };
                    mapCanvas.style.cursor = 'grabbing';
                }
            });

            mapCanvas.addEventListener('mousemove', event => {
                if (!isRightDragging || !lastPointerPosition) {
                    return;
                }

                event.preventDefault();
                const deltaX = event.clientX - lastPointerPosition.x;
                const deltaY = event.clientY - lastPointerPosition.y;

                if (deltaX !== 0 || deltaY !== 0) {
                    map.panBy([-deltaX, -deltaY], {
                        animate: false
                    });
                    lastPointerPosition = {
                        x: event.clientX,
                        y: event.clientY
                    };
                }
            });

            const stopRightDrag = () => {
                if (!isRightDragging) {
                    return;
                }

                isRightDragging = false;
                lastPointerPosition = null;
                mapCanvas.style.cursor = '';
            };

            mapCanvas.addEventListener('mouseup', event => {
                if (event.button === 2) {
                    stopRightDrag();
                }
            });

            mapCanvas.addEventListener('mouseleave', stopRightDrag);
            window.addEventListener('mouseup', event => {
                if (event.button === 2) {
                    stopRightDrag();
                }
            });

            // Custom marker icon function
            function createMarkerIcon(safetyLevel) {
                let color, borderColor;

                switch (safetyLevel.toLowerCase()) {
                    case 'safe':
                        color = '#4CAF50';
                        borderColor = '#2E7D32';
                        break;
                    case 'caution':
                        color = '#FF9800';
                        borderColor = '#E65100';
                        break;
                    case 'dangerous':
                        color = '#F44336';
                        borderColor = '#C62828';
                        break;
                    default:
                        color = '#9E9E9E';
                        borderColor = '#424242';
                }

                return L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="
                        background: ${color};
                        width: 24px;
                        height: 24px;
                        border-radius: 50%;
                        border: 2px solid ${borderColor};
                        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: bold;
                        font-size: 10px;
                    ">${safetyLevel.charAt(0)}</div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12],
                    popupAnchor: [0, -12]
                });
            }

            function sanitizeText(value) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                };

                return String(value ?? '').replace(/[&<>"']/g, char => map[char] || char);
            }

            // Store markers
            let markers = [];
            let currentMarker = null;

            // DOM elements
            const loadingOverlay = document.getElementById('loading-overlay');
            const defaultPanel = document.getElementById('default-panel');
            const resultPanel = document.getElementById('result-panel');
            const closeResultBtn = document.getElementById('close-result-btn');
            const currentLocationBtn = document.getElementById('current-location-btn');
            const safetyVerdict = document.getElementById('safety-verdict');
            const safetyIcon = document.getElementById('safety-icon');
            const confidenceLevel = document.getElementById('confidence-level');
            const locationName = document.getElementById('location-name');
            const coordinates = document.getElementById('coordinates');
            const windSpeed = document.getElementById('wind-speed');
            const waveHeight = document.getElementById('wave-height');
            const visibility = document.getElementById('visibility');
            const tideState = document.getElementById('tide-state');
            const marineNotice = document.getElementById('marine-notice');
            const waveCard = waveHeight ? waveHeight.closest('div') : null;
            const waveCardLabel = waveCard ? waveCard.querySelector('p') : null;
            const recommendations = document.getElementById('recommendations');
            const riskAreas = document.getElementById('risk-areas');
            const weatherAlertsSection = document.getElementById('weather-alerts-section');
            const weatherAlerts = document.getElementById('weather-alerts');
            const apiStatus = document.getElementById('api-status');
            const updateTime = document.getElementById('update-time');
            const historySection = document.getElementById('history-insights');
            const historyStatus = document.getElementById('history-status');
            const historySummary = document.getElementById('history-summary');
            const historySafeCount = document.getElementById('history-safe-count');
            const historyCautionCount = document.getElementById('history-caution-count');
            const historyDangerCount = document.getElementById('history-danger-count');
            const historyAverageConfidence = document.getElementById('history-average-confidence');
            const historyLastDanger = document.getElementById('history-last-danger');
            const historyList = document.getElementById('history-list');
            const outcomeCard = document.getElementById('outcome-card');
            const outcomeForm = document.getElementById('outcome-form');
            const plannedTripsList = document.getElementById('planned-trips-list');
            const plannedTripsEmpty = document.getElementById('planned-trips-empty');
            const plannedTripSelect = document.getElementById('planned-trip-select');
            const outcomeSelect = document.getElementById('outcome-select');
            const outcomeNotes = document.getElementById('outcome-notes');
            const outcomeStatus = document.getElementById('outcome-status');
            const outcomeSubmit = document.getElementById('outcome-submit');
            let historyRequestToken = 0;
            let lastPrediction = null;
            const outcomeStatusDefault = 'Select the saved spot you actually visited, then record the real conditions.';
            const plannedTripsStorageKey = 'fishingSafetyPlannedTrips';
            let plannedTrips = [];

            function persistPlannedTrips() {
                if (!outcomeEnabled) {
                    return;
                }

                try {
                    localStorage.setItem(plannedTripsStorageKey, JSON.stringify(plannedTrips));
                } catch (error) {
                    console.warn('Unable to persist planned trips', error);
                }
            }

            function renderPlannedTrips(focusTripId = null) {
                if (!outcomeEnabled || !plannedTripSelect) {
                    return;
                }

                const baseOption = '<option value="">Select a saved spot…</option>';
                if (!Array.isArray(plannedTrips)) {
                    plannedTrips = [];
                }

                const hasTrips = plannedTrips.length > 0;

                if (plannedTripsEmpty) {
                    plannedTripsEmpty.classList.toggle('hidden', hasTrips);
                }

                if (plannedTripsList) {
                    if (!hasTrips) {
                        plannedTripsList.classList.add('hidden');
                        plannedTripsList.innerHTML = '';
                    } else {
                        plannedTripsList.classList.remove('hidden');
                        plannedTripsList.innerHTML = plannedTrips.map(trip => {
                            const verdictLabel = sanitizeText(trip.verdict || 'Unknown');
                            const confidenceLabel = typeof trip.confidence === 'number'
                                ? `${Math.round(trip.confidence * 100)}%`
                                : '—';
                            const coordinateLabel = `${Number(trip.coordinates?.lat ?? 0).toFixed(3)}, ${Number(trip.coordinates?.lng ?? 0).toFixed(3)}`;
                            const savedTime = trip.savedAt ? new Date(trip.savedAt).toLocaleTimeString() : '';
                            const metaParts = [
                                verdictLabel,
                                confidenceLabel !== '—' ? `Conf ${confidenceLabel}` : null,
                                coordinateLabel,
                                savedTime ? `Saved ${savedTime}` : null,
                            ].filter(Boolean);

                            return `
                                <li class="flex items-start justify-between rounded-lg border border-emerald-100 dark:border-emerald-900/60 bg-white dark:bg-gray-900/50 p-3">
                                    <button type="button" class="text-left flex-1" data-select-trip="${trip.id}">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">${sanitizeText(trip.locationLabel || coordinateLabel)}</p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400">${metaParts.join(' • ')}</p>
                                    </button>
                                    <button type="button" class="ml-3 text-[11px] font-semibold text-red-600 hover:text-red-700 dark:text-red-300 dark:hover:text-red-200" data-remove-trip="${trip.id}">
                                        Remove
                                    </button>
                                </li>
                            `;
                        }).join('');
                    }
                }

                const options = hasTrips
                    ? plannedTrips.map(trip => `<option value="${trip.id}">${sanitizeText(trip.locationLabel || `${Number(trip.coordinates?.lat ?? 0).toFixed(3)}, ${Number(trip.coordinates?.lng ?? 0).toFixed(3)}`)} • ${sanitizeText(trip.verdict || 'Unknown')}</option>`).join('')
                    : '';

                plannedTripSelect.innerHTML = `${baseOption}${options}`;
                plannedTripSelect.disabled = !hasTrips;

                if (outcomeSubmit) {
                    outcomeSubmit.disabled = !hasTrips;
                    if (!hasTrips) {
                        outcomeSubmit.innerHTML = 'Save Outcome';
                    }
                }

                if (!hasTrips) {
                    plannedTripSelect.value = '';
                } else if (focusTripId) {
                    plannedTripSelect.value = focusTripId;
                }
            }

            function loadPlannedTrips() {
                if (!outcomeEnabled) {
                    return;
                }

                try {
                    const raw = localStorage.getItem(plannedTripsStorageKey);
                    const parsed = raw ? JSON.parse(raw) : [];
                    plannedTrips = Array.isArray(parsed) ? parsed : [];
                } catch (error) {
                    plannedTrips = [];
                    console.warn('Unable to load planned trips', error);
                }

                renderPlannedTrips();
            }

            function findPlannedTrip(tripId) {
                return plannedTrips.find(trip => trip.id === tripId);
            }

            function removePlannedTrip(tripId) {
                if (!outcomeEnabled) {
                    return;
                }

                plannedTrips = plannedTrips.filter(trip => trip.id !== tripId);
                persistPlannedTrips();
                renderPlannedTrips();
            }

            function addPlannedTripFromPrediction(predictionData) {
                if (!outcomeEnabled || !predictionData || !predictionData.feature_vector || !plannedTripSelect) {
                    return null;
                }

                const latValue = Number(predictionData.location?.latitude ?? 0);
                const lonValue = Number(predictionData.location?.longitude ?? 0);
                const coordinateLabel = `${latValue.toFixed(4)}, ${lonValue.toFixed(4)}`;
                const locationLabel = predictionData.location?.name || coordinateLabel;
                const locationKey = `${latValue.toFixed(4)}|${lonValue.toFixed(4)}`;

                const tripId = `trip-${Date.now()}-${Math.random().toString(16).slice(2, 8)}`;

                plannedTrips = plannedTrips.filter(trip => trip.locationKey !== locationKey);

                plannedTrips.unshift({
                    id: tripId,
                    locationLabel,
                    locationKey,
                    coordinates: {
                        lat: latValue,
                        lng: lonValue,
                    },
                    verdict: predictionData.safety_assessment?.verdict || 'Unknown',
                    confidence: predictionData.safety_assessment?.confidence ?? null,
                    savedAt: new Date().toISOString(),
                    feature_vector: predictionData.feature_vector,
                    api_verdict: predictionData.safety_assessment?.verdict || 'Unknown',
                    is_marine: Boolean(predictionData.is_marine_location ?? predictionData.location?.is_marine),
                });

                if (plannedTrips.length > 10) {
                    plannedTrips = plannedTrips.slice(0, 10);
                }

                persistPlannedTrips();
                renderPlannedTrips(tripId);

                return tripId;
            }

            // Set current time
            updateTime.textContent = new Date().toLocaleTimeString();

            // Show loading state
            function showLoading() {
                loadingOverlay.classList.remove('hidden');
            }

            // Hide loading state
            function hideLoading() {
                loadingOverlay.classList.add('hidden');
            }

            // Show default panel
            function showDefaultPanel() {
                defaultPanel.classList.remove('hidden');
                resultPanel.classList.add('hidden');
                if (historySection) {
                    historySection.classList.add('hidden');
                }
                if (outcomeCard) {
                    outcomeCard.classList.add('hidden');
                }
            }

            // Show result panel
            function showResultPanel() {
                defaultPanel.classList.add('hidden');
                resultPanel.classList.remove('hidden');
            }

            // Close result panel
            closeResultBtn.addEventListener('click', showDefaultPanel);

            function setOutcomeStatus(message, tone = 'info') {
                if (!outcomeStatus) {
                    return;
                }

                const toneClass = {
                    info: 'text-gray-500 dark:text-gray-400',
                    success: 'text-emerald-600 dark:text-emerald-300',
                    error: 'text-red-600 dark:text-red-300',
                    warning: 'text-yellow-600 dark:text-yellow-300',
                };

                outcomeStatus.className = `text-xs ${toneClass[tone] || toneClass.info}`;
                outcomeStatus.textContent = message;
            }

            function resetOutcomeForm() {
                if (!outcomeEnabled || !outcomeCard || !outcomeForm) {
                    return;
                }

                if (plannedTripSelect) {
                    plannedTripSelect.value = '';
                }

                if (outcomeSelect) {
                    outcomeSelect.value = '';
                }

                if (outcomeNotes) {
                    outcomeNotes.value = '';
                }

                if (outcomeSubmit) {
                    outcomeSubmit.disabled = false;
                    outcomeSubmit.innerHTML = 'Save Outcome';
                }

                setOutcomeStatus(outcomeStatusDefault, 'info');
                delete outcomeForm.dataset.logged;
            }

            function prepareOutcomeLogging(predictionData) {
                if (!outcomeEnabled || !outcomeCard || !outcomeForm) {
                    return;
                }

                lastPrediction = predictionData;
                resetOutcomeForm();
                const newTripId = addPlannedTripFromPrediction(predictionData);
                outcomeCard.classList.remove('hidden');

                if (!newTripId) {
                    setOutcomeStatus('This spot could not be saved for logging yet. Tap it again once the prediction finishes loading.', 'warning');
                    return;
                }

                if (predictionData?.location) {
                    const label = predictionData.location.name || `${Number(predictionData.location.latitude ?? 0).toFixed(3)}, ${Number(predictionData.location.longitude ?? 0).toFixed(3)}`;
                    setOutcomeStatus(`Saved ${label} in your trip list. Once you return, pick it and log what really happened.`, 'info');
                } else {
                    setOutcomeStatus('Saved this spot in your trip list. Select it later and record what really happened.', 'info');
                }

                if (plannedTripSelect) {
                    plannedTripSelect.value = newTripId;
                }
            }

            // Update safety result display
            function updateSafetyResult(data) {
                // Update location info
                const latValue = Number(data.location.latitude ?? 0);
                const lonValue = Number(data.location.longitude ?? 0);
                const isMarineLocation = Boolean(data.is_marine_location ?? data.location?.is_marine);
                const coordinateLabel = `${latValue.toFixed(4)}, ${lonValue.toFixed(4)}`;
                const locationLabel = data.location.name || coordinateLabel;

                locationName.textContent = locationLabel;
                coordinates.textContent = isMarineLocation
                    ? `${coordinateLabel} (coastal water point)`
                    : `${coordinateLabel} (land point)`;
                if (marineNotice) {
                    marineNotice.classList.toggle('hidden', isMarineLocation);
                }
                if (marineNotice && !isMarineLocation) {
                    marineNotice.textContent = 'This point is on land; marine data limited.';
                }
                if (waveCard) {
                    waveCard.classList.toggle('on-land-card', !isMarineLocation);
                }
                if (waveCardLabel) {
                    waveCardLabel.textContent = isMarineLocation ? 'Wave Height' : 'Land Conditions';
                }

                // Update safety verdict and styling
                safetyVerdict.textContent = data.safety_assessment.verdict;
                confidenceLevel.textContent = `Confidence: ${(data.safety_assessment.confidence * 100).toFixed(1)}%`;
                if (data.safety_assessment.override_reasons && data.safety_assessment.override_reasons.length > 0) {
                    confidenceLevel.textContent += ' • Severe weather override active';
                }
                if (data.typhoon_active) {
                    confidenceLevel.textContent += ' • Typhoon bulletin active';
                } else if (data.severe_alert_present) {
                    confidenceLevel.textContent += ' • Weather alert issued';
                }

                // Set icon and color based on verdict
                let icon, colorClass;
                switch (data.safety_assessment.verdict.toLowerCase()) {
                    case 'safe':
                        icon = '✅';
                        colorClass = 'text-green-600 dark:text-green-400';
                        break;
                    case 'caution':
                        icon = '⚠️';
                        colorClass = 'text-yellow-600 dark:text-yellow-400';
                        break;
                    case 'dangerous':
                        icon = '🚨';
                        colorClass = 'text-red-600 dark:text-red-400';
                        break;
                    default:
                        icon = '❓';
                        colorClass = 'text-gray-600 dark:text-gray-400';
                }

                safetyIcon.innerHTML = icon;
                safetyVerdict.className = `text-3xl font-bold ${colorClass}`;

                // Update weather conditions
                windSpeed.textContent = `${Number(data.weather_conditions.wind_speed_kph ?? 0).toFixed(1)} km/h`;
                waveHeight.textContent = isMarineLocation
                    ? `${Number(data.weather_conditions.wave_height_m ?? 0).toFixed(1)} m`
                    : 'Not applicable';
                visibility.textContent = `${Number(data.weather_conditions.visibility_km ?? 0).toFixed(1)} km`;
                tideState.textContent = data.weather_conditions.tide_state;

                // Update recommendations
                let recommendationsHTML = '';
                data.recommendations.forEach(rec => {
                    let icon = '✓';
                    let color = 'text-green-500';

                    if (rec.includes('🚨') || rec.includes('⚠️') || rec.includes('💨') || rec.includes('🌊') || rec.includes('🌧️') || rec.includes('🌫️')) {
                        icon = rec.charAt(0);
                    }

                    if (rec.includes('DO NOT GO') || rec.includes('dangerous')) {
                        color = 'text-red-500';
                    } else if (rec.includes('caution') || rec.includes('Exercise extreme caution')) {
                        color = 'text-yellow-500';
                    }

                    recommendationsHTML += `
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-5 h-5 ${color}">
                                <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            <p class="ml-2 text-sm text-gray-700 dark:text-gray-300">${rec}</p>
                        </div>
                    `;
                });
                recommendations.innerHTML = recommendationsHTML;

                // Update risk areas
                if (data.historical_data.risk_areas && data.historical_data.risk_areas.length > 0) {
                    let riskHTML = '<div class="space-y-2">';
                    data.historical_data.risk_areas.forEach(area => {
                        riskHTML += `
                            <div class="flex items-start text-sm">
                                <span class="flex-shrink-0 mt-0.5 text-yellow-500">•</span>
                                <span class="ml-2">${area.name} - ${area.distance_km.toFixed(1)}km away (${area.incidents} incidents)</span>
                            </div>
                        `;
                    });
                    riskHTML += '</div>';
                    riskAreas.innerHTML = riskHTML;
                } else {
                    riskAreas.innerHTML = '<p class="text-sm text-green-700 dark:text-green-200">No high-risk areas detected within 10km radius</p>';
                }

                if (weatherAlertsSection && weatherAlerts) {
                    const alerts = Array.isArray(data.weather_alerts) ? data.weather_alerts : [];
                    if (alerts.length) {
                        weatherAlertsSection.classList.remove('hidden');
                        const alertCards = alerts.map(alert => {
                            const severity = (alert.severity || '').toLowerCase();
                            let badgeClass = 'bg-gray-200 text-gray-800';
                            if (severity === 'warning') {
                                badgeClass = 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-200';
                            } else if (severity === 'watch') {
                                badgeClass = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-200';
                            } else if (severity === 'advisory') {
                                badgeClass = 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200';
                            }

                            const startLabel = alert.start ? new Date(alert.start * 1000).toLocaleString() : '—';
                            const endLabel = alert.end ? new Date(alert.end * 1000).toLocaleString() : '—';
                            const sourceLabel = sanitizeText(alert.source || 'Official bulletin');
                            const title = `${alert.is_typhoon ? '🌀 ' : ''}${sanitizeText(alert.title || 'Weather Alert')}`;
                            const description = sanitizeText(alert.description || '');

                            return `
                                <div class="border border-red-100 dark:border-red-900/40 rounded-lg p-3 bg-white/90 dark:bg-gray-800/70 shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-1 text-[11px] font-semibold rounded-full ${badgeClass}">${sanitizeText(alert.severity || 'Alert')}</span>
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400">${sourceLabel}</span>
                                    </div>
                                    <p class="mt-2 font-semibold text-sm text-gray-800 dark:text-gray-100">${title}</p>
                                    ${description ? `<p class="mt-1 text-xs text-gray-600 dark:text-gray-300 leading-relaxed">${description}</p>` : ''}
                                    <div class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                                        <span>Valid: ${startLabel} – ${endLabel}</span>
                                    </div>
                                </div>
                            `;
                        }).join('');

                        weatherAlerts.innerHTML = alertCards;
                    } else {
                        weatherAlertsSection.classList.add('hidden');
                        weatherAlerts.innerHTML = '';
                    }
                }

                // Update timestamp
                updateTime.textContent = new Date().toLocaleTimeString();

                if (outcomeEnabled) {
                    prepareOutcomeLogging(data);
                }

                // Add result to history
                addToHistory(data);
            }

            // Add result to history in local storage
            function addToHistory(data) {
                let history = JSON.parse(localStorage.getItem('fishingSafetyHistory') || '[]');

                const newEntry = {
                    location: data.location.name || `${data.location.latitude.toFixed(4)}, ${data.location.longitude.toFixed(4)}`,
                    coordinates: {
                        lat: data.location.latitude,
                        lng: data.location.longitude
                    },
                    verdict: data.safety_assessment.verdict,
                    confidence: data.safety_assessment.confidence,
                    timestamp: new Date().toISOString(),
                    wind: data.weather_conditions.wind_speed_kph,
                    waves: data.weather_conditions.wave_height_m,
                    isMarine: Boolean(data.is_marine_location ?? data.location?.is_marine)
                };

                // Add to beginning of array
                history.unshift(newEntry);

                // Keep only last 10 entries
                if (history.length > 10) {
                    history = history.slice(0, 10);
                }

                localStorage.setItem('fishingSafetyHistory', JSON.stringify(history));
            }

            function resetHistorySection(message) {
                if (!enableHistoryApi || !historySection) {
                    return;
                }

                historySection.classList.remove('hidden');
                historySummary?.classList.add('hidden');

                if (historyList) {
                    historyList.innerHTML = '';
                }

                if (historyStatus) {
                    historyStatus.textContent = message || 'Searching previous checks near this location...';
                }

                if (historyAverageConfidence) {
                    historyAverageConfidence.textContent = '—';
                }

                if (historyLastDanger) {
                    historyLastDanger.textContent = '';
                }
            }

            async function loadHistoricalInsights(lat, lng) {
                if (!enableHistoryApi || !historySection) {
                    return;
                }

                const token = ++historyRequestToken;
                resetHistorySection('Loading recent checks in this area...');

                try {
                    const params = new URLSearchParams({
                        lat: lat.toString(),
                        lon: lng.toString(),
                        radius_km: '8',
                        limit: '20',
                    });

                    const response = await fetch(`/api/fishing-safety/history?${params.toString()}`);

                    if (token !== historyRequestToken) {
                        return;
                    }

                    if (!response.ok) {
                        throw new Error('History request failed');
                    }

                    const historyData = await response.json();
                    renderHistoryInsights(historyData);
                } catch (error) {
                    if (token !== historyRequestToken) {
                        return;
                    }

                    console.error('Error loading history insights:', error);
                    resetHistorySection('Unable to load recent history for this area. Please try again later.');
                }
            }

            function renderHistoryInsights(historyData) {
                if (!historySection) {
                    return;
                }

                const logs = Array.isArray(historyData?.logs) ? historyData.logs : [];
                const summary = historyData?.summary || {};

                if (!logs.length) {
                    resetHistorySection('No recorded fishing safety checks nearby yet. Be the first to log one!');
                    return;
                }

                if (historyStatus) {
                    historyStatus.textContent = `Showing the last ${logs.length} checks within ${historyData?.radius_used ?? 0} km.`;
                }

                historySummary?.classList.remove('hidden');

                if (historySafeCount) {
                    historySafeCount.textContent = summary?.counts?.safe ?? 0;
                }

                if (historyCautionCount) {
                    historyCautionCount.textContent = summary?.counts?.caution ?? 0;
                }

                if (historyDangerCount) {
                    historyDangerCount.textContent = summary?.counts?.dangerous ?? 0;
                }

                if (historyAverageConfidence) {
                    const avg = summary?.average_confidence_percent;
                    historyAverageConfidence.textContent = typeof avg === 'number' ? `${avg.toFixed(1)}%` : '—';
                }

                if (historyLastDanger) {
                    const lastDanger = summary?.last_dangerous_at;
                    historyLastDanger.textContent = lastDanger ?
                        `Most recent dangerous conditions logged ${new Date(lastDanger).toLocaleString()}` :
                        'No dangerous readings recorded in this window.';
                }

                if (!historyList) {
                    return;
                }

                const entries = logs.slice(0, 5).map((log) => {
                    const confidencePercent = typeof log.confidence === 'number' ?
                        `${Math.round(log.confidence * 100)}%` :
                        '—';
                    const distanceLabel = typeof log.distance_km === 'number' ?
                        `${log.distance_km.toFixed(1)} km` :
                        '—';
                    const timestamp = log.predicted_at ? new Date(log.predicted_at).toLocaleString() : 'Unknown time';
                    const extraFlags = [...(log.override_reasons || []), ...(log.environmental_flags || [])];
                    const waveDisplay = log.is_marine === false
                        ? 'Waves: N/A (land point)'
                        : `Waves: ${Number(log.wave_height_m ?? 0).toFixed(1)} m`;

                    const severityClass = (() => {
                        const verdict = (log.verdict || '').toLowerCase();
                        if (verdict.includes('danger')) return 'text-red-600 dark:text-red-300';
                        if (verdict.includes('caution')) return 'text-yellow-600 dark:text-yellow-300';
                        return 'text-green-600 dark:text-green-300';
                    })();

                    return `
                        <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white/80 dark:bg-gray-800/80">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold ${severityClass}">${log.verdict ?? 'Unknown'}</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">${distanceLabel}</span>
                            </div>
                            <div class="mt-1 text-xs text-gray-600 dark:text-gray-300 flex flex-wrap gap-2">
                                <span>Wind: ${Number(log.wind_speed_kph ?? 0).toFixed(1)} km/h</span>
                                <span>${waveDisplay}</span>
                                <span>Confidence: ${confidencePercent}</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">${timestamp}</p>
                            ${extraFlags.length ? `<ul class="mt-2 text-xs text-orange-600 dark:text-orange-300 list-disc list-inside">${extraFlags.slice(0, 3).map(flag => `<li>${flag}</li>`).join('')}</ul>` : ''}
                        </div>
                    `;
                }).join('');

                historyList.innerHTML = entries;
            }

            // Check fishing safety for coordinates
            async function checkFishingSafety(lat, lng) {
                showLoading();

                try {
                    const response = await fetch('/api/fishing-safety/', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            lat,
                            lon: lng
                        })
                    });

                    const data = await response.json();

                    if (data.error) {
                        throw new Error(data.error);
                    }

                    const isMarine = Boolean(data.is_marine_location ?? data.location?.is_marine);

                    // Remove previous marker if exists
                    if (currentMarker) {
                        map.removeLayer(currentMarker);
                    }

                    // Add new marker
                    currentMarker = L.marker([lat, lng], {
                        icon: createMarkerIcon(data.safety_assessment.verdict)
                    }).addTo(map);

                    const verdictLabel = data.safety_assessment?.verdict || 'Unknown';
                    const severity = verdictLabel.toLowerCase();
                    const confidencePercent = typeof data.safety_assessment?.confidence === 'number'
                        ? `${(data.safety_assessment.confidence * 100).toFixed(0)}%`
                        : '—';

                    let badgeClass = 'bg-gray-100 text-gray-700';
                    let borderClass = 'border-blue-200';
                    let headlineClass = 'text-gray-800';
                    if (severity.includes('danger')) {
                        badgeClass = 'bg-red-100 text-red-700';
                        borderClass = 'border-red-200';
                        headlineClass = 'text-red-700';
                    } else if (severity.includes('caution')) {
                        badgeClass = 'bg-yellow-100 text-yellow-700';
                        borderClass = 'border-yellow-200';
                        headlineClass = 'text-yellow-700';
                    } else if (severity.includes('safe')) {
                        badgeClass = 'bg-green-100 text-green-700';
                        borderClass = 'border-green-200';
                        headlineClass = 'text-green-700';
                    }

                    const locationLabel = sanitizeText(
                        data.location?.name || `${lat.toFixed(4)}, ${lng.toFixed(4)}`
                    );

                    const windValue = `${Number(data.weather_conditions.wind_speed_kph ?? 0).toFixed(1)} km/h`;
                    const waveValue = isMarine
                        ? `${Number(data.weather_conditions.wave_height_m ?? 0).toFixed(1)} m`
                        : 'Not applicable';
                    const waveLabel = isMarine ? 'Waves' : 'Land';
                    const waveBlockClass = isMarine
                        ? 'rounded-lg bg-purple-600 px-2 py-1.5 text-center'
                        : 'rounded-lg bg-gray-500 px-2 py-1.5 text-center';
                    const waveLabelClass = isMarine
                        ? 'text-[10px] font-semibold uppercase text-purple-100'
                        : 'text-[10px] font-semibold uppercase text-gray-200';
                    const waveValueClass = isMarine
                        ? 'text-xs font-bold text-white'
                        : 'text-xs font-semibold text-gray-100';
                    const landNote = isMarine
                        ? ''
                        : '<p class="mt-2 text-[10px] text-gray-600">Land point - marine values limited.</p>';

                    const popupContent = `
                        <div class="p-3 rounded-xl border ${borderClass} bg-white/95 shadow-sm min-w-[220px]">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-1 text-[11px] font-semibold uppercase tracking-wide rounded-full ${badgeClass}">${sanitizeText(verdictLabel)}</span>
                                <span class="text-[11px] text-gray-500">Confidence ${confidencePercent}</span>
                            </div>
                            <h3 class="mt-2 text-sm font-bold ${headlineClass}">${locationLabel}</h3>
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <div class="rounded-lg bg-blue-600 px-2 py-1.5 text-center">
                                    <p class="text-[10px] font-semibold uppercase text-blue-100">Wind</p>
                                    <p class="text-xs font-bold text-white">${sanitizeText(windValue)}</p>
                                </div>
                                <div class="${waveBlockClass}">
                                    <p class="${waveLabelClass}">${sanitizeText(waveLabel)}</p>
                                    <p class="${waveValueClass}">${sanitizeText(waveValue)}</p>
                                </div>
                            </div>
                            ${landNote}
                        </div>
                    `;

                    currentMarker.bindPopup(popupContent).openPopup();

                    // Update the result panel
                    updateSafetyResult(data);
                    if (enableHistoryApi) {
                        loadHistoricalInsights(lat, lng);
                    } else if (historySection) {
                        historySection.classList.add('hidden');
                    }
                    showResultPanel();

                } catch (error) {
                    console.error('Error checking fishing safety:', error);
                    alert(`Error: ${error.message}. Please try again later.`);
                } finally {
                    hideLoading();
                }
            }

            if (outcomeEnabled && outcomeForm) {
                outcomeForm.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    if (outcomeSubmit?.disabled) {
                        return;
                    }

                    const selectedTripId = plannedTripSelect?.value || '';
                    if (!selectedTripId) {
                        setOutcomeStatus('Select the saved spot you actually visited before logging.', 'warning');
                        return;
                    }

                    const plannedTrip = findPlannedTrip(selectedTripId);

                    if (!plannedTrip) {
                        setOutcomeStatus('That saved spot is no longer available. Check the map again and save it before logging.', 'error');
                        renderPlannedTrips();
                        return;
                    }

                    if (!plannedTrip.feature_vector) {
                        setOutcomeStatus('This saved spot is missing model data. Tap it on the map again before logging the outcome.', 'error');
                        return;
                    }

                    const selectedOutcome = outcomeSelect?.value;

                    if (!selectedOutcome) {
                        setOutcomeStatus('Pick what actually happened on the water.', 'warning');
                        return;
                    }

                    const payload = {
                        lat: Number(plannedTrip.coordinates?.lat ?? 0),
                        lon: Number(plannedTrip.coordinates?.lng ?? 0),
                        outcome: selectedOutcome,
                        api_verdict: plannedTrip.api_verdict ?? 'Unknown',
                        features: plannedTrip.feature_vector,
                    };

                    const notesValue = outcomeNotes?.value?.trim();
                    if (notesValue) {
                        payload.notes = notesValue;
                    }

                    setOutcomeStatus('Saving outcome…', 'info');
                    if (outcomeSubmit) {
                        outcomeSubmit.disabled = true;
                        outcomeSubmit.innerHTML = 'Saving…';
                    }

                    try {
                        const response = await fetch('/api/fishing-safety/record-outcome', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        });

                        if (!response.ok) {
                            const errorPayload = await response.json().catch(() => ({}));
                            throw new Error(errorPayload.error || 'Failed to record outcome');
                        }

                        const result = await response.json();
                        removePlannedTrip(selectedTripId);

                        if (plannedTripSelect) {
                            plannedTripSelect.value = '';
                        }

                        if (outcomeSelect) {
                            outcomeSelect.value = '';
                        }

                        if (outcomeNotes) {
                            outcomeNotes.value = '';
                        }

                        setOutcomeStatus(result.message || 'Outcome recorded. Retrain soon to learn from it.', 'success');
                    } catch (error) {
                        console.error('Failed to record outcome:', error);
                        setOutcomeStatus(error.message || 'Unable to record outcome right now.', 'error');
                    } finally {
                        if (outcomeSubmit) {
                            outcomeSubmit.disabled = false;
                            outcomeSubmit.innerHTML = 'Save Outcome';
                        }
                    }
                });
            }

            if (plannedTripsList) {
                plannedTripsList.addEventListener('click', (event) => {
                    const removeButton = event.target.closest('[data-remove-trip]');
                    if (removeButton) {
                        removePlannedTrip(removeButton.dataset.removeTrip);
                        if (plannedTripSelect) {
                            plannedTripSelect.value = '';
                        }
                        if (plannedTrips.length) {
                            setOutcomeStatus('Removed that spot from your list. Tap a new location to add another.', 'info');
                        } else {
                            setOutcomeStatus('No saved spots right now. Tap the map to add one before logging.', 'info');
                        }
                        return;
                    }

                    const selectButton = event.target.closest('[data-select-trip]');
                    if (selectButton && plannedTripSelect) {
                        plannedTripSelect.value = selectButton.dataset.selectTrip;
                        plannedTripSelect.dispatchEvent(new Event('change'));
                    }
                });
            }

            plannedTripSelect?.addEventListener('change', () => {
                const tripId = plannedTripSelect.value;

                if (!tripId) {
                    setOutcomeStatus(outcomeStatusDefault, 'info');
                    return;
                }

                const selectedTrip = findPlannedTrip(tripId);
                if (selectedTrip) {
                    const label = selectedTrip.locationLabel
                        || `${Number(selectedTrip.coordinates?.lat ?? 0).toFixed(3)}, ${Number(selectedTrip.coordinates?.lng ?? 0).toFixed(3)}`;
                    setOutcomeStatus(`Locked in ${label}. Record what actually happened before you forget.`, 'info');
                }
            });

            // Handle map clicks
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                // Validate coordinates are in Philippines area
                if (lat < 5.0 || lat > 20.0 || lng < 116.0 || lng > 127.0) {
                    alert('Please select a location within the Philippines coastal area.');
                    return;
                }

                checkFishingSafety(lat, lng);
            });

            // Handle sample location clicks
            document.querySelectorAll('[data-lat]').forEach(button => {
                button.addEventListener('click', function() {
                    const lat = parseFloat(this.getAttribute('data-lat'));
                    const lng = parseFloat(this.getAttribute('data-lng'));
                    const targetZoom = Math.max(map.getZoom(), 13);
                    map.setView([lat, lng], targetZoom);
                    checkFishingSafety(lat, lng);
                });
            });

            // Current location button
            if (!currentLocationBtn) {
                console.warn('Current location button not found in DOM.');
            }

            currentLocationBtn?.addEventListener('click', function() {
                currentLocationBtn.disabled = true;
                currentLocationBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m1.969-9A8.001 8.001 0 0119.418 9m-15.356 2H9m0 0h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9"></path></svg> Locating...';

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            // Check if within Philippines area
                            if (lat < 5.0 || lat > 20.0 || lng < 116.0 || lng > 127.0) {
                                alert('Your current location is outside the Philippines coastal area. Please select a location on the map instead.');
                                resetButton();
                                return;
                            }

                            map.setView([lat, lng], 13);
                            const checkPromise = checkFishingSafety(lat, lng);
                            if (checkPromise && typeof checkPromise.finally === 'function') {
                                checkPromise.finally(resetButton);
                            } else {
                                resetButton();
                            }
                        },
                        function(error) {
                            console.error('Geolocation error:', error);
                            alert('Unable to get your location. Please enable location services and try again.');
                            resetButton();
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000
                        }
                    );
                } else {
                    alert('Geolocation is not supported by your browser.');
                    resetButton();
                }

                function resetButton() {
                    currentLocationBtn.disabled = false;
                    currentLocationBtn.innerHTML = `
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        My Location
                    `;
                }
            });

            // Check API status
            async function checkApiStatus() {
                try {
                    const response = await fetch('/api/fishing-safety/health');
                    if (response.ok) {
                        apiStatus.innerHTML = `
                            <svg class="w-3 h-3 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            API: Online
                        `;
                    } else {
                        throw new Error('API offline');
                    }
                } catch (error) {
                    apiStatus.innerHTML = `
                        <svg class="w-3 h-3 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v4m0-12l-3-3m3 3l3-3m-6 8h12M4 12h16"></path>
                        </svg>
                        API: Offline
                    `;
                }
            }

            if (outcomeEnabled) {
                loadPlannedTrips();
            }

            // Initialize
            checkApiStatus();
            setInterval(checkApiStatus, 60000); // Check every minute

            // Show default panel on load
            showDefaultPanel();
        });
    </script>
    @endpush
</x-app-layout>