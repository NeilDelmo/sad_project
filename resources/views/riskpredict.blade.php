<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Fishing Risk Prediction
        </h2>
    </x-slot>

    <div class="p-6 bg-white dark:bg-gray-800 shadow rounded-lg">
        <form action="{{ route('predict-risk') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Wind Speed (kph)</label>
                    <input type="number" step="0.1" name="wind_speed_kph" class="w-full rounded-md" required>
                </div>
                <div>
                    <label>Wave Height (m)</label>
                    <input type="number" step="0.1" name="wave_height_m" class="w-full rounded-md" required>
                </div>
                <div>
                    <label>Rainfall (mm)</label>
                    <input type="number" step="0.1" name="rainfall_mm" class="w-full rounded-md" required>
                </div>
                <div>
                    <label>Tide Level (m)</label>
                    <input type="number" step="0.1" name="tide_level_m" class="w-full rounded-md" required>
                </div>
                <div>
                    <label>Moon Phase (0–3)</label>
                    <input type="number" min="0" max="3" name="moon_phase" class="w-full rounded-md" required>
                </div>
                <div>
                    <label>Visibility (km)</label>
                    <input type="number" step="0.1" name="visibility_km" class="w-full rounded-md" required>
                </div>
                <div>
                    <label>Past Incidents Nearby</label>
                    <input type="number" name="past_incidents_nearby" class="w-full rounded-md" required>
                </div>
                <div>
                    <label>Location</label>
                    <select name="location" class="w-full rounded-md" required>
                        <option value="Batangas Bay">Batangas Bay</option>
                        <option value="Calatagan Coast">Calatagan Coast</option>
                        <option value="Lubang Island">Lubang Island</option>
                        <option value="Mindoro Strait">Mindoro Strait</option>
                        <option value="Verde Island Passage">Verde Island Passage</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-md">Predict Risk</button>
            </div>
        </form>

        @if (isset($result))
            <div class="mt-6 p-4 bg-gray-100 rounded-md">
                <h3 class="text-lg font-semibold">Prediction Result:</h3>
                <p class="text-xl text-blue-700 font-bold">{{ $result }}</p>
            </div>
        @endif
    </div>
</x-app-layout>