from flask import Flask, request, jsonify
from flask_cors import CORS
import pandas as pd
import joblib
import requests
import os
import math
import warnings
from datetime import datetime
import numpy as np
from dotenv import load_dotenv
import traceback
import time
import json
from concurrent.futures import ThreadPoolExecutor

# Load environment variables from Laravel's .env file
env_path = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), '.env')
load_dotenv(env_path)

warnings.filterwarnings("ignore", category=UserWarning)
warnings.filterwarnings("ignore", category=FutureWarning)

app = Flask(__name__)
CORS(app)

# Configuration
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_PATH = os.path.join(BASE_DIR, "fishing_trip_risk_model.pkl")
DATA_PATH = os.path.join(BASE_DIR, "fishing_trip_risk_dataset.csv")

# Weather API configuration - Properly loaded from Laravel .env
OPENWEATHER_API_KEY = os.getenv('OPENWEATHER_API_KEY')
if not OPENWEATHER_API_KEY:
    print("⚠️ WARNING: OPENWEATHER_API_KEY not found in .env file!")
    print("Please add OPENWEATHER_API_KEY=your_key_here to your .env file")

class FishingSafetyAPI:
    def __init__(self):
        self.model = None
        self.location_mapping = {}
        self.marine_cache = {}
        self.elevation_cache = {}
        self.weather_cache = {}
        self.weather_cache_ttl = 180  # seconds
        self.load_model()
    
    def load_model(self):
        """Load the trained model and location mappings with fallbacks"""
        try:
            if os.path.exists(MODEL_PATH):
                self.model = joblib.load(MODEL_PATH)
                print("✅ Model loaded successfully")
            else:
                print("⚠️ Model file not found - will use rule-based prediction")
                self.model = None
            
            if os.path.exists(DATA_PATH):
                df_data = pd.read_csv(DATA_PATH)
                self.location_mapping = {name: i for i, name in enumerate(sorted(df_data['location'].unique()))}
                print("✅ Location mapping loaded successfully")
            else:
                print("⚠️ Data file not found - using default location mapping")
                self.location_mapping = {"default": 0}
        except Exception as e:
            print(f"❌ Error loading model: {e}")
            self.model = None
    
    def get_weather_data(self, lat, lon):
        """Fetch comprehensive weather data using One Call API 3.0 only"""
        try:
            cache_key = (round(lat, 3), round(lon, 3))
            cached_entry = self.weather_cache.get(cache_key)
            if cached_entry:
                cached_data, cached_name, cached_ts = cached_entry
                if time.time() - cached_ts < self.weather_cache_ttl:
                    print("♻️ Using cached weather data")
                    return cached_data, cached_name
                else:
                    self.weather_cache.pop(cache_key, None)

            if not OPENWEATHER_API_KEY or OPENWEATHER_API_KEY == "your_openweather_api_key_here":
                print("⚠️ API key not configured - using mock weather data")
                mock_data = self.get_mock_weather_data(lat, lon)
                self._store_weather_cache(cache_key, mock_data)
                return mock_data
            
            def fetch_onecall():
                onecall_url = "https://api.openweathermap.org/data/3.0/onecall"
                params = {
                    'lat': lat,
                    'lon': lon,
                    'appid': OPENWEATHER_API_KEY,
                    'units': 'metric',
                    'exclude': 'minutely'  # Keep alerts so we can surface typhoon warnings
                }
                response = requests.get(onecall_url, params=params, timeout=10)
                response.raise_for_status()
                return response.json()

            def fetch_location_name():
                try:
                    location_url = "http://api.openweathermap.org/geo/1.0/reverse"
                    location_params = {
                        'lat': lat,
                        'lon': lon,
                        'limit': 1,
                        'appid': OPENWEATHER_API_KEY
                    }
                    response = requests.get(location_url, params=location_params, timeout=5)
                    response.raise_for_status()
                    location_data = response.json()
                    if location_data and len(location_data) > 0:
                        return location_data[0].get('name', 'Unknown location')
                except Exception as exc:
                    print(f"⚠️ Location lookup fallback: {exc}")
                return "Unknown location"

            with ThreadPoolExecutor(max_workers=2) as executor:
                onecall_future = executor.submit(fetch_onecall)
                location_future = executor.submit(fetch_location_name)
                onecall_data = onecall_future.result()
                location_name = location_future.result()
            
            print(f"✅ Weather data fetched for: {location_name} ({lat}, {lon})")
            result = (onecall_data, location_name)
            self._store_weather_cache(cache_key, result)
            return result
        except requests.exceptions.RequestException as e:
            print(f"❌ API request failed: {e}")
            fallback = self.get_mock_weather_data(lat, lon)
            self._store_weather_cache(cache_key, fallback)
            return fallback
        except Exception as e:
            print(f"❌ Error fetching weather data: {e}")
            fallback = self.get_mock_weather_data(lat, lon)
            self._store_weather_cache(cache_key, fallback)
            return fallback
    
    def get_mock_weather_data(self, lat, lon):
        """Generate realistic mock weather data for testing/development"""
        import random
        
        # Generate realistic values based on Philippines coastal conditions
        mock_onecall = {
            'current': {
                'temp': random.uniform(26, 32),
                'humidity': random.uniform(70, 90),
                'pressure': random.uniform(1008, 1015),
                'wind_speed': random.uniform(2, 8),  # m/s
                'wind_deg': random.uniform(0, 360),
                'visibility': random.uniform(8000, 15000),
                'uvi': random.uniform(5, 11),
                'weather': [{'main': 'Clear', 'description': 'clear sky'}],
                'rain': {'1h': random.uniform(0, 2)}
            },
            'hourly': [
                {
                    'dt': int(datetime.now().timestamp()) + i * 3600,
                    'wind_speed': random.uniform(2, 10),
                    'wind_deg': random.uniform(0, 360)
                } for i in range(24)
            ]
        }
        
        # Mock location name based on coordinates
        location_name = "Calatagan Bay"
        if 13.8 <= lat <= 13.9 and 120.6 <= lon <= 120.7:
            location_name = "Calatagan Coastal Area"
        
        print("⚠️ Using mock weather data for testing/development")
        return mock_onecall, location_name
    
    def get_marine_conditions(self, lat, lon, wind_speed_ms):
        """Calculate marine conditions based on wind and location"""
        # Beaufort scale for wave height estimation
        beaufort_scale = {
            0: 0.0, 1: 0.1, 2: 0.2, 3: 0.6, 4: 1.0, 
            5: 2.0, 6: 3.0, 7: 4.0, 8: 5.5, 9: 7.0, 
            10: 9.0, 11: 11.5, 12: 14.0
        }
        
        # Convert wind speed to Beaufort scale
        wind_kph = wind_speed_ms * 3.6
        if wind_kph < 1: beaufort = 0
        elif wind_kph < 6: beaufort = 1
        elif wind_kph < 12: beaufort = 2
        elif wind_kph < 20: beaufort = 3
        elif wind_kph < 29: beaufort = 4
        elif wind_kph < 39: beaufort = 5
        elif wind_kph < 50: beaufort = 6
        elif wind_kph < 62: beaufort = 7
        elif wind_kph < 75: beaufort = 8
        elif wind_kph < 89: beaufort = 9
        elif wind_kph < 103: beaufort = 10
        elif wind_kph < 118: beaufort = 11
        else: beaufort = 12
        
        wave_height = beaufort_scale.get(beaufort, 1.0)
        
        # Adjust for coastal vs open water (Philippines coastal areas)
        coastal_factor = 0.7  # Waves are typically smaller in coastal areas
        wave_height *= coastal_factor
        
        return {
            'wave_height_m': wave_height,
            'beaufort_scale': beaufort,
            'sea_state': self.get_sea_state_description(beaufort)
        }
    
    def get_sea_state_description(self, beaufort):
        """Get sea state description based on Beaufort scale"""
        descriptions = {
            0: "Calm (glassy)",
            1: "Light air (ripples)",
            2: "Light breeze (small wavelets)",
            3: "Gentle breeze (large wavelets)",
            4: "Moderate breeze (small waves)",
            5: "Fresh breeze (moderate waves)",
            6: "Strong breeze (large waves)",
            7: "Near gale (sea heaps up)",
            8: "Gale (moderately high waves)",
            9: "Strong gale (high waves)",
            10: "Storm (very high waves)",
            11: "Violent storm (exceptionally high waves)",
            12: "Hurricane (air filled with foam)"
        }
        return descriptions.get(beaufort, "Unknown")
    
    def get_tide_data(self, lat, lon):
        """Simulate tide data for Philippines coastal areas"""
        # Simple sinusoidal tide simulation
        now = datetime.now()
        hours_since_midnight = now.hour + now.minute / 60.0
        
        # Two tides per day (semi-diurnal) - adjusted for Philippines
        tide_cycle = 12.42  # Average time between high tides
        tide_phase = (hours_since_midnight % tide_cycle) / tide_cycle * 2 * math.pi
        
        # Tide range for Philippines coastal areas (approximately 1.5m range)
        tide_level = math.sin(tide_phase) * 0.75  # ±0.75m from mean sea level
        
        # Determine tide state
        tide_rate = math.cos(tide_phase) * 0.75
        if tide_rate > 0.1:
            tide_state = "Rising"
        elif tide_rate < -0.1:
            tide_state = "Falling"
        else:
            tide_state = "Slack"
        
        return {
            'level_m': tide_level,
            'state': tide_state,
            'rate_m_per_hour': tide_rate
        }
    
    def get_moon_phase(self):
        """Calculate current moon phase"""
        now = datetime.now()
        # Known new moon date (January 11, 2024)
        known_new_moon = datetime(2024, 1, 11)
        days_since = (now - known_new_moon).days
        lunar_cycle = 29.53058867  # Precise lunar cycle
        phase = (days_since % lunar_cycle) / lunar_cycle
        # Convert to more intuitive scale (0 = new moon, 0.5 = full moon)
        return phase
    
    def get_historical_incidents(self, lat, lon, radius_km=10):
        """Get historical incident data for the area"""
        # Known risky areas in Philippines coastal regions
        risky_areas = [
            {'lat': 13.8500, 'lon': 120.6167, 'incidents': 8, 'name': 'Calatagan Point'},
            {'lat': 13.8300, 'lon': 120.6000, 'incidents': 5, 'name': 'Balibago Reef'},
            {'lat': 13.8700, 'lon': 120.6300, 'incidents': 3, 'name': 'Bagalangit Point'},
            {'lat': 14.5472, 'lon': 120.8275, 'incidents': 6, 'name': 'Manila Bay Entrance'},
            {'lat': 13.0906, 'lon': 123.7438, 'incidents': 4, 'name': 'Lagonoy Gulf'}
        ]
        
        total_incidents = 0
        nearby_areas = []
        for area in risky_areas:
            distance = self.calculate_distance(lat, lon, area['lat'], area['lon'])
            if distance <= radius_km:
                # Weight incidents by distance (closer = more relevant)
                weight = max(0, 1 - (distance / radius_km))
                weighted_incidents = area['incidents'] * weight
                total_incidents += weighted_incidents
                nearby_areas.append({
                    'name': area['name'],
                    'distance_km': round(distance, 1),
                    'incidents': area['incidents']
                })
        
        return {
            'total_weighted_incidents': round(total_incidents, 1),
            'nearby_risk_areas': nearby_areas
        }

    def is_marine_location(self, lat, lon):
        """Check whether the coordinate is over water using OpenStreetMap reverse geocoding."""
        try:
            # Simple cache to avoid hammering Nominatim for nearby clicks
            # Use four decimal places (~11m) to avoid treating nearby land/ocean points as identical
            cache_key = (round(lat, 4), round(lon, 4))
            if cache_key in self.marine_cache:
                return self.marine_cache[cache_key]

            url = "https://nominatim.openstreetmap.org/reverse"
            params = {
                'format': 'json',
                'lat': lat,
                'lon': lon,
                'zoom': 13,
                'addressdetails': 1,
                'namedetails': 1,
                'extratags': 1,
                'accept-language': 'en'
            }
            headers = {'User-Agent': 'FishingSafetyApp/1.0'}

            response = requests.get(url, params=params, headers=headers, timeout=5)
            response.raise_for_status()
            data = response.json()

            address = data.get('address', {})
            display_name = (data.get('display_name') or '').lower()
            classification = (data.get('class') or '').lower()
            category = (data.get('category') or '').lower()
            type_name = (data.get('type') or '').lower()
            addresstype = (data.get('addresstype') or '').lower()
            extratags = data.get('extratags', {})
            address_values = ' '.join(
                (value or '').lower()
                for value in address.values()
                if isinstance(value, str)
            )

            water_terms = (
                'sea', 'ocean', 'bay', 'coast', 'strait', 'gulf', 'channel',
                'harbor', 'harbour', 'port', 'shore', 'beach', 'cove', 'lagoon',
                'estuary', 'waters', 'sound', 'river', 'lake', 'pond', 'marina'
            )

            marine_types = {
                'sea', 'ocean', 'bay', 'strait', 'channel', 'harbor', 'harbour',
                'lagoon', 'estuary', 'coastline', 'water', 'river', 'riverbank',
                'lake', 'pond', 'reservoir', 'dam', 'dock', 'marina'
            }

            # Check classification hints from Nominatim response
            is_water = any([
                classification in {'water', 'waterway'} or category in {'water', 'waterway'},
                classification == 'natural' and type_name in marine_types,
                classification == 'place' and type_name in marine_types,
                addresstype in marine_types,
                type_name in marine_types,
                address.get('natural') in marine_types,
                address.get('waterway') in marine_types,
                address.get('water') in marine_types,
                address.get('place') in marine_types,
                any(term in display_name for term in water_terms),
                any(term in address_values for term in water_terms),
                extratags.get('natural') in marine_types,
                extratags.get('water') in marine_types,
                extratags.get('waterway') in marine_types,
            ])

            is_water_bool = bool(is_water)
            if not is_water_bool:
                elevation = self._get_elevation(lat, lon)
                if elevation is not None and elevation <= 0.5:
                    is_water_bool = True
                    print(
                        "ℹ️ Elevation fallback marked as water",
                        {
                            "lat": round(lat, 5),
                            "lon": round(lon, 5),
                            "elevation": elevation
                        }
                    )
                else:
                    print(
                        "ℹ️ Marine check classified as land",
                        {
                            "lat": round(lat, 5),
                            "lon": round(lon, 5),
                            "class": classification,
                            "type": type_name,
                            "addresstype": addresstype,
                            "category": category,
                            "display_name": display_name[:120],
                            "elevation": elevation
                        }
                    )

            self.marine_cache[cache_key] = is_water_bool
            return is_water_bool
        except Exception as exc:
            print(f"⚠️ Water detection API error: {exc}")
            # Default to treating as marine so users still receive data even if lookup fails
            return True

    def _get_elevation(self, lat, lon):
        """Fetch elevation data to help disambiguate water vs land when geocoding is inconclusive."""
        cache_key = (round(lat, 4), round(lon, 4))
        if cache_key in self.elevation_cache:
            return self.elevation_cache[cache_key]

        try:
            url = "https://api.opentopodata.org/v1/etopo1"
            params = {"locations": f"{lat},{lon}"}
            response = requests.get(url, params=params, timeout=5)
            response.raise_for_status()
            payload = response.json()
            results = payload.get("results", [])
            if results:
                elevation = results[0].get("elevation")
                self.elevation_cache[cache_key] = elevation
                return elevation
        except Exception as exc:
            print(f"⚠️ Elevation lookup failed: {exc}")

        self.elevation_cache[cache_key] = None
        return None

    def _store_weather_cache(self, cache_key, result):
        """Cache weather responses with a TTL to reduce repeat external calls."""
        if not isinstance(result, tuple) or len(result) != 2:
            return
        data, location_name = result
        self.weather_cache[cache_key] = (data, location_name, time.time())

        # Simple LRU-style trim to prevent unbounded growth
        max_entries = 200
        if len(self.weather_cache) > max_entries:
            # drop the oldest entries
            sorted_items = sorted(
                self.weather_cache.items(),
                key=lambda item: item[1][2]
            )
            for key, _ in sorted_items[: len(self.weather_cache) - max_entries]:
                self.weather_cache.pop(key, None)

    def analyze_weather_alerts(self, alerts):
        """Normalize weather alerts and detect typhoon-level warnings."""
        if not alerts:
            return {
                'alerts': [],
                'typhoon_active': False,
                'severe_alert_present': False
            }

        normalized = []
        typhoon_active = False
        severe_present = False

        typhoon_core_terms = (
            'typhoon',
            'tropical storm',
            'tropical cyclone',
            'hurricane',
            'storm surge'
        )
        signal_terms = (
            'wind signal',
            'tropical cyclone signal',
            'tcws',
            'signal #',
            'signal no.'
        )
        flood_terms = (
            'flood advisory',
            'flood warning',
            'general flood',
            'river flood',
            'flash flood'
        )

        for alert in alerts:
            title = (alert.get('event') or 'Weather Alert').strip()
            description = (alert.get('description') or '').strip()
            severity = (alert.get('severity') or '').strip().lower()
            sender = (alert.get('sender_name') or '').strip()

            combined = f"{title} {description}".lower()
            title_lower = title.lower()
            has_typhoon_term = any(keyword in combined for keyword in typhoon_core_terms)
            has_signal_term = any(term in combined for term in signal_terms)
            has_flood_term = any(term in combined for term in flood_terms)

            is_typhoon = False
            if has_typhoon_term:
                is_typhoon = True
            elif has_signal_term and ('tropical cyclone' in combined or 'typhoon' in combined):
                # Treat PAGASA wind signal advisories tied to tropical cyclones as typhoon alerts
                is_typhoon = True

            if is_typhoon and ('flood' in title_lower or (has_flood_term and not has_typhoon_term)):
                # Avoid classifying standalone flood advisories as typhoon-level alerts
                is_typhoon = False

            if is_typhoon and severity not in {'watch', 'warning'}:
                # Do not treat informational notices as typhoon-level without explicit severity
                is_typhoon = False

            typhoon_active = typhoon_active or is_typhoon

            level = 'information'
            if severity in {'advisory', 'watch', 'warning'}:
                level = severity
            elif is_typhoon:
                level = 'warning'

            if level in {'watch', 'warning'}:
                severe_present = True

            normalized.append({
                'title': title,
                'severity': level,
                'description': description[:600],
                'is_typhoon': is_typhoon,
                'start': alert.get('start'),
                'end': alert.get('end'),
                'source': sender
            })

        return {
            'alerts': normalized,
            'typhoon_active': typhoon_active,
            'severe_alert_present': severe_present
        }
    
    def calculate_distance(self, lat1, lon1, lat2, lon2):
        """Calculate distance between two coordinates using Haversine formula"""
        lat1, lon1, lat2, lon2 = map(math.radians, [lat1, lon1, lat2, lon2])
        dlat = lat2 - lat1
        dlon = lon2 - lon1
        a = math.sin(dlat/2)**2 + math.cos(lat1) * math.cos(lat2) * math.sin(dlon/2)**2
        return 2 * math.asin(math.sqrt(a)) * 6371  # Earth radius in km
    
    def extract_features_from_weather(self, onecall_data, lat, lon, is_marine=True):
        """Extract comprehensive features for the safety model"""
        try:
            current = onecall_data['current']
            
            # Wind data
            wind_speed_ms = current.get('wind_speed', 0)
            wind_speed_kph = wind_speed_ms * 3.6
            wind_direction = current.get('wind_deg', 0)
            wind_gust_ms = current.get('wind_gust', wind_speed_ms)
            wind_gust_kph = wind_gust_ms * 3.6

            # Weather conditions context
            weather_entry = (current.get('weather') or [{}])[0]
            weather_main = weather_entry.get('main', 'Unknown')
            weather_description = weather_entry.get('description', 'Unknown').lower()
            cloud_cover = current.get('clouds', 0)
            humidity = current.get('humidity', 75)
            pressure = current.get('pressure', 1013)
            uv_index = current.get('uvi', 5)

            sunrise = current.get('sunrise')
            sunset = current.get('sunset')
            observation_time = current.get('dt')
            is_night = False
            if sunrise and sunset and observation_time:
                is_night = not (sunrise <= observation_time <= sunset)
            
            # Marine conditions
            if is_marine:
                marine_conditions = self.get_marine_conditions(lat, lon, wind_speed_ms)
            else:
                marine_conditions = {
                    'wave_height_m': 0.0,
                    'beaufort_scale': 0,
                    'sea_state': 'Onshore location'
                }
            wave_height_m = marine_conditions['wave_height_m']
            
            # Precipitation
            rainfall_mm = 0
            if 'rain' in current:
                rainfall_mm = current['rain'].get('1h', 0)
            
            # Tide data
            tide_data = self.get_tide_data(lat, lon)
            tide_level_m = tide_data['level_m']
            
            # Moon phase
            moon_phase = self.get_moon_phase()
            
            # Visibility
            visibility_m = current.get('visibility', 10000)
            visibility_km = visibility_m / 1000
            
            # Historical incidents
            incident_data = self.get_historical_incidents(lat, lon)
            past_incidents_nearby = incident_data['total_weighted_incidents']
            
            # Location encoding
            # Use a more generic location name format for Philippines
            location_name = f"PH_{lat:.3f}_{lon:.3f}"
            location_encoded = self.location_mapping.get(location_name, 0)
            
            # near-term forecast outlook (next 6 hours)
            hourly_slice = (onecall_data.get('hourly') or [])[:6]
            hourly_wind_kph = []
            hourly_gust_kph = []
            hourly_rain_mm = []
            severe_weather_windows = 0
            for hour in hourly_slice:
                wind_val = hour.get('wind_speed', 0) * 3.6
                gust_val = hour.get('wind_gust', hour.get('wind_speed', 0)) * 3.6
                rainfall_val = 0
                if 'rain' in hour:
                    rainfall_val = hour['rain'].get('1h', 0)
                hourly_wind_kph.append(wind_val)
                hourly_gust_kph.append(gust_val)
                hourly_rain_mm.append(rainfall_val)
                weather_group = (hour.get('weather') or [{}])[0].get('main', '').lower()
                if weather_group in {'thunderstorm', 'extreme', 'tornado'}:
                    severe_weather_windows += 1
            max_hourly_wind_kph = max(hourly_wind_kph, default=wind_speed_kph)
            max_hourly_gust_kph = max(hourly_gust_kph, default=wind_gust_kph)
            max_hourly_rain_mm = max(hourly_rain_mm, default=rainfall_mm)

            alerts_present = bool(onecall_data.get('alerts'))
            
            features = {
                "wind_speed_kph": wind_speed_kph,
                "wind_direction": wind_direction,
                "wave_height_m": wave_height_m if is_marine else 0.0,
                "rainfall_mm": rainfall_mm,
                "tide_level_m": tide_level_m,
                "moon_phase": moon_phase,
                "visibility_km": visibility_km,
                "past_incidents_nearby": past_incidents_nearby,
                "location": location_encoded,
                "uv_index": uv_index,
                "humidity": humidity,
                "pressure": pressure,
                "beaufort_scale": marine_conditions['beaufort_scale'],
                "wind_gust_kph": wind_gust_kph,
                "cloud_cover": cloud_cover
            }
            
            environmental_context = {
                "weather_main": weather_main,
                "weather_description": weather_description,
                "cloud_cover": cloud_cover,
                "is_night": is_night,
                "alerts_present": alerts_present,
                "max_hourly_wind_kph": max_hourly_wind_kph,
                "max_hourly_gust_kph": max_hourly_gust_kph,
                "max_hourly_rain_mm": max_hourly_rain_mm,
                "severe_weather_windows": severe_weather_windows,
                "wind_gust_kph": wind_gust_kph,
                "is_marine_location": bool(is_marine),
                "flags": []
            }

            alert_summary = self.analyze_weather_alerts(onecall_data.get('alerts'))
            if alert_summary['alerts']:
                environmental_context['active_alerts'] = alert_summary['alerts']
            if alert_summary['typhoon_active']:
                environmental_context['typhoon_alert'] = True
                message = "Active typhoon or tropical cyclone alert in effect"
                if message not in environmental_context['flags']:
                    environmental_context['flags'].append(message)
            if alert_summary['severe_alert_present']:
                message = "Official weather agency issued a severe weather watch/warning"
                if message not in environmental_context['flags']:
                    environmental_context['flags'].append(message)

            if not is_marine:
                message = "Selected point appears to be on land; marine values approximated"
                if message not in environmental_context['flags']:
                    environmental_context['flags'].append(message)

            return features, {
                'marine_conditions': marine_conditions,
                'tide_data': tide_data,
                'incident_data': incident_data,
                'environmental_context': environmental_context,
                'alert_summary': alert_summary
            }
        except Exception as e:
            print(f"❌ Error extracting features: {e}")
            traceback.print_exc()
            return None, None
    
    def predict_safety(self, features, context=None):
        """Predict fishing safety with enhanced logic including fallbacks"""
        override_reasons = []
        try:
            # If model is available, use it
            if self.model:
                # Align incoming features with model training columns
                if hasattr(self.model, 'feature_names_in_'):
                    filtered = {name: features.get(name, 0) for name in self.model.feature_names_in_}
                    df = pd.DataFrame([filtered])[list(self.model.feature_names_in_)]
                else:
                    df = pd.DataFrame([features])
                prediction = self.model.predict(df)[0]
                probability = self.model.predict_proba(df)[0]
            else:
                # Fallback rule-based prediction
                prediction, probability = self.rule_based_prediction(features)

            verdict_map = {0: "Safe", 1: "Caution", 2: "Dangerous"}
            verdict = verdict_map.get(prediction, "Unknown")

            # Format probabilities for all risk levels
            if isinstance(probability, (list, np.ndarray)):
                if len(probability) >= 3:
                    probs = {
                        "safe": float(probability[0]),
                        "caution": float(probability[1]),
                        "dangerous": float(probability[2])
                    }
                elif len(probability) == 2:
                    # Binary classifier fallback
                    probs = {
                        "safe": float(probability[0]),
                        "caution": 0.0,
                        "dangerous": float(probability[1])
                    }
                else:
                    # Single value probability
                    highest_prob = float(max(probability))
                    if prediction == 0:  # Safe
                        probs = {"safe": highest_prob, "caution": 0.0, "dangerous": 1-highest_prob}
                    elif prediction == 1:  # Caution
                        probs = {"safe": (1-highest_prob)/2, "caution": highest_prob, "dangerous": (1-highest_prob)/2}
                    else:  # Dangerous
                        probs = {"safe": 1-highest_prob, "caution": 0.0, "dangerous": highest_prob}
            else:
                # Scalar probability
                if prediction == 0:  # Safe
                    probs = {"safe": float(probability), "caution": 0.0, "dangerous": 1-float(probability)}
                elif prediction == 1:  # Caution
                    prob = float(probability)
                    probs = {"safe": (1-prob)/2, "caution": prob, "dangerous": (1-prob)/2}
                else:  # Dangerous
                    prob = float(probability)
                    probs = {"safe": 1-prob, "caution": 0.0, "dangerous": prob}

            override = self.extreme_weather_override(features, verdict)
            if override:
                verdict = override["verdict"]
                prediction = override["risk_level"]
                probs = override["probabilities"]
                override_reasons = override["reasons"]

            env_adjustment = self.environmental_adjustment(features, context, verdict)
            if env_adjustment:
                verdict = env_adjustment["verdict"]
                prediction = env_adjustment["risk_level"]
                probs = env_adjustment["probabilities"]
                override_reasons.extend(env_adjustment["reasons"])

            result = {
                "verdict": verdict,
                "risk_level": int(prediction),
                "confidence": float(max(probs.values())),
                "probabilities": probs,
                "override_reasons": override_reasons
            }
            return result
        except Exception as e:
            print(f"❌ Error in prediction: {e}")
            traceback.print_exc()
            # Ultimate fallback
            return {
                "verdict": "Caution",
                "risk_level": 1,
                "confidence": 0.6,
                "probabilities": {"safe": 0.2, "caution": 0.6, "dangerous": 0.2},
                "override_reasons": ["Model unavailable - using conservative fallback"]
            }
    
    def rule_based_prediction(self, features):
        """Rule-based safety prediction as fallback"""
        risk_score = 0
        
        # Wind speed risk
        if features["wind_speed_kph"] > 40:
            risk_score += 3
        elif features["wind_speed_kph"] > 25:
            risk_score += 2
        elif features["wind_speed_kph"] > 15:
            risk_score += 1
        
        # Wave height risk
        if features["wave_height_m"] > 3:
            risk_score += 3
        elif features["wave_height_m"] > 2:
            risk_score += 2
        elif features["wave_height_m"] > 1:
            risk_score += 1
        
        # Rainfall risk
        if features["rainfall_mm"] > 10:
            risk_score += 2
        elif features["rainfall_mm"] > 5:
            risk_score += 1
        
        # Visibility risk
        if features["visibility_km"] < 2:
            risk_score += 2
        elif features["visibility_km"] < 5:
            risk_score += 1
        
        # Historical incidents
        if features["past_incidents_nearby"] > 5:
            risk_score += 2
        elif features["past_incidents_nearby"] > 2:
            risk_score += 1
        
        # Determine verdict
        if risk_score >= 6:
            return 2, [0.1, 0.2, 0.7]  # Dangerous
        elif risk_score >= 3:
            return 1, [0.2, 0.6, 0.2]  # Caution
        else:
            return 0, [0.7, 0.2, 0.1]  # Safe
    
    def extreme_weather_override(self, features, current_verdict):
        """Apply hard safety overrides when extreme conditions are detected."""
        reasons_danger = []
        reasons_caution = []

        wind = features.get("wind_speed_kph", 0)
        waves = features.get("wave_height_m", 0)
        rainfall = features.get("rainfall_mm", 0)
        pressure = features.get("pressure", 1010)

        # Dangerous thresholds (typhoon / severe storm conditions)
        if wind >= 60:
            reasons_danger.append("Wind speed exceeds 60 kph (typhoon-level winds)")
        if waves >= 3:
            reasons_danger.append("Wave height exceeds 3 meters")
        if rainfall >= 25:
            reasons_danger.append("Extreme rainfall detected (>= 25mm)")
        if pressure <= 990:
            reasons_danger.append("Sea-level pressure below 990 hPa – storm system likely")

        # Cautionary thresholds (gale / rough sea)
        if wind >= 35:
            reasons_caution.append("Wind speed exceeds 35 kph – gale conditions")
        if waves >= 2:
            reasons_caution.append("Wave height exceeds 2 meters")
        if rainfall >= 10:
            reasons_caution.append("Heavy rainfall expected (>= 10mm)")

        if reasons_danger and current_verdict != "Dangerous":
            return {
                "verdict": "Dangerous",
                "risk_level": 2,
                "probabilities": {"safe": 0.05, "caution": 0.15, "dangerous": 0.8},
                "reasons": reasons_danger
            }

        if reasons_caution and current_verdict == "Safe":
            return {
                "verdict": "Caution",
                "risk_level": 1,
                "probabilities": {"safe": 0.2, "caution": 0.6, "dangerous": 0.2},
                "reasons": reasons_caution
            }

        return None

    def environmental_adjustment(self, features, context, current_verdict):
        """Apply conservative adjustments based on contextual environmental signals."""
        if not context:
            return None

        reasons_danger = []
        reasons_caution = []

        wind = features.get("wind_speed_kph", 0)
        gust = context.get("wind_gust_kph", wind)
        cloud_cover = context.get("cloud_cover", 0)
        max_forecast_wind = context.get("max_hourly_wind_kph", wind)
        max_forecast_gust = context.get("max_hourly_gust_kph", gust)
        max_forecast_rain = context.get("max_hourly_rain_mm", features.get("rainfall_mm", 0))
        severe_windows = context.get("severe_weather_windows", 0)
        alerts_present = context.get("alerts_present", False)
        weather_main = (context.get("weather_main") or "").lower()
        weather_description = context.get("weather_description", "")
        is_night = context.get("is_night", False)

        current_level = {"Safe": 0, "Caution": 1, "Dangerous": 2}.get(current_verdict, 1)

        flags = context.setdefault("flags", [])

        active_alerts = context.get("active_alerts", [])
        typhoon_alert = context.get("typhoon_alert")
        if typhoon_alert and not any(alert.get("is_typhoon") for alert in active_alerts):
            typhoon_alert = False

        if typhoon_alert:
            reason = "Active typhoon or tropical cyclone warning issued for this area"
            reasons_danger.append(reason)
            alerts_present = True

        dangerous_alerts = []
        caution_alerts = []

        severe_watch_keywords = (
            "signal",
            "storm surge",
            "tropical storm",
            "tropical cyclone",
            "typhoon",
            "hurricane"
        )

        for alert in active_alerts:
            severity = (alert.get("severity") or "information").lower()
            title = (alert.get("title") or "").strip()
            normalized_title = title.lower()

            is_moderate_flood = "flood" in normalized_title and "moderate" in normalized_title
            is_heavy_flood = "flood" in normalized_title and any(word in normalized_title for word in ("severe", "major", "heavy"))

            if alert.get("is_typhoon"):
                dangerous_alerts.append(alert)
                continue

            if severity == "warning":
                if is_moderate_flood and not is_heavy_flood:
                    caution_alerts.append(alert)
                else:
                    dangerous_alerts.append(alert)
                continue

            if severity == "watch":
                if any(keyword in normalized_title for keyword in severe_watch_keywords):
                    dangerous_alerts.append(alert)
                else:
                    caution_alerts.append(alert)
                continue

            caution_alerts.append(alert)

        alert_should_escalate = False
        if alerts_present:
            if typhoon_alert or dangerous_alerts:
                alert_should_escalate = True
            elif max_forecast_wind >= 45 or max_forecast_gust >= 60 or max_forecast_rain >= 20 or severe_windows >= 1:
                alert_should_escalate = True
            elif weather_main in {"thunderstorm", "tornado", "squall", "extreme"}:
                alert_should_escalate = True

            if alert_should_escalate:
                reasons_danger.append("Weather service issued an active severe alert for this area")
            elif caution_alerts:
                has_flood_advisory = any("flood" in (alert.get("title") or "").lower() for alert in caution_alerts)
                if has_flood_advisory:
                    reasons_caution.append("Moderate flood advisory in effect – expect localized flooding near shore")
                else:
                    reasons_caution.append("Official weather alert in effect – monitor conditions closely")
            else:
                reasons_caution.append("Official weather alert in effect – monitor conditions closely")

        if severe_windows >= 1 and not alert_should_escalate:
            reasons_danger.append("Thunderstorm or extreme weather expected in the next 6 hours")
        if weather_main in {"thunderstorm", "tornado", "squall", "extreme"} and not alert_should_escalate:
            reasons_danger.append(f"Severe weather detected ({weather_main.title()})")

        if max_forecast_wind >= 60 or max_forecast_gust >= 70:
            reasons_danger.append("Forecast wind/gust exceeds 60 kph within the next few hours")
        if max_forecast_rain >= 30:
            reasons_danger.append("Forecast rainfall exceeds 30mm soon")

        if reasons_danger and current_level < 2:
            for reason in reasons_danger:
                if reason not in flags:
                    flags.append(reason)
            return {
                "verdict": "Dangerous",
                "risk_level": 2,
                "probabilities": {"safe": 0.05, "caution": 0.2, "dangerous": 0.75},
                "reasons": reasons_danger
            }

        # Cautionary adjustments for marginal but concerning conditions
        if gust >= 45:
            reasons_caution.append("Wind gusts exceed 45 kph – handling small vessels is difficult")
        if max_forecast_wind >= 40:
            reasons_caution.append("Forecast winds exceed 40 kph in the next few hours")
        if max_forecast_rain >= 12:
            reasons_caution.append("Heavy rainfall expected soon (>= 12mm)")
        if cloud_cover >= 85 and is_night:
            reasons_caution.append("Nighttime with heavy cloud cover – very limited visibility")
        if "storm" in weather_description or "squall" in weather_description:
            reasons_caution.append(f"Storm conditions reported ({weather_description})")

        if reasons_caution and current_level == 0:
            for reason in reasons_caution:
                if reason not in flags:
                    flags.append(reason)
            return {
                "verdict": "Caution",
                "risk_level": 1,
                "probabilities": {"safe": 0.25, "caution": 0.55, "dangerous": 0.2},
                "reasons": reasons_caution
            }

        if reasons_caution:
            for reason in reasons_caution:
                if reason not in flags:
                    flags.append(reason)

        return None

    def get_recommendations(self, verdict, features, extra_data, override_reasons=None):
        """Generate comprehensive safety recommendations"""
        recommendations = []
        env_context = extra_data.get('environmental_context', {})
        
        # Primary verdict recommendations
        if verdict == "Dangerous":
            recommendations.append("🚨 DO NOT GO FISHING - Conditions are dangerous")
            recommendations.append("🏠 Stay on shore and wait for conditions to improve")
            recommendations.append("📱 Monitor weather updates regularly")
        elif verdict == "Caution":
            recommendations.append("⚠️ Exercise extreme caution if fishing")
            recommendations.append("🚤 Stay close to shore and inform others of your plans")
            recommendations.append("📱 Carry emergency communication devices")
            recommendations.append("⏰ Limit trip duration to essential activities only")
        else:
            recommendations.append("✅ Conditions are generally safe for fishing")
            recommendations.append("🎣 Good conditions for recreational fishing")
            recommendations.append("😌 Still practice normal safety precautions")

        if override_reasons:
            icon = "🚨" if verdict == "Dangerous" else "⚠️"
            for reason in override_reasons:
                recommendations.append(f"{icon} {reason}")
        
        # Specific condition warnings
        if features["wind_speed_kph"] > 30:
            recommendations.append(f"💨 Very high winds ({features['wind_speed_kph']:.1f} km/h) - avoid going out")
        elif features["wind_speed_kph"] > 20:
            recommendations.append(f"🌪️ Strong winds ({features['wind_speed_kph']:.1f} km/h) - use larger boats only")
        
        if features["wave_height_m"] > 2.5:
            recommendations.append(f"🌊 High waves ({features['wave_height_m']:.1f}m) - dangerous for small boats")
        elif features["wave_height_m"] > 1.5:
            recommendations.append(f"🌊 Moderate waves ({features['wave_height_m']:.1f}m) - use caution")
        
        if features["rainfall_mm"] > 5:
            recommendations.append(f"🌧️ Heavy rain ({features['rainfall_mm']:.1f}mm) - poor visibility and rough seas")
        
        if features["visibility_km"] < 5:
            recommendations.append(f"🌫️ Poor visibility ({features['visibility_km']:.1f}km) - stay very close to shore")
        
        # Tide recommendations
        if 'tide_data' in extra_data:
            tide = extra_data['tide_data']
            if tide['state'] == 'Rising':
                recommendations.append("🌊 Rising tide - good for fishing near structures")
            elif tide['state'] == 'Falling':
                recommendations.append("🌊 Falling tide - fish may move to deeper water")
        
        # UV protection
        if features.get("uv_index", 0) > 8:
            recommendations.append("☀️ High UV index - use sun protection")
        elif features.get("uv_index", 0) > 5:
            recommendations.append("🌤️ Moderate UV index - consider sun protection")
        
        # Environmental context advisories
        active_alerts = env_context.get("active_alerts", [])
        if active_alerts:
            alert = active_alerts[0]
            icon = "🌀" if alert.get("is_typhoon") else "📢"
            severity = alert.get("severity", "").title()
            title = alert.get("title", "Weather alert")
            if env_context.get("typhoon_alert"):
                recommendations.append("🌀 PAGASA/meteorological agencies report a typhoon affecting this area")
            recommendations.append(f"{icon} {severity or 'Alert'}: {title}")
        
        if env_context.get("alerts_present"):
            recommendations.append("📢 Follow official weather advisories and stay updated")
        if env_context.get("is_night"):
            recommendations.append("🌙 Limited visibility at night – ensure navigation lights and stay close to shore")
        if env_context.get("cloud_cover", 0) >= 85:
            recommendations.append("☁️ Heavy cloud cover – visibility and navigation markers may be obscured")
        if env_context.get("max_hourly_wind_kph", 0) >= 40:
            recommendations.append("🪁 Forecast winds increasing soon – reassess conditions before heading out")
        if env_context.get("max_hourly_rain_mm", 0) >= 12:
            recommendations.append("🌧️ Expect heavy rain in the next few hours – plan for rapid weather changes")

        # Historical incident warning
        if features["past_incidents_nearby"] > 3:
            recommendations.append("⚠️ This area has a history of fishing incidents - extra caution advised")
            for area in extra_data['incident_data']['nearby_risk_areas']:
                if area['incidents'] > 2:
                    recommendations.append(f"📍 Nearby risk area: {area['name']} ({area['distance_km']}km away) - {area['incidents']} reported incidents")
        
        return recommendations

    def record_prediction_debug(self, lat, lon, is_marine, features, env_context, prediction, extra_data):
        """Persist a concise debug snapshot for later inspection."""
        try:
            log_payload = {
                "timestamp": datetime.now().isoformat(),
                "lat": round(float(lat), 5),
                "lon": round(float(lon), 5),
                "is_marine": bool(is_marine),
                "verdict": prediction.get("verdict"),
                "risk_level": prediction.get("risk_level"),
                "confidence": round(float(prediction.get("confidence", 0)), 3),
                "wind_speed_kph": round(float(features.get("wind_speed_kph", 0)), 2),
                "wave_height_m": round(float(features.get("wave_height_m", 0)), 2),
                "rainfall_mm": round(float(features.get("rainfall_mm", 0)), 2),
                "visibility_km": round(float(features.get("visibility_km", 0)), 2),
                "past_incidents_nearby": round(float(features.get("past_incidents_nearby", 0)), 2),
                "override_reasons": prediction.get("override_reasons", []),
                "env_flags": env_context.get("flags", []),
                "max_hourly_wind_kph": round(float(env_context.get("max_hourly_wind_kph", 0)), 2),
                "max_hourly_gust_kph": round(float(env_context.get("max_hourly_gust_kph", 0)), 2),
                "max_hourly_rain_mm": round(float(env_context.get("max_hourly_rain_mm", 0)), 2),
                "alerts_present": bool(env_context.get("alerts_present", False)),
                "alert_titles": [
                    alert.get("title")
                    for alert in extra_data.get('alert_summary', {}).get('alerts', [])
                ],
            }

            log_path = os.path.join(BASE_DIR, "predict_log.txt")
            with open(log_path, "a", encoding="utf-8") as log_file:
                log_file.write(json.dumps(log_payload, ensure_ascii=True) + "\n")
        except Exception as exc:
            print(f"⚠️ Failed to write debug log: {exc}")

    def record_trip_outcome(self, lat, lon, features, verdict, trip_outcome, notes=None):
        """Record a completed trip's actual outcome to improve future training."""
        try:
            if trip_outcome not in {"Safe", "Caution", "Dangerous"}:
                raise ValueError(f"Invalid outcome: {trip_outcome}")

            new_row = {
                "trip_id": int(datetime.now().timestamp() * 1000),
                "location": f"PH_{lat:.3f}_{lon:.3f}",
                "wind_speed_kph": round(float(features.get("wind_speed_kph", 0)), 2),
                "wind_direction": int(features.get("wind_direction", 0)),
                "wave_height_m": round(float(features.get("wave_height_m", 0)), 2),
                "rainfall_mm": round(float(features.get("rainfall_mm", 0)), 2),
                "tide_level_m": round(float(features.get("tide_level_m", 0)), 2),
                "moon_phase": round(float(features.get("moon_phase", 0)), 3),
                "visibility_km": round(float(features.get("visibility_km", 0)), 2),
                "past_incidents_nearby": round(float(features.get("past_incidents_nearby", 0)), 2),
                "uv_index": round(float(features.get("uv_index", 0)), 1),
                "humidity": round(float(features.get("humidity", 0)), 1),
                "pressure": round(float(features.get("pressure", 0)), 2),
                "beaufort_scale": int(features.get("beaufort_scale", 0)),
                "wind_gust_kph": round(float(features.get("wind_gust_kph", 0)), 2),
                "cloud_cover": round(float(features.get("cloud_cover", 0)), 1),
                "api_verdict": verdict,
                "actual_verdict": trip_outcome,
                "verdict": trip_outcome,
                "recorded_at": datetime.now().isoformat(),
                "user_notes": (notes or "").strip()
            }

            if os.path.exists(DATA_PATH):
                df = pd.read_csv(DATA_PATH)
                df = pd.concat([df, pd.DataFrame([new_row])], ignore_index=True)
            else:
                df = pd.DataFrame([new_row])

            df.to_csv(DATA_PATH, index=False)
            print(f"✅ Trip outcome recorded: {trip_outcome} at ({lat:.3f}, {lon:.3f})")
            return True
        except Exception as e:
            print(f"❌ Failed to record trip outcome: {e}")
            return False

