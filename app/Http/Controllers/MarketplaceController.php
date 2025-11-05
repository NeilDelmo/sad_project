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

    public function shop()
    {
        // Get category IDs
        $fishCategories = ProductCategory::whereIn('name', ['Fresh Fish', 'Seafood'])->pluck('id');
        $gearCategories = ProductCategory::whereIn('name', ['Fishing Gear', 'Equipment'])->pluck('id');

        // Get fish products
        $fishProducts = Product::with(['supplier', 'category'])
            ->whereIn('category_id', $fishCategories)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get gear products
        $gearProducts = Product::with(['supplier', 'category'])
            ->whereIn('category_id', $gearCategories)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('marketplaces.marketplacemain', compact('fishProducts', 'gearProducts'));
    }
}
