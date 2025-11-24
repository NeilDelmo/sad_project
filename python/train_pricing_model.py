"""Train a pricing model for marketplace dynamic pricing.

This script generates a synthetic dataset of marketplace pricing scenarios
and trains a Decision Tree Regressor to predict optimal price multipliers
based on freshness, demand, seasonality, and other market factors.

Run with the project virtualenv activated:

    (venv) $ python train_pricing_model.py
"""
from __future__ import annotations

import json
from pathlib import Path
from typing import Dict, Tuple

import joblib
import numpy as np
import pandas as pd
from sklearn.ensemble import GradientBoostingRegressor
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
from sklearn.model_selection import train_test_split

BASE_DIR = Path(__file__).resolve().parent
MODEL_PATH = BASE_DIR / "pricing_model.pkl"
REPORT_PATH = BASE_DIR / "pricing_model_report.json"
DATASET_PATH = BASE_DIR / "pricing_dataset.csv"
TRAINING_FRAME_PATH = BASE_DIR / "pricing_training_frame.csv"

MIN_REAL_ROWS = 500
HYBRID_TARGET_ROWS = 3000

# Feature names expected by predict_price.py
MODEL_FEATURES = [
    "freshness_score",      # 0-100, higher = fresher
    "available_quantity",   # Stock level
    "demand_factor",        # Recent buyer interest (0-2)
    "seasonality_factor",   # Product seasonality (0-2)
    "time_of_day",          # Hour 0-23
    "vendor_rating",        # Vendor reputation 1-5
    "category_id",          # Fish type encoded
]


def generate_synthetic_dataset(n_samples: int = 5000) -> pd.DataFrame:
    """Generate realistic marketplace pricing scenarios."""
    np.random.seed(42)
    
    data = {
        "freshness_score": np.random.randint(20, 100, n_samples),
        "available_quantity": np.random.randint(1, 200, n_samples),
        "demand_factor": np.random.uniform(0.3, 2.0, n_samples),
        "seasonality_factor": np.random.uniform(0.5, 2.0, n_samples),
        "time_of_day": np.random.randint(0, 24, n_samples),
        "vendor_rating": np.random.uniform(2.0, 5.0, n_samples),
        "category_id": np.random.randint(1, 10, n_samples),
    }
    
    df = pd.DataFrame(data)
    
    # Generate optimal price multiplier based on realistic business logic
    # Base multiplier starts at 1.0
    multiplier = np.ones(n_samples)
    
    # Freshness boost: fresher = higher price (up to +30%)
    multiplier += (df["freshness_score"] / 100) * 0.3
    
    # Demand impact: high demand = higher price
    multiplier += (df["demand_factor"] - 1.0) * 0.25
    
    # Seasonality impact
    multiplier += (df["seasonality_factor"] - 1.0) * 0.2
    
    # Scarcity premium: low stock = higher price
    scarcity = np.clip(1 - (df["available_quantity"] / 200), 0, 1)
    multiplier += scarcity * 0.2
    
    # Vendor reputation bonus
    multiplier += (df["vendor_rating"] - 3.0) * 0.05
    
    # Peak hours (6-9 AM, 4-7 PM) get small premium
    peak_hours = ((df["time_of_day"] >= 6) & (df["time_of_day"] <= 9)) | \
                 ((df["time_of_day"] >= 16) & (df["time_of_day"] <= 19))
    multiplier += peak_hours.astype(float) * 0.1
    
    # Add realistic noise
    noise = np.random.normal(0, 0.05, n_samples)
    multiplier += noise
    
    # Clamp multiplier to reasonable range (0.8 to 1.8)
    df["optimal_price_multiplier"] = np.clip(multiplier, 0.8, 1.8)
    
    return df


def load_real_dataset(path: Path) -> pd.DataFrame | None:
    """Load exported marketplace data if available and well-formed."""
    if not path.exists():
        print(f"⚠️ Real pricing dataset not found at {path}.")
        return None

    try:
        df = pd.read_csv(path)
    except Exception as exc:  # pragma: no cover - informative logging only
        print(f"⚠️ Failed to read pricing dataset: {exc}")
        return None

    required_columns = MODEL_FEATURES + ["optimal_price_multiplier"]
    missing = [col for col in required_columns if col not in df.columns]
    if missing:
        print(f"⚠️ Dataset missing required columns: {missing}")
        return None

    df = df.dropna(subset=required_columns).reset_index(drop=True)
    for column in required_columns:
        df[column] = pd.to_numeric(df[column], errors="coerce")

    df = df.dropna(subset=required_columns).reset_index(drop=True)
    if df.empty:
        print("⚠️ Exported dataset had no usable rows after cleaning.")
        return None

    return df


