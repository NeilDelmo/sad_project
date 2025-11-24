<?php

namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Provides fair-market pricing guidance for fishermen based on live market signals
 * and the shared ML pricing model (with conservative adjustments).
 */
class FishermanPricingService
{
	public function __construct(
		private MarketSignalService $marketSignals,
		private PricingPredictionLogger $predictionLogger,
	) {
	}

	public function calculateFairPrice(Product $product, float $baseCost = null, array $options = []): array
	{
		try {
			$product->loadMissing('category');

			$signals = $this->marketSignals->forProduct($product);
			$features = $this->extractProductFeatures($product, $signals);

			$startedAt = microtime(true);
			$prediction = $this->callPricingModel($features);
			$runtimeMs = (int) ((microtime(true) - $startedAt) * 1000);

			$shouldLog = !($options['skip_logging'] ?? false);

			if (!$prediction || !isset($prediction['multiplier'])) {
				$fallback = $this->getFallbackPricing($product, $baseCost, $signals, $features, $runtimeMs);
				if ($shouldLog) {
					$this->logPredictionResult($product, $fallback, $options);
				}
				return $fallback;
			}

			$basePrice = $this->deriveBasePrice($product, $signals);
			$modelMultiplier = (float) $prediction['multiplier'];
			$fairMultiplier = $this->adjustMultiplierForFisherman($modelMultiplier, $signals);
			$suggestedPrice = max(1, $basePrice * $fairMultiplier);
			$confidence = $prediction['confidence'] ?? 0.85;

			$comparisonData = [
				'base_asking_price' => round($basePrice, 2),
				'fair_multiplier' => round($fairMultiplier, 3),
				'original_ml_multiplier' => round($modelMultiplier, 3),
				'suggested_price' => round($suggestedPrice, 2),
				'confidence' => round($confidence, 4),
				'signals' => $signals,
				'features' => $features,
				'price_range' => [
					'low' => round($suggestedPrice * 0.9, 2),
					'fair' => round($suggestedPrice, 2),
					'high' => round($suggestedPrice * 1.1, 2),
				],
				'fallback' => false,
				'runtime_ms' => $runtimeMs,
			];

			if ($baseCost !== null && $baseCost > 0) {
				$comparisonData['profit_at_suggested'] = round($suggestedPrice - $baseCost, 2);
				$comparisonData['profit_margin'] = round((($suggestedPrice - $baseCost) / $baseCost) * 100, 2);
			}

			$comparisonData['log_payload'] = [
				'features' => $features,
				'signals' => $signals,
				'multiplier' => $comparisonData['fair_multiplier'],
				'confidence' => $comparisonData['confidence'],
				'used_fallback' => false,
				'runtime_ms' => $runtimeMs,
				'extra' => [
					'base_price' => $basePrice,
					'model_multiplier' => $modelMultiplier,
				],
			];

			if ($shouldLog) {
				$this->logPredictionResult($product, $comparisonData, $options);
			}

			return $comparisonData;
		} catch (\Throwable $e) {
			Log::error('FishermanPricingService: Failed to calculate fair price', [
				'product_id' => $product->id ?? null,
				'error' => $e->getMessage(),
			]);

			$fallback = $this->getFallbackPricing($product, $baseCost);
			$fallback['log_payload']['extra']['exception'] = $e->getMessage();
			if (!($options['skip_logging'] ?? false)) {
				$this->logPredictionResult($product, $fallback, $options);
			}

			return $fallback;
		}
	}

	public function compareOfferToFairPrice(float $vendorOffer, array $fairPricing): array
	{
		$suggestedPrice = $fairPricing['suggested_price'] ?? null;

		if ($suggestedPrice === null || $suggestedPrice <= 0) {
			return [
				'vendor_offer' => round($vendorOffer, 2),
				'suggested_fair_price' => $suggestedPrice,
				'difference_amount' => null,
				'difference_percentage' => null,
				'offer_quality' => 'unknown',
				'warning_message' => 'Fair price unavailable, unable to evaluate offer accurately.',
				'recommendation' => 'Collect more data (signals or manual comps) before accepting.',
				'is_good_deal' => false,
			];
		}

		$difference = $vendorOffer - $suggestedPrice;
		$percentageDiff = round(($difference / $suggestedPrice) * 100, 2);
		$quality = $this->determineOfferQuality($percentageDiff);
		$warning = $this->getWarningForQuality($quality);

		return [
			'vendor_offer' => round($vendorOffer, 2),
			'suggested_fair_price' => round($suggestedPrice, 2),
			'difference_amount' => round($difference, 2),
			'difference_percentage' => $percentageDiff,
			'offer_quality' => $quality,
			'warning_message' => $warning,
			'recommendation' => $this->getRecommendation($quality, $percentageDiff),
			'is_good_deal' => $vendorOffer >= $suggestedPrice * 0.95,
			'comparison_snapshot' => [
				'price_range' => $fairPricing['price_range'] ?? null,
				'confidence' => $fairPricing['confidence'] ?? null,
			],
		];
	}

