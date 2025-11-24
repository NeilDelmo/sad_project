<?php

namespace App\Services;

use App\Models\CustomerOrder;
use App\Models\MarketplaceListing;
use App\Models\Product;
use App\Models\VendorInventory;
use App\Models\VendorOffer;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Collection;

class MarketSignalService
{
    private const RETAIL_WINDOW_DAYS = 7;
    private const WHOLESALE_WINDOW_DAYS = 7;
    private const DEMAND_WINDOW_HOURS = 24;
    private const CACHE_TTL_SECONDS = 900; // 15 minutes

    public function __construct(private CacheRepository $cache)
    {
    }

    /**
     * Aggregate recent market signals for a product.
     */
    public function forProduct(Product $product): array
    {
        $cacheKey = sprintf(
            'market_signals:%d:%s',
            $product->id,
            optional($product->updated_at)?->timestamp ?? 'na'
        );

        return $this->cache->remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($product) {
            return [
                'retail' => $this->computeRetailStats($product),
                'wholesale' => $this->computeWholesaleStats($product),
                'demand' => $this->computeDemandScore($product),
                'supply' => $this->computeSupplyPressure($product),
            ];
        });
    }

    private function computeRetailStats(Product $product): array
    {
        $windowStart = Carbon::now()->subDays(self::RETAIL_WINDOW_DAYS);

        $primaryPrices = CustomerOrder::query()
            ->select('customer_orders.unit_price')
            ->join('marketplace_listings', 'customer_orders.listing_id', '=', 'marketplace_listings.id')
            ->where('marketplace_listings.product_id', $product->id)
            ->whereIn('customer_orders.status', [
                CustomerOrder::STATUS_RECEIVED,
                CustomerOrder::STATUS_DELIVERED,
            ])
            ->where('customer_orders.created_at', '>=', $windowStart)
            ->pluck('customer_orders.unit_price');

        $prices = $primaryPrices;
        if ($primaryPrices->count() < 3) {
            $prices = CustomerOrder::query()
                ->select('customer_orders.unit_price')
                ->join('marketplace_listings', 'customer_orders.listing_id', '=', 'marketplace_listings.id')
                ->join('products', 'marketplace_listings.product_id', '=', 'products.id')
                ->where('products.category_id', $product->category_id)
                ->where('customer_orders.created_at', '>=', $windowStart)
                ->whereIn('customer_orders.status', [
                    CustomerOrder::STATUS_RECEIVED,
                    CustomerOrder::STATUS_DELIVERED,
                ])
                ->pluck('customer_orders.unit_price');
        }

        return [
            'median' => $this->calculateMedian($prices) ?? (float) ($product->unit_price ?? 0),
            'average' => $prices->avg() ?? (float) ($product->unit_price ?? 0),
            'min' => $prices->min(),
            'max' => $prices->max(),
            'sample_size' => $prices->count(),
        ];
    }

    private function computeWholesaleStats(Product $product): array
    {
        $windowStart = Carbon::now()->subDays(self::WHOLESALE_WINDOW_DAYS);

        $offers = VendorOffer::query()
            ->where('product_id', $product->id)
            ->where('created_at', '>=', $windowStart)
            ->get(['status', 'offered_price', 'fisherman_counter_price']);

        $accepted = $offers->where('status', 'accepted');
        $clearingPrices = $accepted->map(fn ($offer) => (float) ($offer->fisherman_counter_price ?? $offer->offered_price));

        $total = $offers->count();
        $acceptanceRate = $total > 0 ? round($accepted->count() / $total, 3) : 0.0;

        return [
            'acceptance_rate' => $acceptanceRate,
            'median_price' => $this->calculateMedian($clearingPrices) ?? (float) ($product->unit_price ?? 0),
            'sample_size' => $clearingPrices->count(),
        ];
    }

    private function computeDemandScore(Product $product): array
    {
        $windowStart = Carbon::now()->subHours(self::DEMAND_WINDOW_HOURS);

        $recentRetailOrders = CustomerOrder::query()
            ->where('created_at', '>=', $windowStart)
            ->whereHas('listing', fn ($query) => $query->where('product_id', $product->id))
            ->count();

        $recentWholesaleOffers = VendorOffer::query()
            ->where('product_id', $product->id)
            ->where('created_at', '>=', $windowStart)
            ->count();

        $activeListings = MarketplaceListing::query()
            ->where('product_id', $product->id)
            ->active()
            ->count();

        $score = 1.0
            + ($recentRetailOrders * 0.05)
            + ($recentWholesaleOffers * 0.02)
            - (max($activeListings - 5, 0) * 0.01);

        return [
            'score' => round(max(0.5, min(2.0, $score)), 2),
            'recent_retail_orders' => $recentRetailOrders,
            'recent_wholesale_offers' => $recentWholesaleOffers,
            'active_listings' => $activeListings,
        ];
    }

    private function computeSupplyPressure(Product $product): array
    {
        $onHand = (float) ($product->available_quantity ?? 0);
        $vendorStock = (float) VendorInventory::query()
            ->where('product_id', $product->id)
            ->whereIn('status', ['in_stock', 'listed'])
            ->sum('quantity');

        $totalSupply = $onHand + $vendorStock;
        $dailyRetailVolume = max(1, $this->estimateDailyRetailVolume($product));
        $daysOfCover = $totalSupply > 0 ? $totalSupply / $dailyRetailVolume : 0;

        $pressure = 1.0;
        if ($daysOfCover < 2) {
            $pressure = 1.4;
        } elseif ($daysOfCover > 7) {
            $pressure = 0.85;
        }

        return [
            'pressure' => round($pressure, 2),
            'total_supply' => $totalSupply,
            'on_hand' => $onHand,
            'vendor_stock' => $vendorStock,
            'estimated_daily_volume' => $dailyRetailVolume,
        ];
    }

    private function calculateMedian(Collection $values): ?float
    {
        if ($values->isEmpty()) {
            return null;
        }

        $sorted = $values->sort()->values();
        $count = $sorted->count();
        $middle = (int) floor($count / 2);

        if ($count % 2) {
            return (float) $sorted[$middle];
        }

        return (float) (($sorted[$middle - 1] + $sorted[$middle]) / 2);
    }

    private function estimateDailyRetailVolume(Product $product): float
    {
        $windowStart = Carbon::now()->subDays(self::RETAIL_WINDOW_DAYS);
        $totalQuantity = CustomerOrder::query()
            ->join('marketplace_listings', 'customer_orders.listing_id', '=', 'marketplace_listings.id')
            ->where('marketplace_listings.product_id', $product->id)
            ->where('customer_orders.created_at', '>=', $windowStart)
            ->sum('customer_orders.quantity');

        return max(1, round($totalQuantity / max(self::RETAIL_WINDOW_DAYS, 1), 2));
    }
}
