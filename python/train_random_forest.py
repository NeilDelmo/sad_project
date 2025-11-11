"""Train the Random Forest classifier used by predict_risk.py.

This script reads `fishing_trip_risk_dataset.csv`, performs lightweight feature
engineering so the resulting columns line up with the runtime feature vector,
then trains a RandomForestClassifier and stores it at
`fishing_trip_risk_model.pkl`.

Run with the project virtualenv activated:

    (venv) $ python train_random_forest.py
"""
from __future__ import annotations

import json
from pathlib import Path
from typing import Dict, Tuple

import joblib
import numpy as np
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix
from sklearn.model_selection import train_test_split

BASE_DIR = Path(__file__).resolve().parent
DATA_PATH = BASE_DIR / "fishing_trip_risk_dataset.csv"
MODEL_PATH = BASE_DIR / "fishing_trip_risk_model.pkl"
REPORT_PATH = BASE_DIR / "training_report.json"

# Columns expected by predict_risk.extract_features_from_weather
MODEL_FEATURES = [
    "wind_speed_kph",
    "wind_direction",
    "wave_height_m",
    "rainfall_mm",
    "tide_level_m",
    "moon_phase",
    "visibility_km",
    "past_incidents_nearby",
    "location",
    "uv_index",
    "humidity",
    "pressure",
    "beaufort_scale",
    "wind_gust_kph",
    "cloud_cover",
]

PHASE_MAP: Dict[str, float] = {
    "New Moon": 0.0,
    "Waxing Crescent": 0.125,
    "First Quarter": 0.25,
    "Waxing Gibbous": 0.375,
    "Full Moon": 0.5,
    "Waning Gibbous": 0.625,
    "Last Quarter": 0.75,
    "Waning Crescent": 0.875,
}

TARGET_MAP: Dict[str, int] = {"Safe": 0, "Caution": 1, "Dangerous": 2}


def _beaufort_from_wind(speed_kph: float) -> int:
    """Helper to match the Beaufort thresholds used in the API."""
    thresholds = [
        (1, 0),
        (6, 1),
        (12, 2),
        (20, 3),
        (29, 4),
        (39, 5),
        (50, 6),
        (62, 7),
        (75, 8),
        (89, 9),
        (103, 10),
        (118, 11),
    ]
    for limit, value in thresholds:
        if speed_kph < limit:
            return value
    return 12


def _prepare_features(df: pd.DataFrame) -> Tuple[pd.DataFrame, pd.Series, Dict[str, int]]:
    df = df.copy()

    # Encode verdict labels
    if "verdict" not in df:
        raise ValueError("Dataset must contain a 'verdict' column.")
    if not set(df["verdict"]).issubset(TARGET_MAP):
        missing = sorted(set(df["verdict"]) - set(TARGET_MAP))
        raise ValueError(f"Dataset contains unexpected verdict labels: {missing}")

    y = df["verdict"].map(TARGET_MAP).astype(int)

    # Deterministic pseudo-random wind direction based on trip_id to avoid leaking
    # real coordinates while keeping values in [0, 360).
    if "trip_id" in df:
        df["wind_direction"] = (df["trip_id"].astype(int) * 137) % 360
    else:
        df["wind_direction"] = 0.0

    # Map moon phases to a numeric cycle value and handle numeric fallbacks.
    moon_raw = df["moon_phase"]
    moon_numeric = pd.to_numeric(moon_raw, errors="coerce")
    df["moon_phase_value"] = moon_raw.map(PHASE_MAP)
    df.loc[df["moon_phase_value"].isna(), "moon_phase_value"] = moon_numeric[df["moon_phase_value"].isna()]
    df["moon_phase_value"] = df["moon_phase_value"].fillna(0.5).astype(float)

    # Encode location to integers (same approach as API load_model).
    location_mapping = {
        name: idx for idx, name in enumerate(sorted(df["location"].astype(str).unique()))
    }
    df["location_encoded"] = df["location"].map(location_mapping).fillna(0).astype(int)

    # Derived meteorological estimates to complete the feature vector.
    df["wind_gust_kph"] = (df["wind_speed_kph"] * 1.18).clip(lower=0)
    df["beaufort_scale"] = df["wind_speed_kph"].apply(_beaufort_from_wind)
    df["cloud_cover"] = (
        20 + df["rainfall_mm"] * 2.0 + df["wave_height_m"] * 5.0
    ).clip(0, 100)
    df["uv_index"] = np.clip(11 - df["cloud_cover"] / 15.0, 0, 11)
    df["humidity"] = np.clip(70 + df["rainfall_mm"] * 1.2, 55, 100)
    df["pressure"] = np.clip(1013 - df["wind_speed_kph"] * 0.3 + df["tide_level_m"] * 1.1, 980, 1035)

    # Assemble the training matrix with the column names used at runtime.
    feature_frame = pd.DataFrame({
        "wind_speed_kph": df["wind_speed_kph"],
        "wind_direction": df["wind_direction"],
        "wave_height_m": df["wave_height_m"],
        "rainfall_mm": df["rainfall_mm"],
        "tide_level_m": df["tide_level_m"],
        "moon_phase": df["moon_phase_value"],
        "visibility_km": df["visibility_km"],
        "past_incidents_nearby": df["past_incidents_nearby"],
        "location": df["location_encoded"],
        "uv_index": df["uv_index"],
        "humidity": df["humidity"],
        "pressure": df["pressure"],
        "beaufort_scale": df["beaufort_scale"],
        "wind_gust_kph": df["wind_gust_kph"],
        "cloud_cover": df["cloud_cover"],
    })

    # Ensure column ordering matches MODEL_FEATURES exactly.
    feature_frame = feature_frame[MODEL_FEATURES]

    return feature_frame, y, location_mapping


def main() -> None:
    if not DATA_PATH.exists():
        raise FileNotFoundError(f"Dataset not found at {DATA_PATH}")

    df = pd.read_csv(DATA_PATH)
    X, y, location_mapping = _prepare_features(df)

    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.25, stratify=y, random_state=42
    )

    model = RandomForestClassifier(
        n_estimators=300,
        max_depth=None,
        min_samples_split=4,
        min_samples_leaf=2,
        class_weight="balanced",
        random_state=42,
        n_jobs=-1,
    )
    model.fit(X_train, y_train)

    y_pred = model.predict(X_test)
    y_prob = model.predict_proba(X_test)

    accuracy = accuracy_score(y_test, y_pred)
    report = classification_report(y_test, y_pred, output_dict=True, zero_division=0)
    cm = confusion_matrix(y_test, y_pred).tolist()

    MODEL_PATH.parent.mkdir(parents=True, exist_ok=True)
    joblib.dump(model, MODEL_PATH)

    REPORT_PATH.write_text(
        json.dumps(
            {
                "accuracy": accuracy,
                "classification_report": report,
                "confusion_matrix": cm,
                "feature_names": list(model.feature_names_in_),
                "location_mapping": location_mapping,
            },
            indent=2,
        )
        + "\n",
        encoding="utf-8",
    )

    print("✅ Random Forest model trained and saved to", MODEL_PATH)
    print("📊 Accuracy:", f"{accuracy:.3f}")
    print("🔍 Classification report written to", REPORT_PATH)


if __name__ == "__main__":
    main()
