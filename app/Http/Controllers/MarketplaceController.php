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
        $filter = $request->query('filter', 'all'); // Get filter parameter, default to 'all'

        // Get category IDs - only Fresh Fish (no seafood, just live fish)
        $fishCategories = ProductCategory::where('name', 'Fresh Fish')->pluck('id');
        $gearCategories = ProductCategory::whereIn('name', ['Fishing Gear', 'Equipment'])->pluck('id');

        // Get products based on filter
        if ($filter === 'fish') {
            // Show only fish products
            $fishProducts = Product::with(['supplier', 'category'])
                ->whereIn('category_id', $fishCategories)
                ->orderBy('created_at', 'desc')
                ->get();
            $gearProducts = collect(); // Empty collection
        } elseif ($filter === 'gear') {
            // Show only gear products
            $fishProducts = collect(); // Empty collection
            $gearProducts = Product::with(['supplier', 'category'])
                ->whereIn('category_id', $gearCategories)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Show all products (Latest/default)
            $fishProducts = Product::with(['supplier', 'category'])
                ->whereIn('category_id', $fishCategories)
                ->orderBy('created_at', 'desc')
                ->get();

            $gearProducts = Product::with(['supplier', 'category'])
                ->whereIn('category_id', $gearCategories)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('marketplaces.marketplacemain', compact('fishProducts', 'gearProducts', 'filter'));
    }
}
