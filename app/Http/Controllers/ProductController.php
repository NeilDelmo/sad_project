<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use App\Models\Order;
use App\Models\VendorInventory;
use App\Notifications\NewCatchAvailable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    private const ACTIVE_OFFER_STATUSES = ['pending', 'countered'];
    private const ONGOING_ORDER_STATUSES = [
        Order::STATUS_PENDING_PAYMENT,
        Order::STATUS_IN_TRANSIT,
        Order::STATUS_DELIVERED,
        Order::STATUS_REFUND_REQUESTED,
    ];

    /**
     * Display a listing of the fisherman's products
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');
        $category = $request->get('category');

        $query = Product::where('supplier_id', Auth::id())
            ->where(function($q) {
                $q->notSpoiled()
                  ->orWhere('available_quantity', 0);
            })
            ->with('category')
            ->withCount([
                'vendorOffers as active_offer_count' => function ($query) {
                    $query->whereIn('status', self::ACTIVE_OFFER_STATUSES);
                },
                'orders as ongoing_order_count' => function ($query) {
                    $query->whereIn('status', self::ONGOING_ORDER_STATUSES);
                },
            ]);

        if ($status === 'active') {
            $query->where('available_quantity', '>', 0);
        } elseif ($status === 'sold_out') {
            $query->where('available_quantity', 0);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category_id', $category);
        }

        $products = $query
            ->orderByRaw('available_quantity = 0 DESC')
            ->orderBy('created_at', 'desc')
            ->get();

        $products->each(function ($product) {
            $product->lock_reasons = $this->getProductLockReasons($product);
            $product->is_edit_locked = !empty($product->lock_reasons);
        });

        $categories = ProductCategory::whereIn('name', ['Fish', 'Shellfish'])->get();

        return view('fisherman.products.index', compact('products', 'status', 'categories'));
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

        $validated = $this->enforceFishTypeForCategory($validated);
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

        if ($response = $this->guardProductEditing($product)) {
            return $response;
        }

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

        if ($response = $this->guardProductEditing($product)) {
            return $response;
        }

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

        $validated = $this->enforceFishTypeForCategory($validated);

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

    private function enforceFishTypeForCategory(array $validated): array
    {
        $category = ProductCategory::find($validated['category_id'] ?? null);
        $categoryName = $category?->name ?? '';
        $isShellfishCategory = strcasecmp($categoryName, 'Shellfish') === 0;

        if ($isShellfishCategory) {
            $validated['fish_type'] = 'Shellfish';
            return $validated;
        }

        if (($validated['fish_type'] ?? null) === 'Shellfish') {
            throw ValidationException::withMessages([
                'fish_type' => 'Shellfish type is only allowed when the category is set to Shellfish.',
            ]);
        }

        return $validated;
    }

    private function guardProductEditing(Product $product)
    {
        return $this->guardProductAction($product, 'edit');
    }

    private function guardProductDeletion(Product $product)
    {
        return $this->guardProductAction($product, 'delete');
    }

    private function guardProductAction(Product $product, string $action)
    {
        $lockReasons = $this->getProductLockReasons($product);

        if (!empty($lockReasons)) {
            $message = 'Cannot ' . $action . ' this product because ' . implode(' and ', $lockReasons) . '. Please wait until all offers and transactions are finished.';
            return redirect()->route('fisherman.products.index')->with('error', $message);
        }

        return null;
    }

    private function getProductLockReasons(Product $product): array
    {
        $lockReasons = [];

        if ($product->available_quantity <= 0) {
            $lockReasons[] = 'its stock is already depleted';
        }

        // Check for ANY offers (active or not) to prevent FK errors
        if ($product->vendorOffers()->exists()) {
            $lockReasons[] = 'there are vendor offers associated with this product';
        }

        // Check for ANY orders (ongoing or completed) to prevent FK errors
        if ($product->orders()->exists()) {
            $lockReasons[] = 'there are transactions tied to this product';
        }

        // Check for ANY inventory records (purchased by vendors)
        if (VendorInventory::where('product_id', $product->id)->exists()) {
            $lockReasons[] = 'it has been purchased by vendors';
        }

        return $lockReasons;
    }

    /**
     * Remove the specified product from database
     */
    public function destroy($id)
    {
        $product = Product::where('supplier_id', Auth::id())
            ->findOrFail($id);

        if ($response = $this->guardProductDeletion($product)) {
            return $response;
        }

        // Delete image if exists
        if ($product->image_path && file_exists(public_path($product->image_path))) {
            unlink(public_path($product->image_path));
        }

        $product->delete();

        return redirect()->route('fisherman.products.index')
            ->with('success', 'Product deleted successfully!');
    }
}
