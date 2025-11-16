<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Rental;
use App\Models\RentalItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    public function index()
    {
        // Get Gear category
        $gearCategory = ProductCategory::where('name', 'Gear')->first();
        
        if (!$gearCategory) {
            return view('rentals.index', ['gearItems' => []]);
        }

        // Get all available gear items
        $gearItems = Product::where('category_id', $gearCategory->id)
            ->where('is_rentable', true)
            ->where('rental_stock', '>', 0)
            ->get();

        return view('rentals.index', compact('gearItems'));
    }

    public function show(Product $product)
    {
        if (!$product->is_rentable) {
            abort(404);
        }

        return view('rentals.show', compact('product'));
    }

    public function create(Request $request)
    {
        $productId = $request->query('product_id');
        $product = null;
        
        if ($productId) {
            $product = Product::where('id', $productId)
                ->where('is_rentable', true)
                ->first();
        }

        return view('rentals.create', compact('product'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rental_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:rental_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $totalPrice = 0;
            $rentalDate = \Carbon\Carbon::parse($validated['rental_date']);
            $returnDate = \Carbon\Carbon::parse($validated['return_date']);
            $durationInDays = $rentalDate->diffInDays($returnDate) + 1;

            // Create rental
            $rental = Rental::create([
                'user_id' => auth()->id(),
                'status' => 'pending',
                'rental_date' => $validated['rental_date'],
                'return_date' => $validated['return_date'],
                'total_price' => 0, // Will update after calculating items
                'deposit_amount' => 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create rental items
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock availability
                if ($product->rental_stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $subtotal = $product->rental_price_per_day * $item['quantity'] * $durationInDays;
                $totalPrice += $subtotal;

                RentalItem::create([
                    'rental_id' => $rental->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price_per_day' => $product->rental_price_per_day,
                    'subtotal' => $subtotal,
                    'condition_out' => 'good',
                ]);
            }

            // Update rental total price and deposit (30% of total)
            $rental->update([
                'total_price' => $totalPrice,
                'deposit_amount' => $totalPrice * 0.3,
            ]);

            DB::commit();

            return redirect()->route('rentals.myrentals')
                ->with('success', 'Rental request submitted successfully! Awaiting approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function myRentals()
    {
        $rentals = Rental::where('user_id', auth()->id())
            ->with(['rentalItems.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('rentals.myrentals', compact('rentals'));
    }

    public function cancel(Rental $rental)
    {
        if ($rental->user_id !== auth()->id()) {
            abort(403);
        }

        if ($rental->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending rentals can be cancelled.']);
        }

        $rental->update(['status' => 'cancelled']);

        return back()->with('success', 'Rental request cancelled successfully.');
    }

    /**
     * Admin: Approve a rental request
     */
    public function approve(Rental $rental)
    {
        // Only admin can approve
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        if ($rental->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending rentals can be approved.']);
        }

        DB::beginTransaction();
        try {
            // Decrement rental stock for each item
            foreach ($rental->rentalItems as $item) {
                $product = $item->product;
                
                if ($product->rental_stock < $item->quantity) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $product->decrement('rental_stock', $item->quantity);
            }

            // Update rental status
            $rental->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            // TODO: Send notification to user
            return back()->with('success', 'Rental request approved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Admin: Reject a rental request
     */
    public function reject(Rental $rental)
    {
        // Only admin can reject
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        if ($rental->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending rentals can be rejected.']);
        }

        $rental->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // TODO: Send notification to user
        return back()->with('success', 'Rental request rejected.');
    }

    /**
     * Admin: View all rentals for management
     */
    public function adminIndex()
    {
        // Only admin can access
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $rentals = Rental::with(['user', 'rentalItems.product', 'approvedBy'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'active', 'completed', 'rejected', 'cancelled')")
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'pending' => Rental::where('status', 'pending')->count(),
            'approved' => Rental::where('status', 'approved')->count(),
            'active' => Rental::where('status', 'active')->count(),
            'completed' => Rental::where('status', 'completed')->count(),
        ];

        return view('rentals.admin.index', compact('rentals', 'stats'));
    }
}
