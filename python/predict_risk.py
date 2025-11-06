import pandas as pd
import joblib
import json
import sys
import os
import traceback
import warnings

warnings.filterwarnings("ignore", category=UserWarning)
warnings.filterwarnings("ignore", category=FutureWarning)

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_PATH = os.path.join(BASE_DIR, "fishing_trip_risk_model.pkl")
DATA_PATH = os.path.join(BASE_DIR, "fishing_trip_risk_dataset.csv")
LOG_PATH = os.path.join(BASE_DIR, "predict_log.txt")

def log_error(msg):
    with open(LOG_PATH, "a") as f:
        f.write(msg + "\n")

try:
    # Load model
    model = joblib.load(MODEL_PATH)

    # Load dataset to recreate label encoding
    df_data = pd.read_csv(DATA_PATH)
    location_mapping = {name: i for i, name in enumerate(sorted(df_data['location'].unique()))}

    # Parse input
    data = json.loads(sys.argv[1])

    # Encode location numerically
    loc_name = data["location"]
    loc_value = location_mapping.get(loc_name, -1)  # -1 if unseen

    # Prepare features
    df = pd.DataFrame([{
        "wind_speed_kph": float(data["wind_speed_kph"]),
        "wave_height_m": float(data["wave_height_m"]),
        "rainfall_mm": float(data["rainfall_mm"]),
        "tide_level_m": float(data["tide_level_m"]),
        "moon_phase": float(data["moon_phase"]),
        "visibility_km": float(data["visibility_km"]),
        "past_incidents_nearby": float(data["past_incidents_nearby"]),
        "location": loc_value
    }])

    # Predict
    prediction = model.predict(df)[0]
    verdict_map = {0: "Safe", 1: "Caution", 2: "Dangerous"}
    verdict = verdict_map.get(prediction, str(prediction))

    print(verdict)

except Exception as e:
    log_error(f"Error during prediction: {e}\n{traceback.format_exc()}")
    print("Error: Prediction failed. Check logs.")
