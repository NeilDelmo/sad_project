<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceListing;
use App\Models\CustomerOrder;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index()
    {
        return view('marketplaces.marketplace');
    }

    public function shop(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

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

        $fishProducts = $query->orderBy('listing_date', 'desc')->get();

        // Precompute stock per listing based on vendor inventory minus non-refunded customer orders
        $stocks = [];
        if ($fishProducts->isNotEmpty()) {
            $listingIds = $fishProducts->pluck('id');
            $soldByListing = CustomerOrder::whereIn('listing_id', $listingIds)
                ->where('status', '!=', CustomerOrder::STATUS_REFUNDED)
                ->selectRaw('listing_id, SUM(quantity) as qty')
                ->groupBy('listing_id')
                ->pluck('qty', 'listing_id');

            foreach ($fishProducts as $listing) {
                $baseQty = optional($listing->vendorInventory)->quantity;
                if ($baseQty === null) { $stocks[$listing->id] = null; continue; }
                $sold = (int) ($soldByListing[$listing->id] ?? 0);
                $stocks[$listing->id] = max(0, (int)$baseQty - $sold);
            }
        }

        return view('marketplaces.marketplacemain', [
            'fishProducts' => $fishProducts,
            'stocks' => $stocks,
            'q' => $q,
        ]);
    }
}
