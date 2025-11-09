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
            if not OPENWEATHER_API_KEY or OPENWEATHER_API_KEY == "your_openweather_api_key_here":
                print("⚠️ API key not configured - using mock weather data")
                return self.get_mock_weather_data(lat, lon)
            
            # One Call API 3.0 - gets everything in one call!
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
            onecall_data = response.json()
            
            # Get location name from reverse geocoding (basic)
            location_url = "http://api.openweathermap.org/geo/1.0/reverse"
            location_params = {
                'lat': lat,
                'lon': lon,
                'limit': 1,
                'appid': OPENWEATHER_API_KEY
            }
            location_response = requests.get(location_url, params=location_params, timeout=5)
            location_name = "Unknown location"
            if location_response.status_code == 200:
                location_data = location_response.json()
                if location_data and len(location_data) > 0:
                    location_name = location_data[0].get('name', 'Unknown location')
            
            print(f"✅ Weather data fetched for: {location_name} ({lat}, {lon})")
            return onecall_data, location_name
        except requests.exceptions.RequestException as e:
            print(f"❌ API request failed: {e}")
            return self.get_mock_weather_data(lat, lon)
        except Exception as e:
            print(f"❌ Error fetching weather data: {e}")
            return self.get_mock_weather_data(lat, lon)
    
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

        keywords = ('typhoon', 'tropical storm', 'tropical cyclone', 'hurricane', 'signal', 'storm surge')

        for alert in alerts:
            title = (alert.get('event') or 'Weather Alert').strip()
            description = (alert.get('description') or '').strip()
            severity = (alert.get('severity') or '').strip().lower()
            sender = (alert.get('sender_name') or '').strip()

            combined = f"{title} {description}".lower()
            is_typhoon = any(keyword in combined for keyword in keywords)
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
    
    def extract_features_from_weather(self, onecall_data, lat, lon):
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
            marine_conditions = self.get_marine_conditions(lat, lon, wind_speed_ms)
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
                "wave_height_m": wave_height_m,
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

        if context.get("typhoon_alert"):
            reasons_danger.append("Active typhoon or tropical cyclone warning issued for this area")
            alerts_present = True

        # Official alerts or forecasted extreme conditions should escalate to Dangerous
        if alerts_present:
            reasons_danger.append("Weather service issued an active alert for this area")
        if max_forecast_wind >= 60 or max_forecast_gust >= 70:
            reasons_danger.append("Forecast wind/gust exceeds 60 kph within the next few hours")
        if severe_windows >= 1:
            reasons_danger.append("Thunderstorm or extreme weather expected in the next 6 hours")
        if weather_main in {"thunderstorm", "tornado", "squall", "extreme"}:
            reasons_danger.append(f"Severe weather detected ({weather_main.title()})")
        if max_forecast_rain >= 30:
            reasons_danger.append("Forecast rainfall exceeds 30mm soon")

        if reasons_danger and current_level < 2:
            context.setdefault("flags", []).extend(reasons_danger)
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
            context.setdefault("flags", []).extend(reasons_caution)
            return {
                "verdict": "Caution",
                "risk_level": 1,
                "probabilities": {"safe": 0.25, "caution": 0.55, "dangerous": 0.2},
                "reasons": reasons_caution
            }

        if reasons_caution:
            context.setdefault("flags", []).extend(reasons_caution)

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
        
        # Get weather data
        weather_result = fishing_api.get_weather_data(lat, lon)
        if not weather_result:
            return jsonify({"error": "Failed to fetch weather data"}), 500
        
        onecall_data, location_name = weather_result
        
        # Extract features
        features, extra_data = fishing_api.extract_features_from_weather(onecall_data, lat, lon)
        if not features or not extra_data:
            return jsonify({"error": "Failed to extract weather features"}), 500
        
        # Make prediction
        env_context = extra_data.get('environmental_context', {})
        prediction = fishing_api.predict_safety(features, env_context)
        if not prediction:
            return jsonify({"error": "Failed to make safety prediction"}), 500
        
        # Prepare comprehensive response
        response = {
            "location": {
                "latitude": lat,
                "longitude": lon,
                "name": location_name,
                "region": "Philippines"
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
            "severe_alert_present": extra_data['alert_summary']['severe_alert_present']
        }
        
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
                features, extra_data = fishing_api.extract_features_from_weather(onecall_data, lat, lon)
                if not features or not extra_data:
                    continue
                
                env_context = extra_data.get('environmental_context', {})
                prediction = fishing_api.predict_safety(features, env_context)
                if not prediction:
                    continue
                
                results.append({
                    "id": i,
                    "location": {"lat": lat, "lon": lon},
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