# Initialize the API
fishing_api = FishingSafetyAPI()

@app.route('/api/fishing-safety', methods=['POST'])
def check_fishing_safety():
    """Enhanced API endpoint for checking fishing safety"""
    try:
        data = request.get_json()
        # Validate input
        if not data or 'lat' not in data or 'lon' not in data:
            return jsonify({"error": "Latitude and longitude are required"}), 400
        
        lat = float(data['lat'])
        lon = float(data['lon'])
        
        # Validation for Philippines coastal areas
        if not (5.0 <= lat <= 20.0 and 116.0 <= lon <= 127.0):
            return jsonify({"error": "Location must be in Philippines area"}), 400
        
        print(f"🎣 Checking fishing safety for: {lat}, {lon}")
        
        # Fetch weather data and marine classification in parallel to reduce perceived latency
        with ThreadPoolExecutor(max_workers=2) as executor:
            weather_future = executor.submit(fishing_api.get_weather_data, lat, lon)
            marine_future = executor.submit(fishing_api.is_marine_location, lat, lon)
            weather_result = weather_future.result()
            is_marine = marine_future.result()

        if not weather_result:
            return jsonify({"error": "Failed to fetch weather data"}), 500

        onecall_data, location_name = weather_result

        # Extract features (waves default to calm if point appears on land)
        features, extra_data = fishing_api.extract_features_from_weather(
            onecall_data,
            lat,
            lon,
            is_marine=is_marine
        )
        if not features or not extra_data:
            return jsonify({"error": "Failed to extract weather features"}), 500
        
        # Make prediction
        env_context = extra_data.get('environmental_context', {})
        prediction = fishing_api.predict_safety(features, env_context)
        if not prediction:
            return jsonify({"error": "Failed to make safety prediction"}), 500
        
        # Prepare comprehensive response
        feature_vector = {
            key: (float(value) if isinstance(value, (int, float, np.floating, np.integer)) else value)
            for key, value in features.items()
        }

        response = {
            "location": {
                "latitude": lat,
                "longitude": lon,
                "name": location_name,
                "region": "Philippines",
                "is_marine": bool(is_marine)
            },
            "timestamp": datetime.now().isoformat(),
            "weather_conditions": {
                "wind_speed_kph": round(features["wind_speed_kph"], 1),
                "wind_direction": features["wind_direction"],
                "wave_height_m": round(features["wave_height_m"], 1),
                "sea_state": extra_data['marine_conditions']['sea_state'],
                "beaufort_scale": features["beaufort_scale"],
                "rainfall_mm": features["rainfall_mm"],
                "tide_level_m": round(features["tide_level_m"], 2),
                "tide_state": extra_data['tide_data']['state'],
                "visibility_km": round(features["visibility_km"], 1),
                "moon_phase": round(features["moon_phase"], 2),
                "uv_index": features.get("uv_index", 0),
                "humidity": features.get("humidity", 0),
                "pressure": features.get("pressure", 0),
                "wind_gust_kph": round(features.get("wind_gust_kph", 0), 1),
                "cloud_cover_pct": env_context.get("cloud_cover", 0),
                "is_night": env_context.get("is_night", False)
            },
            "feature_vector": feature_vector,
            "safety_assessment": prediction,
            "historical_data": {
                "past_incidents_nearby": features["past_incidents_nearby"],
                "risk_areas": extra_data['incident_data']['nearby_risk_areas']
            },
            "recommendations": fishing_api.get_recommendations(
                prediction["verdict"],
                features,
                extra_data,
                prediction.get("override_reasons")
            ),
            "environmental_context": env_context,
            "weather_alerts": extra_data['alert_summary']['alerts'],
            "typhoon_active": extra_data['alert_summary']['typhoon_active'],
            "severe_alert_present": extra_data['alert_summary']['severe_alert_present'],
            "is_marine_location": bool(is_marine)
        }
        
        fishing_api.record_prediction_debug(
            lat,
            lon,
            is_marine,
            features,
            env_context,
            prediction,
            extra_data
        )

        print(f"✅ Safety assessment complete: {prediction['verdict']}")
        return jsonify(response)
    except ValueError as e:
        return jsonify({"error": f"Invalid coordinates: {str(e)}"}), 400
    except Exception as e:
        print(f"❌ Error in safety check: {e}")
        traceback.print_exc()
        return jsonify({"error": f"Internal server error: {str(e)}"}), 500