	private function extractProductFeatures(Product $product, array $signals = []): array
	{
		$now = Carbon::now();
		$createdAt = $product->created_at ? Carbon::parse($product->created_at) : $now;
		$hoursOld = $createdAt->diffInHours($now);
		$freshnessScore = max(0, 100 - ($hoursOld * 2));

		$demandSignal = (float) ($signals['demand']['score'] ?? 1.0);
		$supplyPressure = (float) ($signals['supply']['pressure'] ?? 1.0);
		$acceptanceRate = (float) ($signals['wholesale']['acceptance_rate'] ?? 0.5);

		$categoryFactor = 1.0;
		if ($product->category) {
			$categoryName = strtolower($product->category->name ?? '');
			if (str_contains($categoryName, 'tuna') || str_contains($categoryName, 'salmon')) {
				$categoryFactor = 1.4;
			} elseif (str_contains($categoryName, 'shellfish') || str_contains($categoryName, 'shrimp')) {
				$categoryFactor = 1.2;
			}
		}

		$seasonalityFactor = in_array($now->month, [3, 4, 5, 11, 12], true) ? 1.25 : 1.05;
		$baseQuantity = (float) ($product->available_quantity ?? $product->quantity ?? 12);
		$adjustedSupply = max(1, round($baseQuantity * (1 / max(0.5, $supplyPressure))));
		$dynamicDemand = round($demandSignal * $categoryFactor * (0.9 + $acceptanceRate), 3);

		return [
			'freshness_score' => $freshnessScore,
			'available_quantity' => (int) $adjustedSupply,
			'demand_factor' => $dynamicDemand,
			'seasonality_factor' => $seasonalityFactor,
			'time_of_day' => $now->hour,
			'vendor_rating' => 4.0,
			'category_id' => (int) ($product->category_id ?? 1),
		];
	}

	private function callPricingModel(array $features): ?array
	{
		$pythonPath = config('services.python_path', 'python3');
		$scriptPath = base_path('python/predict_price.py');

		if (!file_exists($scriptPath)) {
			Log::warning('FishermanPricingService: predict_price.py not found');
			return null;
		}

		$command = sprintf(
			'%s %s %s %s %s %s %s %s %s 2>&1',
			escapeshellcmd($pythonPath),
			escapeshellarg($scriptPath),
			escapeshellarg($features['freshness_score']),
			escapeshellarg($features['available_quantity']),
			escapeshellarg($features['demand_factor']),
			escapeshellarg($features['seasonality_factor']),
			escapeshellarg($features['time_of_day']),
			escapeshellarg($features['vendor_rating']),
			escapeshellarg($features['category_id'])
		);

		$output = shell_exec($command);

		if (!$output) {
			return null;
		}

		$result = json_decode(trim($output), true);

		if (json_last_error() !== JSON_ERROR_NONE || !isset($result['multiplier'])) {
			Log::warning('FishermanPricingService: Invalid ML response', [
				'output' => $output,
				'error' => json_last_error_msg(),
			]);
			return null;
		}

		return $result;
	}

	private function adjustMultiplierForFisherman(float $vendorMultiplier, array $signals): float
	{
		$multiplier = max(0.8, min(1.6, $vendorMultiplier));

		if ($multiplier > 1.4) {
			$multiplier = 1.3 + (($multiplier - 1.4) * 0.25);
		} elseif ($multiplier < 0.95) {
			$multiplier = 0.95 + (($multiplier - 0.95) * 0.5);
		} else {
			$multiplier *= 0.95;
		}

		$demand = (float) ($signals['demand']['score'] ?? 1.0);
		$supply = (float) ($signals['supply']['pressure'] ?? 1.0);
		$acceptance = (float) ($signals['wholesale']['acceptance_rate'] ?? 0.5);

		$inventoryRelief = max(0.7, min(1.3, 2 - $supply));
		$marketTension = max(0.8, min(1.4, $demand * (0.9 + $acceptance)));

		$adjusted = $multiplier * $inventoryRelief * $marketTension;

		return max(0.85, min(1.4, round($adjusted, 3)));
	}

