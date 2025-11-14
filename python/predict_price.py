"""Predict optimal price multiplier for marketplace listings.

This script loads the trained pricing model and predicts the optimal
price multiplier based on product features.

Usage from command line:
    python predict_price.py <freshness_score> <available_quantity> <demand_factor> \
        <seasonality_factor> <time_of_day> <vendor_rating> <category_id>

Usage from PHP (via shell_exec or similar):
    python predict_price.py 85 50 1.2 1.5 10 4.5 3

Returns:
    JSON with predicted multiplier: {"multiplier": 1.234, "confidence": 0.95}
"""
from __future__ import annotations

import json
import sys
from pathlib import Path
from typing import Dict

import joblib
import numpy as np

BASE_DIR = Path(__file__).resolve().parent
MODEL_PATH = BASE_DIR / "pricing_model.pkl"

# Feature names (must match training script)
MODEL_FEATURES = [
    "freshness_score",
    "available_quantity",
    "demand_factor",
    "seasonality_factor",
    "time_of_day",
    "vendor_rating",
    "category_id",
]


def load_model():
    """Load the trained pricing model."""
    if not MODEL_PATH.exists():
        raise FileNotFoundError(
            f"Pricing model not found at {MODEL_PATH}. "
            f"Run train_pricing_model.py first."
        )
    return joblib.load(MODEL_PATH)


def predict_multiplier(
    freshness_score: float,
    available_quantity: int,
    demand_factor: float,
    seasonality_factor: float,
    time_of_day: int,
    vendor_rating: float,
    category_id: int,
) -> Dict[str, float]:
    """
    Predict optimal price multiplier.
    
    Args:
        freshness_score: 0-100, higher = fresher
        available_quantity: Stock level
        demand_factor: Recent buyer interest (0-2)
        seasonality_factor: Product seasonality (0-2)
        time_of_day: Hour 0-23
        vendor_rating: Vendor reputation 1-5
        category_id: Fish type encoded
    
    Returns:
        Dict with 'multiplier' and 'confidence'
    """
    model = load_model()
    
    # Prepare feature vector
    features = np.array([[
        freshness_score,
        available_quantity,
        demand_factor,
        seasonality_factor,
        time_of_day,
        vendor_rating,
        category_id,
    ]])
    
    # Predict
    multiplier = model.predict(features)[0]
    
    # Clamp to reasonable range
    multiplier = max(0.8, min(1.8, multiplier))
    
    # Simple confidence estimate based on tree variance
    # (For GradientBoostingRegressor, we can use estimators for variance)
    if hasattr(model, 'estimators_'):
        predictions = [tree[0].predict(features)[0] for tree in model.estimators_]
        variance = np.var(predictions)
        confidence = max(0.5, 1.0 - min(variance * 2, 0.5))
    else:
        confidence = 0.85  # Default confidence
    
    return {
        "multiplier": round(float(multiplier), 4),
        "confidence": round(float(confidence), 4),
    }


def main():
    """Command-line interface."""
    if len(sys.argv) != 8:
        print(
            "Usage: python predict_price.py <freshness_score> <available_quantity> "
            "<demand_factor> <seasonality_factor> <time_of_day> <vendor_rating> <category_id>",
            file=sys.stderr,
        )
        sys.exit(1)
    
    try:
        freshness_score = float(sys.argv[1])
        available_quantity = int(sys.argv[2])
        demand_factor = float(sys.argv[3])
        seasonality_factor = float(sys.argv[4])
        time_of_day = int(sys.argv[5])
        vendor_rating = float(sys.argv[6])
        category_id = int(sys.argv[7])
        
        result = predict_multiplier(
            freshness_score=freshness_score,
            available_quantity=available_quantity,
            demand_factor=demand_factor,
            seasonality_factor=seasonality_factor,
            time_of_day=time_of_day,
            vendor_rating=vendor_rating,
            category_id=category_id,
        )
        
        print(json.dumps(result))
        
    except Exception as e:
        error_result = {"error": str(e), "multiplier": 1.0, "confidence": 0.0}
        print(json.dumps(error_result), file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    main()
