<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl text-gray-800 dark:text-gray-100 leading-tight">📚 Risk Prediction History</h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Search historical predictions to inform upcoming trips.</p>
            </div>
            <a href="{{ route('risk-form') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                <span>⚡</span>
                <span>Back to Predictor</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Location</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="e.g., Batangas Bay" class="w-full px-4 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Risk Level</label>
                        <select name="risk" class="w-full px-4 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition">
                            <option value="">Any</option>
                            <option value="low" @selected(($filters['risk'] ?? '') === 'low')>Low</option>
                            <option value="medium" @selected(($filters['risk'] ?? '') === 'medium')>Medium</option>
                            <option value="high" @selected(($filters['risk'] ?? '') === 'high')>High</option>
                            <option value="error" @selected(($filters['risk'] ?? '') === 'error')>Errors Only</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">From</label>
                        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">To</label>
                        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition">
                    </div>

                    <div class="md:col-span-5 flex flex-wrap items-center justify-end gap-3 pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                            <span>🔍</span>
                            <span>Apply Filters</span>
                        </button>
                        <a href="{{ route('risk-history') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Highlight -->
            <div class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white rounded-2xl p-6 shadow-lg">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.2em] font-semibold">Location insight</p>
                        <h3 class="text-2xl font-bold mt-2">
                            @if($highlightLog)
                                Latest update for <span class="underline decoration-white/60">{{ $highlightLog->location }}</span>
                            @elseif(!empty($filters['q']))
                                No records yet for "{{ $filters['q'] }}"
                            @else
                                Search a location to see its latest status
                            @endif
                        </h3>
                        @if($highlightLog)
                            <p class="mt-2 text-blue-100">Captured {{ $highlightLog->predicted_at->diffForHumans() }} — Wind {{ $highlightLog->wind_speed_kph }} kph, Waves {{ $highlightLog->wave_height_m }} m, Rain {{ $highlightLog->rainfall_mm }} mm.</p>
                        @else
                            <p class="mt-2 text-blue-100">Use the filters above to narrow down historical predictions by location, risk level, and date.</p>
                        @endif
                    </div>
                    <div class="flex flex-col items-start md:items-end gap-2">
                        @if($highlightLog)
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-white/10">
                                {{ $highlightLog->result }}
                            </span>
                            <a href="{{ route('risk-form') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg font-semibold transition">
                                <span>Plan a trip</span>
                                <span>→</span>
                            </a>
                        @else
                            <a href="{{ route('risk-form') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg font-semibold transition">
                                <span>Run a new prediction</span>
                                <span>→</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-emerald-100 dark:bg-emerald-900 text-emerald-900 dark:text-emerald-100 rounded-2xl p-5">
                    <p class="text-sm font-semibold uppercase tracking-wide">Total Entries</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($logs->total()) }}</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 text-green-900 dark:text-green-100 rounded-2xl p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide">Low Risk</p>
                    <p class="text-2xl font-bold mt-2">{{ number_format($stats['low'] ?? 0) }}</p>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-900 dark:text-yellow-100 rounded-2xl p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide">Medium Risk</p>
                    <p class="text-2xl font-bold mt-2">{{ number_format($stats['medium'] ?? 0) }}</p>
                </div>
                <div class="bg-red-100 dark:bg-red-900 text-red-900 dark:text-red-100 rounded-2xl p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide">High / Errors</p>
                    <p class="text-2xl font-bold mt-2">{{ number_format(($stats['high'] ?? 0) + ($stats['error'] ?? 0)) }}</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">When</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Risk</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Wind / Waves</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rain / Visibility</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Incidents</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Logged By</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($logs as $log)
                                <tr>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $log->predicted_at->format('M d, Y h:i A') }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->predicted_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $log->location }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                            @class([
                                                'bg-green-100 text-green-700 dark:bg-green-900/60 dark:text-green-200' => str_contains(strtolower($log->result), 'low'),
                                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/60 dark:text-yellow-200' => str_contains(strtolower($log->result), 'medium'),
                                                'bg-red-100 text-red-700 dark:bg-red-900/60 dark:text-red-200' => str_contains(strtolower($log->result), 'high'),
                                                'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' => str_contains(strtolower($log->result), 'error'),
                                            ])">
                                            {{ $log->result }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">
                                        <div>💨 {{ $log->wind_speed_kph }} kph</div>
                                        <div>🌊 {{ $log->wave_height_m }} m</div>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">
                                        <div>🌧️ {{ $log->rainfall_mm }} mm</div>
                                        <div>👁️ {{ $log->visibility_km }} km</div>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">
                                        <div>⚠️ {{ $log->past_incidents_nearby }}</div>
                                        <div>🌙 Phase {{ $log->moon_phase }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $log->user?->username ?? 'System' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                                        No records yet. Submit a prediction to populate this log.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
