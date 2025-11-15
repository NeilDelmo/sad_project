<?php

namespace App\Http\Controllers;

use App\Models\VendorPreference;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\VendorOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'notify_on' => 'required|in:all,matching',
        ]);

        $prefs = VendorPreference::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'preferred_categories' => $validated['preferred_categories'] ?? [],
                'min_quantity' => $validated['min_quantity'] ?? null,
                'max_unit_price' => $validated['max_unit_price'] ?? null,
                'notify_channels' => ['in_app'],
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

        // Always show all products by default; apply filters only on request
        $applyFilters = $request->boolean('apply_filters', false);

        $query = Product::with(['category', 'supplier', 'activeMarketplaceListing'])
            ->orderByDesc('created_at');

        if ($applyFilters && $prefs) {
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

        // Calculate total spending from accepted offers
        $totalSpending = VendorOffer::where('vendor_id', $user->id)
            ->where('status', 'accepted')
            ->sum(DB::raw('offered_price * quantity'));

        // Count accepted offers
        $acceptedOffersCount = VendorOffer::where('vendor_id', $user->id)
            ->where('status', 'accepted')
            ->count();

        // Get recent accepted offers
        $recentAcceptedOffers = VendorOffer::where('vendor_id', $user->id)
            ->where('status', 'accepted')
            ->with(['fisherman', 'product'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent countered offers awaiting vendor response
        $recentCounterOffers = VendorOffer::where('vendor_id', $user->id)
            ->where('status', 'countered')
            ->with(['fisherman', 'product'])
            ->orderBy('responded_at', 'desc')
            ->limit(5)
            ->get();

        // Count unread messages
        $unreadCount = \App\Models\Conversation::where(function($q) use ($user) {
            $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
        })->whereHas('messages', function($q) use ($user) {
            $q->where('is_read', false)->where('sender_id', '!=', $user->id);
        })->count();

        return view('vendor.dashboard', [
            'products' => $products,
            'prefs' => $prefs,
            'applyFilters' => $applyFilters,
            'totalSpending' => $totalSpending,
            'acceptedOffersCount' => $acceptedOffersCount,
            'recentAcceptedOffers' => $recentAcceptedOffers,
            'recentCounterOffers' => $recentCounterOffers,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Vendor inbox: show all conversations where vendor is a participant
     * (buyer in fisherman chats, or seller in other contexts).
     */
    public function messages()
    {
        $conversations = \App\Models\Conversation::where(function ($q) {
                $q->where('buyer_id', Auth::id())
                  ->orWhere('seller_id', Auth::id());
            })
            ->with(['buyer', 'product', 'latestMessage', 'messages'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        // Add unread count for each conversation
        $conversations->each(function ($conversation) {
            $conversation->unread_count = $conversation->messages()
                ->where('is_read', false)
                ->where('sender_id', '!=', Auth::id())
                ->count();
        });

        return view('vendor.messages.inbox', compact('conversations'));
    }

    /**
     * Vendor browse page: show all fisherman products with optional filters/search.
     */
    public function browseProducts(Request $request)
    {
        $user = Auth::user();
        $prefs = $user->vendorPreference;

        $applyFilters = $request->boolean('apply_filters', false);
        $onlyFish = $request->boolean('only_fish', false);
        $q = trim((string) $request->get('q', ''));

        $query = Product::with(['category', 'supplier', 'activeMarketplaceListing'])
            ->orderByDesc('created_at');

        if ($onlyFish) {
            $aliases = config('fish.category_aliases', ['Fish', 'Fresh Fish']);
            $query->whereHas('category', function ($cat) use ($aliases) {
                $cat->whereIn('name', $aliases);
            });
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($applyFilters && $prefs) {
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

        $products = $query->paginate(24)->withQueryString();

        return view('vendor.products.index', [
            'products' => $products,
            'prefs' => $prefs,
            'applyFilters' => $applyFilters,
            'onlyFish' => $onlyFish,
            'q' => $q,
        ]);
    }
}
