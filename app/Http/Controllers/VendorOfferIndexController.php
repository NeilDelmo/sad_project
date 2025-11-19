<?php

namespace App\Http\Controllers;

use App\Models\VendorOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorOfferIndexController extends Controller
{
    /**
     * Display offers to fishermen
     */
    public function index(Request $request)
    {
        $vendor = Auth::user();
        $status = $request->get('status', 'pending');

        // Build query
        $query = VendorOffer::where('vendor_id', $vendor->id)
            ->with(['fisherman', 'product', 'product.category']);

        // Filter by status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Get paginated offers
        $offers = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('vendor.offers.index', compact('offers'));
    }
}