	private function getFallbackPricing(
		Product $product,
		?float $baseCost,
		array $signals = [],
		?array $features = null,
		?int $runtimeMs = null
	): array {
		$signals = $signals ?: $this->marketSignals->forProduct($product);
		$features = $features ?: $this->extractProductFeatures($product, $signals);

		$basePrice = $this->deriveBasePrice($product, $signals);
		$demand = (float) ($signals['demand']['score'] ?? 1.0);
		$supply = (float) ($signals['supply']['pressure'] ?? 1.0);
		$acceptance = (float) ($signals['wholesale']['acceptance_rate'] ?? 0.5);

		$inventoryFactor = max(0.7, min(1.3, 2 - $supply));
		$demandFactor = max(0.8, min(1.4, $demand * (0.9 + $acceptance)));
		$suggestedPrice = max(1, $basePrice * $inventoryFactor * $demandFactor);

		$data = [
			'base_asking_price' => round($basePrice, 2),
			'fair_multiplier' => round(($suggestedPrice / max($basePrice, 0.01)), 3),
			'suggested_price' => round($suggestedPrice, 2),
			'confidence' => 0.55,
			'fallback' => true,
			'signals' => $signals,
			'features' => $features,
			'runtime_ms' => $runtimeMs,
			'price_range' => [
				'low' => round($suggestedPrice * 0.9, 2),
				'fair' => round($suggestedPrice, 2),
				'high' => round($suggestedPrice * 1.1, 2),
			],
		];

		if ($baseCost !== null && $baseCost > 0) {
			$data['profit_at_suggested'] = round($suggestedPrice - $baseCost, 2);
			$data['profit_margin'] = round((($suggestedPrice - $baseCost) / $baseCost) * 100, 2);
		}

		$data['log_payload'] = [
			'features' => $features,
			'signals' => $signals,
			'multiplier' => $data['fair_multiplier'],
			'confidence' => $data['confidence'],
			'used_fallback' => true,
			'runtime_ms' => $runtimeMs,
			'extra' => [
				'base_price' => $basePrice,
			],
		];

		return $data;
	}

	private function deriveBasePrice(Product $product, array $signals): float
	{
		$candidates = array_filter([
			$product->unit_price ?? null,
			$signals['retail']['median'] ?? null,
			$signals['retail']['average'] ?? null,
			$signals['wholesale']['median_price'] ?? null,
		], fn ($value) => $value !== null && $value > 0);

		if (empty($candidates)) {
			return round((float) ($product->unit_price ?? 100.0), 2);
		}

		$avg = array_sum($candidates) / count($candidates);

		return round(max(20.0, $avg), 2);
	}

	private function getRecommendation(string $quality, float $percentageDiff): string
	{
		return match ($quality) {
			'low' => '⚠️ This offer is significantly below market value. Consider negotiating hard or rejecting it.',
			'below_fair' => '💭 Slightly below fair price. A counter-offer could secure a better deal.',
			'fair' => '✓ Offer is in line with market pricing. Safe to consider accepting.',
			'above_fair' => '✓ Offer is better than market. Great leverage for quicker acceptance.',
			'excellent' => '🎉 Offer is well above market value. Lock it in, but verify buyer reliability.',
			default => 'Review the offer carefully before deciding.',
		};
	}

	private function determineOfferQuality(float $percentageDiff): string
	{
		return match (true) {
			$percentageDiff <= -20 => 'low',
			$percentageDiff <= -5 => 'below_fair',
			$percentageDiff < 5 => 'fair',
			$percentageDiff < 15 => 'above_fair',
			default => 'excellent',
		};
	}

	private function getWarningForQuality(string $quality): ?string
	{
		return match ($quality) {
			'low' => 'Offer is far below market value; push back or find alternative buyers.',
			'below_fair' => 'Offer undercuts fair value. Negotiate before committing.',
			'excellent' => 'Offer is unusually rich. Double-check terms and payment security.',
			default => null,
		};
	}

	public function logPredictionSnapshot(Product $product, array $comparisonData, array $options = []): void
	{
		$this->logPredictionResult($product, $comparisonData, $options);
	}

	private function logPredictionResult(Product $product, array $comparisonData, array $options = []): void
	{
		$payload = $comparisonData['log_payload'] ?? null;

		if (!$payload) {
			return;
		}

		try {
			$this->predictionLogger->log([
				'context' => $options['log_context'] ?? 'fisherman_pricing',
				'product_id' => $product->id,
				'offer_id' => $options['offer_id'] ?? null,
				'multiplier' => $payload['multiplier'] ?? null,
				'confidence' => $payload['confidence'] ?? null,
				'used_fallback' => $payload['used_fallback'] ?? false,
				'runtime_ms' => $payload['runtime_ms'] ?? ($comparisonData['runtime_ms'] ?? null),
				'features' => $payload['features'] ?? null,
				'signals' => $payload['signals'] ?? null,
				'extra' => $payload['extra'] ?? null,
			]);
		} catch (\Throwable $e) {
			Log::warning('FishermanPricingService: Failed to log prediction', [
				'product_id' => $product->id,
				'error' => $e->getMessage(),
			]);
		}
	}
}