@app.route('/api/fishing-safety/batch', methods=['POST'])
def check_multiple_locations():
    """Check safety for multiple locations (for map visualization)"""
    try:
        data = request.get_json()
        locations = data.get('locations', [])
        if not locations:
            return jsonify({"error": "No locations provided"}), 400
        if len(locations) > 20:
            return jsonify({"error": "Maximum 20 locations allowed per request"}), 400
        
        results = []
        for i, location in enumerate(locations):
            if 'lat' not in location or 'lon' not in location:
                continue
            try:
                lat, lon = float(location['lat']), float(location['lon'])
                # Quick validation
                if not (5.0 <= lat <= 20.0 and 116.0 <= lon <= 127.0):
                    continue
                
                weather_result = fishing_api.get_weather_data(lat, lon)
                if not weather_result:
                    continue
                
                onecall_data, _ = weather_result
                is_marine = fishing_api.is_marine_location(lat, lon)
                features, extra_data = fishing_api.extract_features_from_weather(
                    onecall_data,
                    lat,
                    lon,
                    is_marine=is_marine
                )
                if not features or not extra_data:
                    continue
                
                env_context = extra_data.get('environmental_context', {})
                prediction = fishing_api.predict_safety(features, env_context)
                if not prediction:
                    continue
                
                results.append({
                    "id": i,
                    "location": {"lat": lat, "lon": lon},
                    "is_marine": bool(is_marine),
                    "safety": prediction["verdict"],
                    "risk_level": prediction["risk_level"],
                    "confidence": prediction["confidence"],
                    "wind_speed": round(features["wind_speed_kph"], 1),
                    "wave_height": round(features["wave_height_m"], 1),
                    "sea_state": extra_data['marine_conditions']['sea_state'],
                    "environmental_flags": env_context.get('flags', [])
                })
            except Exception as e:
                print(f"❌ Error processing location {i}: {e}")
                continue
        
        return jsonify({
            "results": results,
            "total_processed": len(results),
            "timestamp": datetime.now().isoformat()
        })
    except Exception as e:
        print(f"❌ Error in batch processing: {e}")
        traceback.print_exc()
        return jsonify({"error": f"Internal server error: {str(e)}"}), 500

