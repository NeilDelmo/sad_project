<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceListing;
use Illuminate\Support\Facades\Auth;
use App\Services\RecommendationService;
use App\Models\CustomerOrder;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    protected RecommendationService $recommendations;

    public function __construct(RecommendationService $recommendations)
    {
        $this->recommendations = $recommendations;
    }

    public function index()
    {
        return view('marketplaces.marketplace');
    }

    public function shop(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $sellerFilter = $request->integer('seller');
        $categoryFilter = $request->get('category');

        $aliases = config('fish.category_aliases', ['Fish', 'Fresh Fish']);
        $query = MarketplaceListing::with(['product', 'product.category', 'seller', 'vendorInventory'])
            ->active()
            ->whereHas('product.category', function($q2) use ($aliases) {
                $q2->whereIn('name', $aliases);
            });

        if ($q !== '') {
            $query->whereHas('product', function($p) use ($q) {
                $p->where('name', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($sellerFilter) {
            $query->where('seller_id', $sellerFilter);
        }

        if ($categoryFilter && is_string($categoryFilter)) {
            $query->whereHas('product.category', function($p) use ($categoryFilter) {
                $p->where('name', $categoryFilter);
            });
        }

        $fishProducts = $query->orderBy('listing_date', 'desc')->get();

        // Filter out spoiled items (expired)
        $fishProducts = $fishProducts->filter(function ($listing) {
            return $listing->freshness_level !== 'Spoiled';
        });

        // Precompute stock per listing based on vendor inventory
        $stocks = [];
        if ($fishProducts->isNotEmpty()) {
            foreach ($fishProducts as $listing) {
                $baseQty = optional($listing->vendorInventory)->quantity;
                $stocks[$listing->id] = $baseQty;
            }
        }

        $buyerId = (Auth::check() && optional(Auth::user())->user_type === 'buyer') ? Auth::id() : null;
        $recommendations = $this->recommendations->buildBuyerRecommendations($q, $aliases, 8, $buyerId);

        return view('marketplaces.marketplacemain', [
            'fishProducts' => $fishProducts,
            'stocks' => $stocks,
            'q' => $q,
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * JSON API endpoint for marketplace recommendations
     */
    public function recommendations(Request $request)
    {
        $q = $request->get('q');
        $limit = (int) ($request->get('limit', 8));
        $aliases = config('fish.category_aliases', ['Fish', 'Fresh Fish']);
        $buyerId = (Auth::check() && optional(Auth::user())->user_type === 'buyer') ? Auth::id() : null;
        $data = $this->recommendations->buildBuyerRecommendations($q, $aliases, $limit, $buyerId);
        return response()->json($data);
    }
}
