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
        // Get all products with their relationships, ordered by newest first
        $products = Product::with(['supplier', 'category'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Group products by category
        $fishProducts = $products->filter(function ($product) {
            return $product->category && 
                   (stripos($product->category->name, 'fish') !== false || 
                    stripos($product->category->name, 'seafood') !== false);
        });

        $gearProducts = $products->filter(function ($product) {
            return $product->category && 
                   (stripos($product->category->name, 'gear') !== false || 
                    stripos($product->category->name, 'equipment') !== false ||
                    stripos($product->category->name, 'tool') !== false);
        });

        return view('marketplaces.marketplacemain', compact('products', 'fishProducts', 'gearProducts'));
    }
}
