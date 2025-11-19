<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceListing;
use App\Models\VendorOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MLAnalyticsController extends Controller
{
    public function index()
    {
        // Pricing Model Stats
        $pricingStats = [
            'total_ai_listings' => MarketplaceListing::whereNotNull('ml_confidence')->count(),
            'avg_confidence' => round(MarketplaceListing::whereNotNull('ml_confidence')->avg('ml_confidence') * 100, 1),
            'avg_multiplier' => round(MarketplaceListing::whereNotNull('ml_multiplier')->avg('ml_multiplier'), 2),
            'price_range' => [
                'min' => MarketplaceListing::whereNotNull('ml_multiplier')->min('ml_multiplier'),
                'max' => MarketplaceListing::whereNotNull('ml_multiplier')->max('ml_multiplier'),
            ],
        ];

        // Fisherman Protection Stats
        $offerStats = [
            'total_offers_analyzed' => VendorOffer::whereNotNull('suggested_price_fisherman')->count(),
            'avg_confidence' => round(VendorOffer::whereNotNull('ml_confidence_fisherman')->avg('ml_confidence_fisherman') * 100, 1),
            'offers_below_fair' => VendorOffer::whereNotNull('suggested_price_fisherman')
                ->whereRaw('offered_price < suggested_price_fisherman * 0.95')
                ->count(),
            'offers_above_fair' => VendorOffer::whereNotNull('suggested_price_fisherman')
                ->whereRaw('offered_price > suggested_price_fisherman * 1.05')
                ->count(),
        ];

        // Recent AI-Priced Listings
        $recentListings = MarketplaceListing::with(['product', 'seller'])
            ->whereNotNull('ml_confidence')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // ML Model Performance by Category
        $categoryPerformance = DB::table('marketplace_listings')
            ->join('products', 'marketplace_listings.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->select(
                'product_categories.name as category',
                DB::raw('COUNT(*) as listings_count'),
                DB::raw('AVG(marketplace_listings.ml_confidence) as avg_confidence'),
                DB::raw('AVG(marketplace_listings.ml_multiplier) as avg_multiplier')
            )
            ->whereNotNull('marketplace_listings.ml_confidence')
            ->groupBy('product_categories.name')
            ->get();

        return view('ml-analytics', compact('pricingStats', 'offerStats', 'recentListings', 'categoryPerformance'));
    }
}
