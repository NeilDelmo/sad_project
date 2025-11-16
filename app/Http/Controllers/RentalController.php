<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Notifications\RentalApproved;
use App\Notifications\RentalRejected;
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

        // Get rentable gear with stock on hand; exclude retired
        $gearItems = Product::where('category_id', $gearCategory->id)
            ->where('is_rentable', true)
            ->where('rental_stock', '>', 0)
            ->where('equipment_status', '!=', 'retired')
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
            $expiresAt = now()->addDays(2);
            // Reserve units by incrementing reserved_stock only
            foreach ($rental->rentalItems as $item) {
                $product = $item->product()->lockForUpdate()->first();
                if ((($product->rental_stock ?? 0) - ($product->reserved_stock ?? 0)) < $item->quantity) {
                    throw new \Exception("Insufficient available stock for {$product->name}");
                }
                $product->increment('reserved_stock', $item->quantity);
            }

            // Update rental status
            $rental->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'expires_at' => $expiresAt,
            ]);

            DB::commit();

            // Send notification to user
            $rental->user->notify(new RentalApproved($rental));

            return back()->with('success', 'Rental approved. Units reserved until pickup.');

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

        // Send notification to user
        $rental->user->notify(new RentalRejected($rental));

        return back()->with('success', 'Rental request rejected.');
    }

    /**
     * Admin: Activate a rental (mark equipment as picked up)
     */
    public function activate(Rental $rental)
    {
        // Only admin can activate
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        if ($rental->status !== 'approved') {
            return back()->withErrors(['error' => 'Only approved rentals can be activated.']);
        }

        DB::beginTransaction();
        try {
            foreach ($rental->rentalItems as $item) {
                $product = $item->product()->lockForUpdate()->first();
                $product->decrement('reserved_stock', $item->quantity);
                $product->decrement('rental_stock', $item->quantity);
            }

            $rental->update([
                'status' => 'active',
                'picked_up_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Rental marked as active. Equipment picked up successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Admin: Process equipment return
     */
    public function processReturn(Request $request, Rental $rental)
    {
        // Only admin can process return
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        if ($rental->status !== 'active') {
            return back()->withErrors(['error' => 'Only active rentals can be returned.']);
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.rental_item_id' => 'required|exists:rental_items,id',
            'items.*.good' => 'required|integer|min:0',
            'items.*.fair' => 'required|integer|min:0',
            'items.*.damaged' => 'required|integer|min:0',
            'items.*.lost' => 'required|integer|min:0',
            'items.*.photos' => 'nullable|array',
            'items.*.photos.*' => 'image|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $lateFee = 0;
            
            // Calculate late fee if overdue (e.g., $50 per day late)
            if (now()->isAfter($rental->return_date)) {
                $daysLate = now()->diffInDays($rental->return_date);
                $lateFee = $daysLate * 50; // $50 per day
            }

            // Update condition counts for each item and adjust stock
            foreach ($validated['items'] as $key => $itemData) {
                $rentalItem = RentalItem::findOrFail($itemData['rental_item_id']);
                
                // Verify this item belongs to this rental
                if ($rentalItem->rental_id !== $rental->id) {
                    throw new \Exception('Invalid rental item.');
                }

                // Partial returns allowed: validate new counts do not exceed remaining
                $existingGood = (int)($rentalItem->good_count ?? 0);
                $existingFair = (int)($rentalItem->fair_count ?? 0);
                $existingDam  = (int)($rentalItem->damaged_count ?? 0);
                $existingLost = (int)($rentalItem->lost_count ?? 0);
                $already = $existingGood + $existingFair + $existingDam + $existingLost;
                $remaining = max(0, (int)$rentalItem->quantity - $already);

                $addGood = (int)($itemData['good'] ?? 0);
                $addFair = (int)($itemData['fair'] ?? 0);
                $addDam  = (int)($itemData['damaged'] ?? 0);
                $addLost = (int)($itemData['lost'] ?? 0);
                $delta = $addGood + $addFair + $addDam + $addLost;
                if ($delta < 1) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "items.{$rentalItem->id}.good" => 'Enter at least 1 unit to return for this item.',
                    ]);
                }
                if ($delta > $remaining) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "items.{$rentalItem->id}.good" => 'Return counts exceed remaining quantity.',
                    ]);
                }

                // Require a photo for any damaged or lost units in this return
                if (($addDam + $addLost) > 0) {
                    if (!$request->hasFile("items.{$rentalItem->id}.photos.0")) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            "items.{$rentalItem->id}.photos" => 'At least one photo is required when any units are damaged or lost.',
                        ]);
                    }
                }

                // Handle optional multiple photos
                $photoPath = null;
                if ($request->hasFile("items.{$rentalItem->id}.photos")) {
                    $files = $request->file("items.{$rentalItem->id}.photos");
                    foreach ($files as $idx => $photo) {
                        $photoName = 'damage_' . $rental->id . '_' . $rentalItem->id . '_' . time() . '_' . $idx . '.' . $photo->getClientOriginalExtension();
                        $stored = $photo->storeAs('rental_damage', $photoName, 'public');
                        if ($stored) {
                            \App\Models\RentalItemPhoto::create([
                                'rental_item_id' => $rentalItem->id,
                                'path' => $stored,
                            ]);
                            if ($photoPath === null) { $photoPath = $stored; }
                        }
                    }
                }

                // Accumulate counts and set summary condition
                $newGood = $existingGood + $addGood;
                $newFair = $existingFair + $addFair;
                $newDam  = $existingDam + $addDam;
                $newLost = $existingLost + $addLost;
                $newTotal = $newGood + $newFair + $newDam + $newLost;
                $condition = 'mixed';
                foreach (['good' => $newGood, 'fair' => $newFair, 'damaged' => $newDam, 'lost' => $newLost] as $label => $cnt) {
                    if ($cnt === (int)$rentalItem->quantity) { $condition = $label; break; }
                }

                $rentalItem->update([
                    'good_count' => $newGood,
                    'fair_count' => $newFair,
                    'damaged_count' => $newDam,
                    'lost_count' => $newLost,
                    'condition_in' => $condition,
                    'condition_in_photo' => $rentalItem->condition_in_photo ?: $photoPath,
                ]);

                $product = $rentalItem->product;
                
                // Handle based on condition
                $good = $addGood; $fair = $addFair; $dam = $addDam; $lost = $addLost;
                // Restore good + fair
                $restore = $good + $fair;
                if ($restore > 0) { $product->increment('rental_stock', $restore); }
                // Damaged units go to maintenance (do not restore)
                if ($dam > 0) {
                    $product->increment('maintenance_count', $dam);
                    $product->update(['equipment_status' => 'maintenance']);
                }
                // Lost units remain reduced from the earlier approval decrement
            }

            // Complete rental if all items fully returned
            $allReturned = $rental->rentalItems->every(function($it){
                $sum = (int)($it->good_count ?? 0) + (int)($it->fair_count ?? 0) + (int)($it->damaged_count ?? 0) + (int)($it->lost_count ?? 0);
                return $sum >= (int)$it->quantity;
            });
            if ($allReturned) {
                $rental->update([
                    'status' => 'completed',
                    'returned_at' => now(),
                    'late_fee' => $lateFee,
                ]);
                try { $rental->user->notify(new \App\Notifications\ReturnProcessed($rental)); } catch (\Throwable $t) {}
            }

            DB::commit();

            $message = 'Equipment returned successfully.';
            if ($lateFee > 0) {
                $message .= " Late fee applied: ₱" . number_format($lateFee, 2);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
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

    /**
     * Admin: View equipment maintenance dashboard
     */
    public function maintenanceDashboard()
    {
        // Only admin can access
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Get all equipment needing maintenance
        $maintenanceEquipment = Product::where('is_rentable', true)
            ->where('equipment_status', 'maintenance')
            ->with(['rentalItems' => function($query) {
                $query->where(function($q){
                        $q->where('condition_in', 'damaged')
                          ->orWhereNotNull('damaged_count')
                          ->orWhere('damaged_count', '>', 0)
                          ->orWhere('lost_count', '>', 0);
                    })
                    ->with('rental.user')
                    ->latest();
            }])
            ->get();

        // Get retired equipment
        $retiredEquipment = Product::where('is_rentable', true)
            ->where('equipment_status', 'retired')
            ->get();

        return view('rentals.admin.maintenance', compact('maintenanceEquipment', 'retiredEquipment'));
    }

    /**
     * Admin: Mark equipment as repaired
     */
    public function markRepaired(Request $request, Product $product)
    {
        // Only admin can repair
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        if ($product->equipment_status !== 'maintenance') {
            return back()->withErrors(['error' => 'Only equipment in maintenance can be marked as repaired.']);
        }

        $validated = $request->validate([
            'repair_notes' => 'required|string|max:1000',
            'repair_cost' => 'required|numeric|min:0',
            'repaired_count' => 'nullable|integer|min:0',
        ]);

        $timestamp = now()->format('Y-m-d H:i');
        $newNote = "[{$timestamp}] Repaired by " . auth()->user()->name . " - Cost: ₱" . number_format($validated['repair_cost'], 2) . "\n" . $validated['repair_notes'];
        $existingNotes = $product->maintenance_notes ?? '';
        $updatedNotes = $existingNotes ? $existingNotes . "\n\n---\n\n" . $newNote : $newNote;

        $repaired = min((int)($validated['repaired_count'] ?? 0), (int)($product->maintenance_count ?? 0));

        $updates = [
            'maintenance_notes' => $updatedNotes,
            'total_repair_cost' => ($product->total_repair_cost ?? 0) + $validated['repair_cost'],
            'last_maintenance_date' => now(),
        ];
        if ($repaired > 0) {
            $updates['maintenance_count'] = max(0, ($product->maintenance_count ?? 0) - $repaired);
            $updates['rental_stock'] = ($product->rental_stock ?? 0) + $repaired;
        }

        // If no units remain in maintenance, set status to available
        if ((($updates['maintenance_count'] ?? $product->maintenance_count ?? 0)) === 0) {
            $updates['equipment_status'] = 'available';
        }

        $product->update($updates);

        return back()->with('success', 'Equipment marked as repaired and available for rent.');
    }

    /**
     * Admin: Retire equipment permanently
     */
    public function retireEquipment(Product $product)
    {
        // Only admin can retire equipment
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $product->update([
            'equipment_status' => 'retired',
            'rental_stock' => 0, // Set stock to 0 so it doesn't appear in catalog
        ]);

        return back()->with('success', 'Equipment retired permanently.');
    }
}
