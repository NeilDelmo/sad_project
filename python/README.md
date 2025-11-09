# Fishing Safety Prediction API

## Setup Instructions

### 1. Install Python Dependencies
```bash
cd python
pip install -r requirements.txt
```

### 2. Configure API Key
Add your OpenWeather API key to the Laravel `.env` file in the root directory:

```env
OPENWEATHER_API_KEY=your_api_key_here
```

**Get your free API key:**
1. Sign up at https://openweathermap.org/api
2. Go to https://home.openweathermap.org/api_keys
3. Copy your API key
4. Paste it in the `.env` file

**Free Tier:** 1,000 API calls/day (One Call API 3.0)

### 3. Run the Flask API Server
```bash
python predict_risk.py
```

The API will be available at: `http://localhost:5000`

## API Endpoints

### POST /api/fishing-safety
Check fishing safety for a specific location.

**Request:**
```json
{
  "lat": 13.8500,
  "lon": 120.6167
}
```

**Response:**
```json
{
  "location": {
    "latitude": 13.85,
    "longitude": 120.6167,
    "area": "Balibago, Calatagan, Batangas"
  },
  "timestamp": "2025-11-09T10:30:00",
  "weather_conditions": {
    "wind_speed_kph": 15.2,
    "wave_height_m": 1.5,
    "rainfall_mm": 0,
    "tide_level_m": 1.2,
    "visibility_km": 10,
    "moon_phase": 0.25
  },
  "safety_assessment": {
    "verdict": "Safe",
    "risk_level": 0,
    "confidence": 0.85,
    "probabilities": {
      "safe": 0.85,
      "caution": 0.12,
      "dangerous": 0.03
    }
  },
  "recommendations": [
    "✅ Conditions are generally safe for fishing"
  ]
}
```

### GET /api/health
Health check endpoint.

## Security Notes

- **NEVER** commit your `.env` file to GitHub
- The `.env` file is already in `.gitignore`
- Share only `.env.example` which has empty values
- Each developer should create their own `.env` file with their API key

## Model Files

- `fishing_trip_risk_model.pkl` - Trained ML model
- `fishing_trip_risk_dataset.csv` - Training dataset
- `predict_risk.py` - Flask API server

## Dependencies

See `requirements.txt` for the full list of Python packages needed.