@app.route('/api/weather-map', methods=['POST'])
def get_weather_map_data():
    """Get weather data optimized for map display"""
    try:
        data = request.get_json()
        lat = float(data['lat'])
        lon = float(data['lon'])
        
        weather_result = fishing_api.get_weather_data(lat, lon)
        if not weather_result:
            return jsonify({"error": "Failed to fetch weather data"}), 500
        
        onecall_data, _ = weather_result
        current = onecall_data['current']
        
        wind_speed_kph = current.get('wind_speed', 0) * 3.6
        wind_direction = current.get('wind_deg', 0)
        marine_conditions = fishing_api.get_marine_conditions(lat, lon, current.get('wind_speed', 0))
        
        map_data = {
            "coordinates": {"lat": lat, "lon": lon},
            "wind": {
                "speed_kph": round(wind_speed_kph, 1),
                "direction": wind_direction,
                "beaufort": marine_conditions['beaufort_scale']
            },
            "waves": {
                "height_m": round(marine_conditions['wave_height_m'], 1),
                "description": marine_conditions['sea_state']
            },
            "visibility_km": round(current.get('visibility', 10000) / 1000, 1),
            "conditions": current['weather'][0]['description'] if current.get('weather') else "Unknown",
            "timestamp": datetime.now().isoformat()
        }
        return jsonify(map_data)
    except Exception as e:
        print(f"❌ Error in weather map data: {e}")
        traceback.print_exc()
        return jsonify({"error": f"Error fetching map data: {str(e)}"}), 500

