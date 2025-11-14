<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index()
    {
        return view('marketplaces.marketplace');
    }

    public function shop(Request $request)
    {
        // Only show fish products
        $fishProducts = Product::with(['supplier', 'category', 'activeMarketplaceListing'])
            ->whereHas('category', function($q) {
                $q->where('name', 'Fish');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('marketplaces.marketplacemain', compact('fishProducts'));
    }
}
