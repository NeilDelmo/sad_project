<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the fisherman's products
     */
    public function index()
    {
        $products = Product::where('supplier_id', Auth::id())
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('fisherman.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        // Only fish categories for fishermen
        $categories = ProductCategory::whereIn('name', ['Fresh Fish', 'Seafood'])->get();
        
        return view('fisherman.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:product_categories,id',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'available_quantity' => 'required|numeric|min:0',
            'freshness_metric' => 'required|in:Very Fresh,Fresh,Good',
            'quality_rating' => 'nullable|numeric|min:0|max:5',
        ]);

        $validated['supplier_id'] = Auth::id();

        Product::create($validated);

        return redirect()->route('fisherman.products.index')
            ->with('success', 'Product added successfully!');
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit($id)
    {
        $product = Product::where('supplier_id', Auth::id())
            ->findOrFail($id);

        $categories = ProductCategory::whereIn('name', ['Fresh Fish', 'Seafood'])->get();

        return view('fisherman.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in database
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('supplier_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:product_categories,id',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'available_quantity' => 'required|numeric|min:0',
            'freshness_metric' => 'required|in:Very Fresh,Fresh,Good',
            'quality_rating' => 'nullable|numeric|min:0|max:5',
        ]);

        $product->update($validated);

        return redirect()->route('fisherman.products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from database
     */
    public function destroy($id)
    {
        $product = Product::where('supplier_id', Auth::id())
            ->findOrFail($id);

        $product->delete();

        return redirect()->route('fisherman.products.index')
            ->with('success', 'Product deleted successfully!');
    }
}