@app.route('/api/health', methods=['GET'])
def health_check():
    """Enhanced health check endpoint"""
    model_status = "loaded" if fishing_api.model else "fallback_mode"
    api_key_status = "configured" if OPENWEATHER_API_KEY and OPENWEATHER_API_KEY != "your_openweather_api_key_here" else "missing"
    
    return jsonify({
        "status": "healthy",
        "model_status": model_status,
        "api_key_status": api_key_status,
        "timestamp": datetime.now().isoformat(),
        "version": "1.0"
    })

@app.route('/api/setup-check', methods=['GET'])
def setup_check():
    """Check if the API is properly configured"""
    checks = {
        "model_file": os.path.exists(MODEL_PATH),
        "data_file": os.path.exists(DATA_PATH),
        "openweather_api_key": OPENWEATHER_API_KEY and OPENWEATHER_API_KEY != "your_openweather_api_key_here",
        "model_loaded": fishing_api.model is not None
    }
    all_good = all(checks.values())
    
    recommendations = []
    if not checks["openweather_api_key"]:
        recommendations.append("Get OpenWeatherMap API key from https://home.openweathermap.org/api_keys and add to .env file")
    if not checks["model_file"]:
        recommendations.append("Train and save your ML model as 'fishing_trip_risk_model.pkl'")
    if not checks["data_file"]:
        recommendations.append("Prepare your training dataset as 'fishing_trip_risk_dataset.csv'")
    if not checks["model_loaded"]:
        recommendations.append("Fix model loading issues - check format and dependencies")
    
    return jsonify({
        "setup_complete": all_good,
        "checks": checks,
        "recommendations": recommendations
    })