def prepare_training_frame() -> Tuple[pd.DataFrame, str]:
    """Return a cleaned dataset and note whether it is real, hybrid, or synthetic."""
    real_df = load_real_dataset(DATASET_PATH)
    dataset_source = "real"

    if real_df is None:
        dataset_source = "synthetic-only"
        df = generate_synthetic_dataset(n_samples=HYBRID_TARGET_ROWS)
        # Persist so the dataset exists for future runs/inspection.
        df.to_csv(DATASET_PATH, index=False)
        print(f"💾 Synthetic dataset saved to {DATASET_PATH}")
    else:
        df = real_df
        if len(real_df) < MIN_REAL_ROWS:
            needed = max(HYBRID_TARGET_ROWS - len(real_df), MIN_REAL_ROWS)
            synthetic = generate_synthetic_dataset(n_samples=needed)
            df = pd.concat([real_df, synthetic], ignore_index=True)
            dataset_source = "hybrid"

    TRAINING_FRAME_PATH.parent.mkdir(parents=True, exist_ok=True)
    df.to_csv(TRAINING_FRAME_PATH, index=False)
    print(f"📦 Training frame cached to {TRAINING_FRAME_PATH}")

    return df, dataset_source


def train_model(df: pd.DataFrame) -> Tuple[GradientBoostingRegressor, Dict]:
    """Train the pricing model and return metrics."""
    X = df[MODEL_FEATURES]
    y = df["optimal_price_multiplier"]
    
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.25, random_state=42
    )
    
    model = GradientBoostingRegressor(
        n_estimators=100,
        max_depth=5,
        learning_rate=0.1,
        min_samples_split=10,
        min_samples_leaf=4,
        subsample=0.8,
        random_state=42,
    )
    
    model.fit(X_train, y_train)
    
    # Predictions and metrics
    y_pred = model.predict(X_test)
    
    mae = mean_absolute_error(y_test, y_pred)
    mse = mean_squared_error(y_test, y_pred)
    rmse = np.sqrt(mse)
    r2 = r2_score(y_test, y_pred)
    
    # Feature importance
    feature_importance = dict(zip(MODEL_FEATURES, model.feature_importances_.tolist()))
    
    metrics = {
        "model_type": "GradientBoostingRegressor",
        "mae": float(mae),
        "mse": float(mse),
        "rmse": float(rmse),
        "r2_score": float(r2),
        "feature_importance": feature_importance,
        "n_train": len(X_train),
        "n_test": len(X_test),
    }
    
    return model, metrics


def main() -> None:
    df, dataset_source = prepare_training_frame()
    print(
        f"🔄 Training with {dataset_source} dataset containing {len(df):,} rows"
    )
    
    print("🤖 Training Gradient Boosting pricing model...")
    model, metrics = train_model(df)
    
    # Save model
    MODEL_PATH.parent.mkdir(parents=True, exist_ok=True)
    joblib.dump(model, MODEL_PATH)
    print(f"✅ Model saved to {MODEL_PATH}")
    
    # Save metrics report
    metrics["dataset_source"] = dataset_source
    metrics["dataset_rows"] = int(len(df))
    REPORT_PATH.write_text(json.dumps(metrics, indent=2) + "\n", encoding="utf-8")
    print(f"📊 Metrics report saved to {REPORT_PATH}")
    
    print("\n📈 Model Performance:")
    print(f"  - R² Score: {metrics['r2_score']:.4f}")
    print(f"  - MAE: {metrics['mae']:.4f}")
    print(f"  - RMSE: {metrics['rmse']:.4f}")
    
    print("\n🔍 Feature Importance:")
    for feature, importance in sorted(
        metrics["feature_importance"].items(), key=lambda x: x[1], reverse=True
    ):
        print(f"  - {feature}: {importance:.4f}")


if __name__ == "__main__":
    main()
