<?php

namespace App\Http\Controllers;

use App\Models\VendorOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FishermanOfferController extends Controller
{
    /**
     * Display offers from vendors
     */
    public function index(Request $request)
    {
        $fisherman = Auth::user();
        $status = $request->get('status', 'pending');

        // Build query
        $query = VendorOffer::where('fisherman_id', $fisherman->id)
            ->with(['vendor', 'product', 'product.category']);

        // Filter by status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Get paginated offers
        $offers = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('fisherman.offers.index', compact('offers'));
    }
}