@app.route('/api/record-trip-outcome', methods=['POST'])
def record_trip_outcome():
    """Record the actual outcome of a completed fishing trip for model retraining."""
    try:
        data = request.get_json()
        
        # Validate required fields
        if not data or 'lat' not in data or 'lon' not in data or 'outcome' not in data:
            return jsonify({"error": "Missing required fields: lat, lon, outcome"}), 400
        
        lat = float(data['lat'])
        lon = float(data['lon'])
        outcome = data['outcome'].strip()
        
        if outcome not in {"Safe", "Caution", "Dangerous"}:
            return jsonify({"error": f"Invalid outcome. Must be one of: Safe, Caution, Dangerous"}), 400
        
        # Extract features from the request (API should send these)
        if 'features' not in data:
            return jsonify({"error": "Missing 'features' object"}), 400
        
        features = data['features']
        api_verdict = data.get('api_verdict', 'Unknown')
        
        # Record the outcome
        success = fishing_api.record_trip_outcome(
            lat,
            lon,
            features,
            api_verdict,
            outcome,
            data.get("notes")
        )
        
        if success:
            return jsonify({
                "status": "recorded",
                "message": f"Trip outcome '{outcome}' recorded successfully",
                "note": "Run train_random_forest.py to retrain the model with new data",
                "timestamp": datetime.now().isoformat()
            }), 201
        else:
            return jsonify({"error": "Failed to record trip outcome"}), 500
    except ValueError as e:
        return jsonify({"error": f"Invalid data: {str(e)}"}), 400
    except Exception as e:
        print(f"❌ Error recording trip outcome: {e}")
        traceback.print_exc()
        return jsonify({"error": f"Internal server error: {str(e)}"}), 500

