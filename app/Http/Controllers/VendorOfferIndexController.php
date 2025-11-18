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
    public function index()
    {
        $vendor = Auth::user();

        // Get all offers made by vendor
        $offers = VendorOffer::where('vendor_id', $vendor->id)
            ->with(['fisherman', 'product', 'product.category'])
            ->whereIn('status', ['pending', 'countered', 'accepted'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vendor.offers.index', compact('offers'));
    }
}
