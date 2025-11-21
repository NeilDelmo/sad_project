<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use App\Notifications\NewCatchAvailable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
        // Only allow edible seafood categories
        $categories = ProductCategory::whereIn('name', ['Fish', 'Shellfish'])->get();
        return view('fisherman.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in database
     */
    public function store(Request $request)
    {
        $allowedCategoryIds = ProductCategory::whereIn('name', ['Fish', 'Shellfish'])->pluck('id')->all();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => ['required', 'integer', Rule::in($allowedCategoryIds)],
            'fish_type' => 'nullable|string|in:Shellfish,Oily Fish,White Fish',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'available_quantity' => 'required|numeric|min:0',
            'freshness_metric' => 'required|in:Very Fresh,Fresh,Good',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        $validated['supplier_id'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $imageName);
            $validated['image_path'] = 'images/products/' . $imageName;
        }

        $product = Product::create($validated);

        // Notify vendors based on their preferences
        try {
            $vendors = User::where('user_type', 'vendor')
                ->with('vendorPreference')
                ->get();

            foreach ($vendors as $vendor) {
                $prefs = $vendor->vendorPreference;
                if (!$prefs) { continue; }

                $matches = $prefs->notify_on === 'all';
                if (!$matches) {
                    $matches = true;
                    if (!empty($prefs->preferred_categories) && !in_array($product->category_id, $prefs->preferred_categories)) {
                        $matches = false;
                    }
                    if ($matches && !is_null($prefs->min_quantity) && $product->available_quantity < $prefs->min_quantity) {
                        $matches = false;
                    }
                    if ($matches && !is_null($prefs->max_unit_price) && $product->unit_price > $prefs->max_unit_price) {
                        $matches = false;
                    }
                }

                if ($matches) {
                    $vendor->notify(new NewCatchAvailable($product));
                }
            }
        } catch (\Throwable $e) {
            // Swallow notification errors to not block product creation
        }

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

        // Only allow edible seafood categories
        $categories = ProductCategory::whereIn('name', ['Fish', 'Shellfish'])->get();

        return view('fisherman.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in database
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('supplier_id', Auth::id())
            ->findOrFail($id);

        $allowedCategoryIds = ProductCategory::whereIn('name', ['Fish', 'Shellfish'])->pluck('id')->all();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => ['required', 'integer', Rule::in($allowedCategoryIds)],
            'fish_type' => 'nullable|string|in:Shellfish,Oily Fish,White Fish',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'available_quantity' => 'required|numeric|min:0',
            'freshness_metric' => 'required|in:Very Fresh,Fresh,Good',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path && file_exists(public_path($product->image_path))) {
                unlink(public_path($product->image_path));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $imageName);
            $validated['image_path'] = 'images/products/' . $imageName;
        }

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

        // Delete image if exists
        if ($product->image_path && file_exists(public_path($product->image_path))) {
            unlink(public_path($product->image_path));
        }

        $product->delete();

        return redirect()->route('fisherman.products.index')
            ->with('success', 'Product deleted successfully!');
    }
}