# Legacy compatibility alias for older frontend calls hitting /zone
# Provides a lightweight safety + conditions summary without requiring frontend changes.
@app.route('/zone', methods=['GET'])
def zone_alias():
    try:
        lat = request.args.get('lat', type=float)
        lon = request.args.get('lon', type=float)
        if lat is None or lon is None:
            return jsonify({"error": "lat and lon query parameters are required"}), 400

        # Philippines bounds validation (same as core endpoints)
        if not (5.0 <= lat <= 20.0 and 116.0 <= lon <= 127.0):
            return jsonify({"error": "Location must be in Philippines area"}), 400

        weather_result = fishing_api.get_weather_data(lat, lon)
        if not weather_result:
            return jsonify({"error": "Failed to fetch weather data"}), 500

        onecall_data, location_name = weather_result
        is_marine = fishing_api.is_marine_location(lat, lon)
        features, extra_data = fishing_api.extract_features_from_weather(
            onecall_data,
            lat,
            lon,
            is_marine=is_marine
        )
        if not features or not extra_data:
            return jsonify({"error": "Failed to extract weather features"}), 500

        env_context = extra_data.get('environmental_context', {})
        prediction = fishing_api.predict_safety(features, env_context)
        if not prediction:
            return jsonify({"error": "Failed to make safety prediction"}), 500

        return jsonify({
            "location_name": location_name,
            "lat": lat,
            "lon": lon,
            "verdict": prediction["verdict"],
            "risk_level": prediction["risk_level"],
            "wind_speed_kph": round(features.get("wind_speed_kph", 0), 1),
            "wave_height_m": round(features.get("wave_height_m", 0), 1),
            "is_marine": bool(is_marine),
            "environmental_flags": env_context.get("flags", [])
        })
    except ValueError as e:
        return jsonify({"error": f"Invalid coordinates: {str(e)}"}), 400
    except Exception as e:
        print(f"❌ Error in /zone alias: {e}")
        traceback.print_exc()
        return jsonify({"error": f"Internal server error: {str(e)}"}), 500

if __name__ == '__main__':
    print("🎣 Starting Enhanced Fishing Safety API for Laravel...")
    print("✅ Laravel .env integration enabled")
    print("📍 Available endpoints:")
    print("  POST /api/fishing-safety - Check safety for a single location")
    print("  POST /api/fishing-safety/batch - Check safety for multiple locations")
    print("  POST /api/weather-map - Get weather data for map display")
    print("  GET /api/health - Health check")
    print("  GET /api/setup-check - Configuration check")
    print("\n🔧 Setup Instructions:")
    print("1. Get OpenWeatherMap API key: https://home.openweathermap.org/api_keys")
    print("2. Add to your Laravel .env file: OPENWEATHER_API_KEY=your_key_here")
    print("3. Train your ML model and save as 'fishing_trip_risk_model.pkl'")
    print("4. Prepare dataset as 'fishing_trip_risk_dataset.csv'")
    print("5. API will automatically read from Laravel's .env file")
    
    app.run(debug=True, host='0.0.0.0', port=5000)