<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\MarketplaceListing;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index()
    {
        return view('marketplaces.marketplace');
    }

    public function shop(Request $request)
    {
        // Show only active marketplace listings (vendor-created with ML pricing)
        $aliases = config('fish.category_aliases', ['Fish', 'Fresh Fish']);
        $fishProducts = MarketplaceListing::with(['product', 'product.category', 'seller', 'vendorInventory'])
            ->active()
            ->whereHas('product.category', function($q) use ($aliases) {
                $q->whereIn('name', $aliases);
            })
            ->orderBy('listing_date', 'desc')
            ->get();

        return view('marketplaces.marketplacemain', compact('fishProducts'));
    }
}
