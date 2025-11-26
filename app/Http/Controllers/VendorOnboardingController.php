<?php

namespace App\Http\Controllers;

use App\Models\VendorPreference;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\VendorOffer;
use App\Models\CustomerOrder;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorOnboardingController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $prefs = $user->vendorPreference;
        // Only show Fish and Shellfish - vendors don't buy equipment/gear
        $categories = ProductCategory::whereIn('name', ['Fish', 'Shellfish'])
            ->orderBy('name')
            ->get(['id','name']);
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
            ->active()
            ->notSpoiled()
            ->where('available_quantity', '>', 0)
            ->whereHas('supplier', function($q) {
                $q->where('account_status', 'active');
            })
            ->orderByDesc('created_at');

        // Filter to only show fish and shellfish products (vendors don't buy equipment)
        $fishCategories = ProductCategory::whereIn('name', ['Fish', 'Shellfish'])->pluck('id');
        $query->whereIn('category_id', $fishCategories);

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

        // Calculate total spending from received orders (buying from fishermen - cash on delivery)
        $totalSpending = Order::where('vendor_id', $user->id)
            ->where('status', Order::STATUS_RECEIVED)
            ->sum('total');

        // Calculate total income from marketplace sales to buyers
        $totalIncome = CustomerOrder::whereHas('listing.vendorInventory', function($q) use ($user) {
                $q->where('vendor_id', $user->id);
            })
            ->whereIn('status', ['received', 'delivered'])
            ->sum('total');

        // Count accepted offers
        $acceptedOffersCount = VendorOffer::where('vendor_id', $user->id)
            ->where('status', 'accepted')
            ->count();

        // Get recent offers (all statuses)
        $recentAcceptedOffers = VendorOffer::where('vendor_id', $user->id)
            ->with(['fisherman', 'product'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get recent countered offers awaiting vendor response
        $recentCounterOffers = VendorOffer::where('vendor_id', $user->id)
            ->where('status', 'countered')
            ->with(['fisherman', 'product'])
            ->orderBy('responded_at', 'desc')
            ->limit(5)
            ->get();

        // Count pending offers for vendor (replaces unread messages)
        $pendingOffersCount = VendorOffer::where('vendor_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Get recent marketplace customer orders
        $recentCustomerOrders = CustomerOrder::where('vendor_id', $user->id)
            ->with(['buyer', 'listing.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get daily income data for last 14 days (marketplace sales)
        $incomeChartData = CustomerOrder::where('vendor_id', $user->id)
            ->whereIn('status', ['received', 'delivered'])
            ->where('created_at', '>=', now()->subDays(13))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as income')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get daily spending data for last 14 days (buying from fishermen)
        $spendingChartData = Order::where('vendor_id', $user->id)
            ->where('status', Order::STATUS_RECEIVED)
            ->where('created_at', '>=', now()->subDays(13))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as spending')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing days with 0
        $chartLabels = [];
        $chartIncomeValues = [];
        $chartSpendingValues = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('M d');
            
            $incomeData = $incomeChartData->firstWhere('date', $date);
            $chartIncomeValues[] = $incomeData ? (float)$incomeData->income : 0;
            
            $spendingData = $spendingChartData->firstWhere('date', $date);
            $chartSpendingValues[] = $spendingData ? (float)$spendingData->spending : 0;
        }

        return view('vendor.dashboard', [
            'products' => $products,
            'prefs' => $prefs,
            'applyFilters' => $applyFilters,
            'totalIncome' => $totalIncome,
            'totalSpending' => $totalSpending,
            'acceptedOffersCount' => $acceptedOffersCount,
            'recentAcceptedOffers' => $recentAcceptedOffers,
            'recentCounterOffers' => $recentCounterOffers,
            'pendingOffersCount' => $pendingOffersCount,
            'recentCustomerOrders' => $recentCustomerOrders,
            'chartLabels' => $chartLabels,
            'chartIncomeValues' => $chartIncomeValues,
            'chartSpendingValues' => $chartSpendingValues,
        ]);
    }

    /**
     * Vendor inbox: show all conversations where vendor is a participant
     * (buyer in fisherman chats, or seller in other contexts).
     */
    public function messages()
    {
        // Messaging feature removed
        $conversations = collect([]);
        return view('vendor.messages.inbox', compact('conversations'));
    }

    /**
     * Display simple offers table (bidding) for vendors.
     * Shows offers made by the vendor and any countered offers awaiting action.
     */
    public function offers()
    {
        $vendor = Auth::user();

        $status = request()->get('status');

        $query = VendorOffer::where('vendor_id', $vendor->id)
            ->with(['fisherman', 'product', 'product.category'])
            ->orderByDesc('created_at');

        if (in_array($status, ['pending', 'countered', 'accepted', 'withdrawn', 'auto_rejected'])) {
            $query->where('status', $status);
        } elseif ($status === 'all') {
            // Show all offers regardless of status
            // No additional filter needed
        } else {
            // Default view includes key statuses
            $query->whereIn('status', ['pending', 'countered', 'accepted']);
        }

        $offers = $query->paginate(20)->withQueryString();

        return view('vendor.offers.index', compact('offers'));
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
            ->active()
            ->notSpoiled()
            ->where('available_quantity', '>', 0)
            ->whereHas('supplier', function($q) {
                $q->where('account_status', 'active');
            })
            ->orderByDesc('created_at');

        // Filter out equipment and gear - vendors only buy fish and shellfish
        $fishCategories = ProductCategory::whereIn('name', ['Fish', 'Shellfish'])->pluck('id');
        $query->whereIn('category_id', $fishCategories);

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

        // Build bidding stats for visible products
        $productIds = $products->pluck('id')->all();
        $offers = \App\Models\VendorOffer::whereIn('product_id', $productIds)
            ->whereIn('status', ['pending', 'countered'])
            ->orderByDesc('offered_price')
            ->get()
            ->groupBy('product_id');

        $biddingStats = [];
        foreach ($offers as $pid => $rows) {
            $uniqueVendors = $rows->pluck('vendor_id')->unique();
            $topBids = $rows->pluck('offered_price')->take(3)->values()->all();

            $yourOffer = $rows->firstWhere('vendor_id', $user->id);
            $yourRank = null;
            if ($yourOffer) {
                // Determine rank among offers by offered_price desc
                $sorted = $rows->sortByDesc('offered_price')->values();
                foreach ($sorted as $idx => $row) {
                    if ((int)$row->vendor_id === (int)$user->id) {
                        $yourRank = $idx + 1; // 1-based
                        break;
                    }
                }
            }

            $biddingStats[$pid] = [
                'bidders' => $uniqueVendors->count(),
                'highest' => (float) ($rows->max('offered_price') ?? 0),
                'your_offer' => $yourOffer?->offered_price,
                'your_rank' => $yourRank,
                'top_bids' => $topBids,
            ];
        }

        // Get vendor's pending/countered offers to disable buttons
        $pendingOffers = \App\Models\VendorOffer::where('vendor_id', $user->id)
            ->whereIn('status', ['pending', 'countered'])
            ->pluck('product_id')
            ->toArray();

        return view('vendor.products.index', [
            'products' => $products,
            'prefs' => $prefs,
            'applyFilters' => $applyFilters,
            'pendingOffers' => $pendingOffers,
            'onlyFish' => $onlyFish,
            'q' => $q,
            'biddingStats' => $biddingStats,
        ]);
    }
}
