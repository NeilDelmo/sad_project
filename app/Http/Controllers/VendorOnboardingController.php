<?php

namespace App\Http\Controllers;

use App\Models\VendorPreference;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorOnboardingController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $prefs = $user->vendorPreference;
        $categories = ProductCategory::orderBy('name')->get(['id','name']);
        return view('vendor.onboarding', compact('prefs', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'preferred_categories' => 'array',
            'preferred_categories.*' => 'integer|exists:product_categories,id',
            'min_quantity' => 'nullable|integer|min:0',
            'max_unit_price' => 'nullable|numeric|min:0',
            'notify_channels' => 'array',
            'notify_channels.*' => 'in:in_app,email',
            'notify_on' => 'required|in:all,matching',
        ]);

        $prefs = VendorPreference::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'preferred_categories' => $validated['preferred_categories'] ?? [],
                'min_quantity' => $validated['min_quantity'] ?? null,
                'max_unit_price' => $validated['max_unit_price'] ?? null,
                'notify_channels' => $validated['notify_channels'] ?? ['in_app'],
                'notify_on' => $validated['notify_on'] ?? 'matching',
                'onboarding_completed_at' => now(),
            ]
        );

        return redirect()->route('vendor.dashboard')->with('success', 'Preferences saved.');
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $prefs = $user->vendorPreference;

        $query = Product::with(['category','supplier'])->orderByDesc('created_at');

        if ($prefs && $prefs->notify_on === 'matching') {
            if (!empty($prefs->preferred_categories)) {
                $query->whereIn('category_id', $prefs->preferred_categories);
            }
            if (!is_null($prefs->min_quantity)) {
                $query->where('available_quantity', '>=', $prefs->min_quantity);
            }
            if (!is_null($prefs->max_unit_price)) {
                $query->where('unit_price', '<=', $prefs->max_unit_price);
            }
        }

        $products = $query->limit(20)->get();

        return view('vendor.dashboard', compact('products', 'prefs'));
    }
}
