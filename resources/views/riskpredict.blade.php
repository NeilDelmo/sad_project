<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl text-gray-800 dark:text-gray-100 leading-tight">
                    🌊 Fishing Risk Prediction
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">AI-powered safety assessment for your fishing trips</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 shadow-xl rounded-2xl overflow-hidden">
                <!-- Header Banner -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                    <h3 class="text-white text-2xl font-bold mb-2">Weather & Environment Assessment</h3>
                    <p class="text-blue-100">Enter the current conditions to get a risk prediction</p>
                </div>

                <div class="p-8">
                    <form action="{{ route('predict-risk') }}" method="POST" class="space-y-8">
                        @csrf

                        <!-- Weather Section -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                                <span class="inline-block w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3">1</span>
                                Weather Conditions
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Wind Speed -->
                                <div class="relative">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        💨 Wind Speed
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" step="0.1" name="wind_speed_kph" 
                                            class="w-full px-4 py-3 pr-12 rounded-lg border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition" 
                                            placeholder="e.g., 15.5" required>
                                        <span class="absolute right-4 top-3 text-gray-500 font-medium">kph</span>
                                    </div>
                                </div>

                                <!-- Wave Height -->
                                <div class="relative">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        🌊 Wave Height
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" step="0.1" name="wave_height_m" 
                                            class="w-full px-4 py-3 pr-12 rounded-lg border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition" 
                                            placeholder="e.g., 2.5" required>
                                        <span class="absolute right-4 top-3 text-gray-500 font-medium">m</span>
                                    </div>
                                </div>

                                <!-- Rainfall -->
                                <div class="relative">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        🌧️ Rainfall
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" step="0.1" name="rainfall_mm" 
                                            class="w-full px-4 py-3 pr-12 rounded-lg border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition" 
                                            placeholder="e.g., 10" required>
                                        <span class="absolute right-4 top-3 text-gray-500 font-medium">mm</span>
                                    </div>
                                </div>

                                <!-- Visibility -->
                                <div class="relative">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        👁️ Visibility
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" step="0.1" name="visibility_km" 
                                            class="w-full px-4 py-3 pr-12 rounded-lg border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition" 
                                            placeholder="e.g., 5.0" required>
                                        <span class="absolute right-4 top-3 text-gray-500 font-medium">km</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ocean & Location Section -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                                <span class="inline-block w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center mr-3">2</span>
                                Ocean & Location Data
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Tide Level -->
                                <div class="relative">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        🌊 Tide Level
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" step="0.1" name="tide_level_m" 
                                            class="w-full px-4 py-3 pr-12 rounded-lg border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition" 
                                            placeholder="e.g., 1.2" required>
                                        <span class="absolute right-4 top-3 text-gray-500 font-medium">m</span>
                                    </div>
                                </div>

                                <!-- Moon Phase -->
                                <div class="relative">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        🌙 Moon Phase
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select name="moon_phase" 
                                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition" 
                                        required>
                                        <option value="">Select moon phase...</option>
                                        <option value="0">🌑 New Moon (0)</option>
                                        <option value="1">🌙 Waxing Crescent (1)</option>
                                        <option value="2">🌕 Full Moon (2)</option>
                                        <option value="3">🌗 Waning Crescent (3)</option>
                                    </select>
                                </div>

                                <!-- Location -->
                                <div class="relative md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        📍 Fishing Location
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select name="location" 
                                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition" 
                                        required>
                                        <option value="">Choose a location...</option>
                                        <option value="Batangas Bay">🌊 Batangas Bay</option>
                                        <option value="Calatagan Coast">🏖️ Calatagan Coast</option>
                                        <option value="Lubang Island">🏝️ Lubang Island</option>
                                        <option value="Mindoro Strait">⛵ Mindoro Strait</option>
                                        <option value="Verde Island Passage">🌅 Verde Island Passage</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Safety & History Section -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                                <span class="inline-block w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center mr-3">3</span>
                                Safety History
                            </h4>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    ⚠️ Past Incidents Nearby
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="number" min="0" name="past_incidents_nearby" 
                                    class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition" 
                                    placeholder="e.g., 0, 1, 2..." required>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Number of reported incidents in this area in the past month</p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-4 pt-6">
                            <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition duration-200 flex items-center justify-center gap-2">
                                <span>⚡</span>
                                <span>Predict Risk</span>
                            </button>
                            <button type="reset" class="px-8 py-4 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                Clear
                            </button>
                        </div>
                    </form>

                    <!-- Result Display -->
                    @if (isset($result))
                        <div class="mt-10 animate-fade-in">
                            <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">📊 Prediction Result</h4>
                            @php
                                $riskLevel = strtolower($result);
                                $isHigh = strpos($riskLevel, 'high') !== false;
                                $isMedium = strpos($riskLevel, 'medium') !== false;
                                $isLow = strpos($riskLevel, 'low') !== false;
                                
                                if ($isHigh) {
                                    $bgColor = 'bg-red-50 dark:bg-red-900';
                                    $borderColor = 'border-red-300 dark:border-red-700';
                                    $textColor = 'text-red-700 dark:text-red-200';
                                    $badgeBg = 'bg-red-600';
                                    $icon = '🚨';
                                } elseif ($isMedium) {
                                    $bgColor = 'bg-yellow-50 dark:bg-yellow-900';
                                    $borderColor = 'border-yellow-300 dark:border-yellow-700';
                                    $textColor = 'text-yellow-700 dark:text-yellow-200';
                                    $badgeBg = 'bg-yellow-600';
                                    $icon = '⚠️';
                                } else {
                                    $bgColor = 'bg-green-50 dark:bg-green-900';
                                    $borderColor = 'border-green-300 dark:border-green-700';
                                    $textColor = 'text-green-700 dark:text-green-200';
                                    $badgeBg = 'bg-green-600';
                                    $icon = '✅';
                                }
                            @endphp
                            
                            <div class="{{ $bgColor }} {{ $borderColor }} border-2 rounded-lg p-6">
                                <div class="flex items-center gap-4">
                                    <span class="text-4xl">{{ $icon }}</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Risk Assessment</p>
                                        <p class="{{ $textColor }} text-3xl font-bold">{{ $result }}</p>
                                    </div>
                                </div>
                                
                                @if ($isHigh)
                                    <div class="mt-4 text-sm {{ $textColor }}">
                                        <p class="font-semibold mb-1">⛔ Not Recommended for Fishing</p>
                                        <p>Conditions are dangerous. Stay ashore and wait for better weather.</p>
                                    </div>
                                @elseif ($isMedium)
                                    <div class="mt-4 text-sm {{ $textColor }}">
                                        <p class="font-semibold mb-1">⚠️ Use Caution</p>
                                        <p>Fishing is possible but be prepared for challenging conditions. Take extra safety precautions.</p>
                                    </div>
                                @else
                                    <div class="mt-4 text-sm {{ $textColor }}">
                                        <p class="font-semibold mb-1">✅ Safe to Fish</p>
                                        <p>Conditions are favorable. Ensure you still follow standard safety protocols.</p>
                                    </div>
                                @endif
                            </div>

                            @if (isset($input))
                                <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-3">Input Summary</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                        <div>
                                            <p class="text-gray-600 dark:text-gray-400">Wind Speed</p>
                                            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $input['wind_speed_kph'] }} kph</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 dark:text-gray-400">Wave Height</p>
                                            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $input['wave_height_m'] }} m</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 dark:text-gray-400">Rainfall</p>
                                            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $input['rainfall_mm'] }} mm</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 dark:text-gray-400">Location</p>
                                            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $input['location'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-8 bg-blue-50 dark:bg-blue-900 border-2 border-blue-200 dark:border-blue-700 rounded-lg p-6">
                <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">ℹ️ How This Works</h4>
                <p class="text-blue-800 dark:text-blue-200 text-sm">This AI-powered system analyzes weather conditions, ocean data, and historical incident reports to assess fishing safety. Always use this as a guide and prioritize your crew's safety above all else.</p>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fade-in 0.3s ease-in;
        }
    </style>
</x-app-layout>