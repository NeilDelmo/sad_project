<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Product;
use App\Models\Order;
use App\Models\Rental;
use App\Models\VendorOffer;
use App\Models\CustomerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FishermanController extends Controller
{
    /**
     * Display the fisherman dashboard
     */
    public function dashboard()
    {
        $fisherman = Auth::user();

        // Get fisherman's products count
        $productsCount = Product::where('supplier_id', $fisherman->id)->count();

        // Count pending offers for fisherman (replaces unread messages)
        $pendingOffersCount = VendorOffer::where('fisherman_id', $fisherman->id)
            ->where('status', 'pending')
            ->count();

        // Get recent products
        $recentProducts = Product::where('supplier_id', $fisherman->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate total income from received orders (vendor confirmed receipt - COD)
        $totalIncome = Order::where('fisherman_id', $fisherman->id)
            ->where('status', Order::STATUS_RECEIVED)
            ->sum('total');

        // Calculate total spending from rentals (equipment/gear rentals)
        $totalSpending = Rental::where('user_id', $fisherman->id)
            ->whereIn('status', ['completed', 'returned'])
            ->sum('total_charges');

        // Count accepted offers
        $acceptedOffersCount = VendorOffer::where('fisherman_id', $fisherman->id)
            ->where('status', 'accepted')
            ->count();

        // Get recent offers (all statuses)
        $recentAcceptedOffers = VendorOffer::where('fisherman_id', $fisherman->id)
            ->with(['vendor', 'product'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get rental statistics
        $activeRentalsCount = Rental::where('user_id', $fisherman->id)
            ->whereIn('status', ['pending', 'approved', 'active'])
            ->count();

        $pendingRentalsCount = Rental::where('user_id', $fisherman->id)
            ->where('status', 'pending')
            ->count();

        // Get daily income data for last 14 days (line chart)
        $incomeChartData = Order::where('fisherman_id', $fisherman->id)
            ->where('status', Order::STATUS_RECEIVED)
            ->where('created_at', '>=', now()->subDays(13))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as income')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get daily spending data for last 14 days (rentals)
        $spendingChartData = Rental::where('user_id', $fisherman->id)
            ->whereIn('status', ['completed', 'returned'])
            ->where('created_at', '>=', now()->subDays(13))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_charges) as spending')
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

        return view('fisherman.dashboard', compact(
            'productsCount',
            'pendingOffersCount',
            'recentProducts',
            'totalIncome',
            'totalSpending',
            'acceptedOffersCount',
            'recentAcceptedOffers',
            'activeRentalsCount',
            'pendingRentalsCount',
            'chartLabels',
            'chartIncomeValues',
            'chartSpendingValues'
        ));
    }

    /**
     * Display inbox with all conversations
     */
    public function inbox()
    {
        // Messaging feature removed
        $conversations = collect([]);
        return view('fisherman.messages.inbox', compact('conversations'));
    }

    /**
     * Display simple offers table (bidding) for fishermen.
     * Shows pending and countered offers with Accept and Counter actions.
     */
    public function offers()
    {
        $fisherman = Auth::user();

        $status = request()->get('status');

        $query = VendorOffer::where('fisherman_id', $fisherman->id)
            ->with(['vendor', 'product', 'product.category'])
            ->orderByDesc('created_at');

        if (in_array($status, ['pending', 'countered', 'accepted', 'auto_rejected', 'closed', 'withdrawn', 'expired'])) {
            $query->where('status', $status);
        } elseif ($status === 'all') {
            // no additional filter
        } else {
            // Default to actionable filters (pending + countered)
            $query->whereIn('status', ['pending', 'countered']);
        }

        $offers = $query->paginate(20)->withQueryString();

        return view('fisherman.offers.index', compact('offers'));
    }
}
