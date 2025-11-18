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
    public function index()
    {
        $fisherman = Auth::user();

        // Get all offers for fisherman's products
        $offers = VendorOffer::where('fisherman_id', $fisherman->id)
            ->with(['vendor', 'product', 'product.category'])
            ->whereIn('status', ['pending', 'countered'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('fisherman.offers.index', compact('offers'));
    }
}
