<?php

namespace App\Services;

use App\Models\MarketplaceListing;
use App\Models\CustomerOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RecommendationService
{
    /**
     * Build buyer-facing recommendations.
     *
     * @param string|null $q Optional search query to bias results
     * @param array $categoryAliases Category names considered as fish
     * @param int $limit Per-list limits
     * @return array
     */
    public function buildBuyerRecommendations(?string $q = null, array $categoryAliases = ['Fish','Fresh Fish'], int $limit = 8, ?int $buyerId = null): array
    {
        $filters = function($query) use ($q, $categoryAliases) {
            $query->active()
                ->whereHas('product.category', function($q2) use ($categoryAliases) {
                    $q2->whereIn('name', $categoryAliases);
                });
            if ($q !== null && trim($q) !== '') {
                $needle = trim($q);
                $query->whereHas('product', function($p) use ($needle) {
                    $p->where('name', 'like', "%{$needle}%")
                      ->orWhere('description', 'like', "%{$needle}%");
                });
            }
        };

        [$preferredCategoryIds, $preferredSellerIds] = $this->deriveBuyerPreferences($buyerId);

        $boostSeller = empty($preferredSellerIds) ? '' : 'CASE WHEN marketplace_listings.seller_id IN (' . implode(',', $preferredSellerIds) . ") THEN 0 ELSE 1 END, ";
        $boostCategory = empty($preferredCategoryIds) ? '' : 'CASE WHEN products.category_id IN (' . implode(',', $preferredCategoryIds) . ") THEN 0 ELSE 1 END, ";

        $cheapest = MarketplaceListing::with(['product','product.category','seller'])
            ->where($filters)
            ->join('products', 'marketplace_listings.product_id', '=', 'products.id')
            ->orderByRaw($boostSeller . $boostCategory . 'COALESCE(marketplace_listings.final_price, marketplace_listings.dynamic_price, marketplace_listings.asking_price, marketplace_listings.base_price) ASC')
            ->limit($limit)
            ->get(['marketplace_listings.*']);

        $freshest = MarketplaceListing::with(['product','product.category','seller'])
            ->where($filters)
            ->join('products', 'marketplace_listings.product_id', '=', 'products.id')
            ->orderByRaw($boostSeller . $boostCategory . 'marketplace_listings.freshness_score DESC, marketplace_listings.listing_date DESC')
            ->limit($limit)
            ->get(['marketplace_listings.*']);

        $trending = MarketplaceListing::with(['product','product.category','seller'])
            ->where($filters)
            ->join('products', 'marketplace_listings.product_id', '=', 'products.id')
            ->orderByRaw($boostSeller . $boostCategory . 'marketplace_listings.demand_factor DESC, marketplace_listings.listing_date DESC')
            ->limit($limit)
            ->get(['marketplace_listings.*']);

        // Seasonal: leverage product seasonality_factor
        $seasonal = MarketplaceListing::with(['product','product.category','seller'])
            ->where($filters)
            ->join('products', 'marketplace_listings.product_id', '=', 'products.id')
            ->orderByRaw($boostSeller . $boostCategory . 'products.seasonality_factor DESC, marketplace_listings.listing_date DESC')
            ->limit($limit)
            ->get(['marketplace_listings.*']);

        $sellers = $this->extractRecommendedSellers(collect([$cheapest, $freshest, $trending, $seasonal]));

        $racks = $this->buildCategoryRacks($categoryAliases, $limit, $preferredCategoryIds);

        return [
            'cheapest' => $cheapest,
            'freshest' => $freshest,
            'trending' => $trending,
            'seasonal' => $seasonal,
            'sellers' => $sellers,
            'racks' => $racks,
        ];
    }

    /**
     * Derive recommended sellers from listings sets, prioritizing those
     * who appear frequently across sets.
     */
    private function extractRecommendedSellers(Collection $sets, int $limit = 6): Collection
    {
        $counts = [];
        $sellerMap = [];
        foreach ($sets as $listings) {
            foreach ($listings as $l) {
                if (!$l->seller) continue;
                $sid = $l->seller->id;
                $counts[$sid] = ($counts[$sid] ?? 0) + 1;
                $sellerMap[$sid] = $l->seller;
            }
        }
        arsort($counts);
        $topIds = array_slice(array_keys($counts), 0, $limit);
        return collect($topIds)->map(function($sid) use ($sellerMap) { return $sellerMap[$sid]; });
    }

    /**
     * Derive buyer preferences from past orders.
     * Returns arrays: [preferredCategoryIds, preferredSellerIds]
     */
    private function deriveBuyerPreferences(?int $buyerId): array
    {
        if (!$buyerId) {
            return [[], []];
        }
        $orders = CustomerOrder::query()
            ->where('buyer_id', $buyerId)
            ->whereIn('customer_orders.status', [CustomerOrder::STATUS_DELIVERED, CustomerOrder::STATUS_RECEIVED])
            ->join('marketplace_listings','customer_orders.listing_id','=','marketplace_listings.id')
            ->join('products','marketplace_listings.product_id','=','products.id')
            ->selectRaw('marketplace_listings.seller_id as seller_id, products.category_id as category_id, COUNT(*) as cnt')
            ->groupBy('seller_id','category_id')
            ->get();

        $sellerCounts = [];
        $catCounts = [];
        foreach ($orders as $row) {
            $sellerCounts[(int)$row->seller_id] = ($sellerCounts[(int)$row->seller_id] ?? 0) + (int)$row->cnt;
            $catCounts[(int)$row->category_id] = ($catCounts[(int)$row->category_id] ?? 0) + (int)$row->cnt;
        }
        arsort($sellerCounts);
        arsort($catCounts);
        $preferredSellerIds = array_slice(array_keys($sellerCounts), 0, 5);
        $preferredCategoryIds = array_slice(array_keys($catCounts), 0, 5);
        return [$preferredCategoryIds, $preferredSellerIds];
    }

    /**
     * Build category-specific racks: cheapest and freshest per top categories.
     */
    private function buildCategoryRacks(array $categoryAliases, int $limit, array $preferredCategoryIds = []): array
    {
        // Determine top categories present in active listings
        $topCats = MarketplaceListing::query()
            ->active()
            ->join('products','marketplace_listings.product_id','=','products.id')
            ->join('product_categories','products.category_id','=','product_categories.id')
            ->whereIn('product_categories.name', $categoryAliases)
            ->selectRaw('products.category_id as category_id, product_categories.name as category_name, COUNT(*) as cnt')
            ->groupBy('products.category_id','product_categories.name')
            ->orderByDesc('cnt')
            ->limit(5)
            ->get();

        // Promote preferred categories first
        $topCats = $topCats->sortBy(function($row) use ($preferredCategoryIds) {
            return in_array((int)$row->category_id, $preferredCategoryIds, true) ? 0 : 1;
        });

        $cheapestByCat = [];
        $freshestByCat = [];
        foreach ($topCats as $row) {
            $catId = (int)$row->category_id;
            $catName = (string)$row->category_name;
            $cheapestByCat[$catName] = MarketplaceListing::with(['product','product.category','seller'])
                ->active()
                ->join('products','marketplace_listings.product_id','=','products.id')
                ->where('products.category_id', $catId)
                ->orderByRaw('COALESCE(marketplace_listings.final_price, marketplace_listings.dynamic_price, marketplace_listings.asking_price, marketplace_listings.base_price) ASC')
                ->limit($limit)
                ->get(['marketplace_listings.*']);

            $freshestByCat[$catName] = MarketplaceListing::with(['product','product.category','seller'])
                ->active()
                ->join('products','marketplace_listings.product_id','=','products.id')
                ->where('products.category_id', $catId)
                ->orderByRaw('marketplace_listings.freshness_score DESC, marketplace_listings.listing_date DESC')
                ->limit($limit)
                ->get(['marketplace_listings.*']);
        }

        return [
            'cheapest_by_category' => $cheapestByCat,
            'freshest_by_category' => $freshestByCat,
        ];
    }
}
