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
    /**
     * Rental statuses that should lock inventory adjustments.
     */
    private const ACTIVE_RENTAL_STATUSES = ['pending', 'approved', 'active'];
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
        // Get cart items from session
        $cart = session()->get('rental_cart', []);
        $cartItems = [];
        
        foreach ($cart as $productId => $quantity) {
            $product = Product::where('id', $productId)
                ->where('is_rentable', true)
                ->first();
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            }
        }

        return view('rentals.create', compact('cartItems'));
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        
        if (!$product->is_rentable) {
            return back()->withErrors(['error' => 'This item is not available for rent.']);
        }

        $cart = session()->get('rental_cart', []);
        $productId = $validated['product_id'];
        
        // Add or update quantity in cart
        if (isset($cart[$productId])) {
            $cart[$productId] += $validated['quantity'];
        } else {
            $cart[$productId] = $validated['quantity'];
        }
        
        // Validate against stock
        if ($cart[$productId] > $product->rental_stock) {
            $cart[$productId] = $product->rental_stock;
            session()->put('rental_cart', $cart);
            return back()->with('warning', "Added {$product->name} to cart (limited to available stock: {$product->rental_stock})");
        }
        
        session()->put('rental_cart', $cart);
        
        return back()->with('success', "{$product->name} added to cart!");
    }

    public function removeFromCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = session()->get('rental_cart', []);
        unset($cart[$validated['product_id']]);
        session()->put('rental_cart', $cart);
        
        return back()->with('success', 'Item removed from cart.');
    }

    public function clearCart()
    {
        session()->forget('rental_cart');
        return back()->with('success', 'Cart cleared.');
    }

    public function getCartCount()
    {
        $cart = session()->get('rental_cart', []);
        return response()->json(['count' => count($cart)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rental_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:rental_date',
            'notes' => 'nullable|string',
        ]);

        // Get cart items from session
        $cart = session()->get('rental_cart', []);
        
        if (empty($cart)) {
            return back()->withErrors(['error' => 'Your cart is empty. Please add items before submitting.']);
        }

        // Convert cart to items array format
        $items = [];
        foreach ($cart as $productId => $quantity) {
            $items[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }
        
        $validated['items'] = $items;

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

            // Apply Fisherman Trust Discount
            $discountAmount = 0;
            $user = auth()->user();
            if ($user->user_type === 'fisherman') {
                $tier = $user->trust_tier ?? 'bronze';
                $discountPercent = match($tier) {
                    'platinum' => 0.10, // 10%
                    'gold' => 0.05,     // 5%
                    'silver' => 0.02,   // 2%
                    default => 0,
                };
                
                if ($discountPercent > 0) {
                    $discountAmount = $totalPrice * $discountPercent;
                    $totalPrice -= $discountAmount;
                }
            }

            // Update rental total price and deposit (30% of total)
            $rental->update([
                'total_price' => $totalPrice,
                'discount_amount' => $discountAmount,
                'deposit_amount' => $totalPrice * 0.3,
            ]);

            DB::commit();

            // Clear the cart after successful submission
            session()->forget('rental_cart');

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

    public function myReports()
    {
        // Auto-resolve stuck reports
        $stuckReports = \App\Models\RentalIssueReport::where('user_id', auth()->id())
            ->where('status', 'under_review')
            ->with(['rental.rentalItems.product'])
            ->get();

        foreach ($stuckReports as $report) {
            $rental = $report->rental;
            if (!$rental) continue;

            $allResolved = true;
            foreach ($rental->rentalItems as $item) {
                $product = $item->product;
                if (!$product) continue;
                
                // If product is in maintenance, the report is still valid
                if ($product->equipment_status === 'maintenance') {
                    $allResolved = false;
                    break;
                }
            }

            if ($allResolved) {
                $report->update(['status' => 'resolved']);
            }
        }

        $reports = \App\Models\RentalIssueReport::where('user_id', auth()->id())
            ->with(['rental.rentalItems.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('rentals.myreports', compact('reports'));
    }

    public function reportForm(Rental $rental)
    {
        if ($rental->user_id !== auth()->id()) {
            abort(403);
        }
        return view('rentals.report', compact('rental'));
    }

    public function submitReport(Request $request, Rental $rental)
    {
        if ($rental->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'issue_type' => 'required|string|in:pre_existing,accidental,lost,other',
            'severity' => 'nullable|string|in:low,medium,high',
            'title' => 'nullable|string|max:120',
            'description' => 'required|string|max:2000',
            'photos.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $idx => $photo) {
                $name = 'issue_' . $rental->id . '_' . time() . '_' . $idx . '.' . $photo->getClientOriginalExtension();
                $stored = $photo->storeAs('images/rental_issues', $name, 'public');
                if ($stored) { $photos[] = $stored; }
            }
        }

        $report = \App\Models\RentalIssueReport::create([
            'rental_id' => $rental->id,
            'user_id' => auth()->id(),
            'issue_type' => $validated['issue_type'],
            'severity' => $validated['severity'] ?? null,
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'],
            'photos' => $photos ?: null,
            'status' => 'open',
        ]);

        // Notify admins
        try {
            $admins = \Spatie\Permission\Models\Role::findByName('admin')->users ?? collect();
        } catch (\Throwable $e) {
            $admins = \App\Models\User::where('user_type', 'admin')->get();
        }
        try {
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\RentalIssueReported($report));
            }
        } catch (\Throwable $e) {}

        return redirect()->route('rentals.myrentals')->with('success', 'Issue reported. An admin will review and contact you.');
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
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($rental->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending rentals can be approved.']);
        }

        DB::beginTransaction();
        try {
            $expiresAt = now()->addDays(2);
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
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
                'pickup_otp' => $otp,
                'otp_generated_at' => now(),
            ]);

            DB::commit();

            // Send notification to user
            $rental->user->notify(new RentalApproved($rental));

            return back()->with('success', "Rental approved. Pickup OTP: {$otp}. Units reserved until pickup.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Admin: Reject a rental request
     */
    public function reject(Request $request, Rental $rental)
    {
        // Only admin can reject
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($rental->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending rentals can be rejected.']);
        }

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $rental->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        // Send notification to user
        $rental->user->notify(new RentalRejected($rental));

        return back()->with('success', 'Rental request rejected.');
    }

    /**
     * Admin: Activate a rental (mark equipment as picked up)
     */
    public function activate(Request $request, Rental $rental)
    {
        // Only admin can activate
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($rental->status !== 'approved') {
            return back()->withErrors(['error' => 'Only approved rentals can be activated.']);
        }

        // Validate OTP
        $validated = $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if ($validated['otp'] !== $rental->pickup_otp) {
            return back()->withErrors(['error' => 'Invalid OTP. Please verify the code.']);
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
                'otp_verified_at' => now(),
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
        if (!auth()->user()->isAdmin()) {
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
            'items.*.photos' => 'nullable|array|max:5',
            'items.*.photos.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            'waive_damage_fees' => 'nullable|boolean',
            'waive_lost_fees' => 'nullable|boolean',
            'waive_reason' => 'nullable|string|max:500',
        ]);

        $waiveDamage = $request->boolean('waive_damage_fees');
        $waiveLost = $request->boolean('waive_lost_fees');
        $waiveReason = trim((string) $request->input('waive_reason', ''));

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
                        $stored = $photo->storeAs('images/rental_damage', $photoName, 'public');
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
                // Calculate settlement charges
                $damageFee = 0;
                $lostFee = 0;
                foreach ($rental->rentalItems as $item) {
                    // Damage fee: 50% of item price per damaged unit
                    $damageCount = (int)($item->damaged_count ?? 0);
                    if ($damageCount > 0) {
                        $damageFee += ($item->price_per_day * 10) * $damageCount * 0.5; // estimate full price as 10x daily
                    }

                    // Lost fee: 100% of item price per lost unit
                    $lostCount = (int)($item->lost_count ?? 0);
                    if ($lostCount > 0) {
                        $lostFee += ($item->price_per_day * 10) * $lostCount;
                    }
                }

                if ($waiveDamage) {
                    $damageFee = 0;
                }
                if ($waiveLost) {
                    $lostFee = 0;
                }

                $waiveNotes = [];
                if ($waiveDamage) { $waiveNotes[] = 'damage fees waived'; }
                if ($waiveLost) { $waiveNotes[] = 'lost fees waived'; }
                $waiveNoteText = null;
                if (!empty($waiveNotes)) {
                    $waiveNoteText = '[' . now()->format('Y-m-d H:i') . '] ' . ucfirst(implode(' & ', $waiveNotes)) . ' by ' . auth()->user()->name;
                    if ($waiveReason !== '') {
                        $waiveNoteText .= ' (Reason: ' . $waiveReason . ')';
                    }
                }

                $adminNotes = $rental->admin_notes ?? '';
                if ($waiveNoteText) {
                    $adminNotes = $adminNotes ? $adminNotes . "\n\n" . $waiveNoteText : $waiveNoteText;
                }

                $totalCharges = $rental->total_price + $lateFee + $damageFee + $lostFee;
                $amountDue = max(0, $totalCharges - ($rental->deposit_paid ?? 0));
                
                $rental->update([
                    'status' => 'completed',
                    'returned_at' => now(),
                    'late_fee' => $lateFee,
                    'damage_fee_waived' => $waiveDamage,
                    'lost_fee_waived' => $waiveLost,
                    'waive_reason' => $waiveReason ?: null,
                    'admin_notes' => $adminNotes,
                    'damage_fee' => $damageFee,
                    'lost_fee' => $lostFee,
                    'total_charges' => $totalCharges,
                    'amount_due' => $amountDue,
                    'payment_status' => $amountDue > 0 ? 'pending' : 'paid',
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
     * Admin: View all rental products (inventory)
     */
    public function adminProducts(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $statusFilter = $request->query('status');
        $allowedStatuses = ['available', 'maintenance', 'retired'];

        $productsQuery = Product::where('is_rentable', true)
            ->withCount(['rentalItems as active_rental_items_count' => function ($query) {
                $query->whereHas('rental', function ($rentals) {
                    $rentals->whereIn('status', self::ACTIVE_RENTAL_STATUSES);
                });
            }]);

        if ($statusFilter && in_array($statusFilter, $allowedStatuses, true)) {
            $productsQuery->where('equipment_status', $statusFilter);
        }

        $products = $productsQuery->orderBy('created_at', 'desc')->get();

        $statusCounts = Product::where('is_rentable', true)
            ->select('equipment_status', DB::raw('COUNT(*) as total'))
            ->groupBy('equipment_status')
            ->pluck('total', 'equipment_status');

        return view('rentals.admin.products', [
            'products' => $products,
            'statusFilter' => $statusFilter,
            'statusCounts' => $statusCounts,
        ]);
    }

    /**
     * Admin: Show form to edit a rental product
     */
    public function editProduct(Product $product)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        if (!$product->is_rentable) {
            abort(404, 'Not a rental product');
        }

        $inventoryLocked = $this->productHasActiveRentals($product);

        return view('rentals.admin.edit_product', compact('product', 'inventoryLocked'));
    }

    /**
     * Admin: Update a rental product
     */
    public function updateProduct(Request $request, Product $product)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'rental_price_per_day' => 'required|numeric|min:0',
            'rental_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'equipment_status' => 'required|in:available,maintenance,retired',
        ]);

        $inventoryLocked = $this->productHasActiveRentals($product);
        if ($inventoryLocked) {
            $lockedFields = [];

            if ((int) $validated['rental_stock'] !== (int) $product->rental_stock) {
                $lockedFields[] = 'stock level';
            }

            if ($validated['equipment_status'] !== $product->equipment_status) {
                $lockedFields[] = 'equipment status';
            }

            if (!empty($lockedFields)) {
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'Stock and equipment status cannot be changed while this item has pending or active rentals. Finish or cancel those rentals before updating these fields.']);
            }
        }

        $hasOpenMaintenance = ($product->equipment_status === 'maintenance') && (($product->maintenance_count ?? 0) > 0);
        if ($hasOpenMaintenance && $validated['equipment_status'] === 'available') {
            return back()
                ->withInput()
                ->withErrors(['error' => 'This equipment still has units under maintenance. Mark it as repaired from the maintenance dashboard before switching back to Available.']);
        }

        $updates = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'rental_price_per_day' => $validated['rental_price_per_day'],
            'rental_stock' => $validated['rental_stock'],
            'equipment_status' => $validated['equipment_status'],
        ];

        if ($request->hasFile('image')) {
            $updates['image_path'] = $request->file('image')->store('images/rentals', 'public');
        }

        $product->update($updates);

        return redirect()->route('rentals.admin.products')->with('success', 'Rental product updated successfully.');
    }

    /**
     * Admin: Show form to create a new rental product
     */
    public function createProduct()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        return view('rentals.admin.create_product');
    }

    /**
     * Admin: Store a new rental product
     */
    public function storeProduct(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'rental_price_per_day' => 'required|numeric|min:0',
            'rental_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $gearCategory = ProductCategory::firstOrCreate(['name' => 'Gear']);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/rentals', 'public');
        }

        Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'unit_price' => 0, // Not for sale
            'available_quantity' => 0, // Not for sale
            'category_id' => $gearCategory->id,
            'image_path' => $imagePath,
            'is_rentable' => true,
            'rental_price_per_day' => $validated['rental_price_per_day'],
            'rental_stock' => $validated['rental_stock'],
            'equipment_status' => 'available',
            'supplier_id' => auth()->id(), // Admin created
            'status' => 'active',
        ]);

        return redirect()->route('rentals.admin.index')->with('success', 'Rental product created successfully.');
    }

    /**
     * Admin: View all rentals for management
     */
    public function adminIndex()
    {
        // Only admin can access
        if (!auth()->user()->isAdmin()) {
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
        if (!auth()->user()->isAdmin()) {
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
     * Determine if a rental product is currently tied to locking rentals.
     */
    private function productHasActiveRentals(Product $product): bool
    {
        return $product->rentalItems()
            ->whereHas('rental', function ($query) {
                $query->whereIn('status', self::ACTIVE_RENTAL_STATUSES);
            })
            ->exists();
    }

    /**
     * Admin: Mark equipment as repaired
     */
    public function markRepaired(Request $request, Product $product)
    {
        // Only admin can repair
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($product->equipment_status !== 'maintenance') {
            return back()->withErrors(['error' => 'Only equipment in maintenance can be marked as repaired.']);
        }

        $validated = $request->validate([
            'repair_notes' => 'nullable|string|max:1000',
            'repair_cost' => 'nullable|numeric|min:0',
            'repaired_count' => 'nullable|integer|min:0',
            'discarded_count' => 'nullable|integer|min:0',
            'discard_reason' => 'nullable|string|max:255',
        ]);

        $repaired = (int)($validated['repaired_count'] ?? 0);
        $discarded = (int)($validated['discarded_count'] ?? 0);
        $cost = (float)($validated['repair_cost'] ?? 0);
        $notes = $validated['repair_notes'] ?? '';
        $discardReason = $validated['discard_reason'] ?? '';

        if ($repaired === 0 && $discarded === 0 && empty($notes)) {
             return back()->withErrors(['error' => 'Please specify an action (repair units, discard units, or add notes).']);
        }

        $timestamp = now()->format('Y-m-d H:i');
        $logEntry = "[{$timestamp}] Maintenance Update by " . auth()->user()->name;
        
        if ($cost > 0) {
            $logEntry .= " - Cost: ₱" . number_format($cost, 2);
        }
        
        if (!empty($notes)) {
            $logEntry .= "\nNotes: " . $notes;
        }
        
        $totalMaintenance = (int)($product->maintenance_count ?? 0);

        if ($repaired + $discarded > $totalMaintenance) {
            return back()->withErrors(['error' => 'Total repaired and discarded units cannot exceed units in maintenance.']);
        }

        if ($repaired > 0) {
            $logEntry .= "\nAction: Repaired {$repaired} unit(s) and returned to stock.";
        }

        if ($discarded > 0) {
            $reasonStr = $discardReason ? " (Reason: {$discardReason})" : "";
            $logEntry .= "\nAction: Discarded {$discarded} unit(s){$reasonStr}.";
        }

        $existingNotes = $product->maintenance_notes ?? '';
        $updatedNotes = $existingNotes ? $existingNotes . "\n\n---\n\n" . $logEntry : $logEntry;

        $updates = [
            'maintenance_notes' => $updatedNotes,
            'total_repair_cost' => ($product->total_repair_cost ?? 0) + $cost,
            'last_maintenance_date' => now(),
        ];

        if ($repaired > 0 || $discarded > 0) {
            $updates['maintenance_count'] = max(0, $totalMaintenance - $repaired - $discarded);
            // Only increment stock for repaired units
            if ($repaired > 0) {
                $updates['rental_stock'] = ($product->rental_stock ?? 0) + $repaired;
            }
        }

        // If no units remain in maintenance, set status to available
        if (($updates['maintenance_count'] ?? $totalMaintenance) === 0) {
            $updates['equipment_status'] = 'available';
        }

        $product->update($updates);

        // Auto-resolve related reports based on processed count
        $processedCount = $repaired + $discarded;
        if ($processedCount > 0) {
            $reports = \App\Models\RentalIssueReport::where('status', 'under_review')
                ->whereHas('rental.rentalItems', function($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
                ->orderBy('created_at', 'asc')
                ->take($processedCount)
                ->get();

            foreach ($reports as $report) {
                $report->update(['status' => 'resolved']);
            }
        }

        return back()->with('success', 'Equipment maintenance updated.');
    }

    /**
     * Admin: Retire equipment permanently
     */
    public function retireEquipment(Product $product)
    {
        // Only admin can retire equipment
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $product->update([
            'equipment_status' => 'retired',
            'rental_stock' => 0, // Set stock to 0 so it doesn't appear in catalog
        ]);

        // Resolve all pending reports for this product
        \App\Models\RentalIssueReport::whereIn('status', ['open', 'under_review'])
            ->whereHas('rental.rentalItems', function($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->update(['status' => 'resolved']);

        return back()->with('success', 'Equipment retired permanently.');
    }

    /**
     * Admin: View all issue reports
     */
    public function viewReports()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $reports = \App\Models\RentalIssueReport::with(['rental.user', 'rental.rentalItems.product', 'user'])
            ->orderByRaw("FIELD(status, 'open', 'under_review', 'resolved')")
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'open' => \App\Models\RentalIssueReport::where('status', 'open')->count(),
            'under_review' => \App\Models\RentalIssueReport::where('status', 'under_review')->count(),
            'resolved' => \App\Models\RentalIssueReport::where('status', 'resolved')->count(),
        ];

        return view('rentals.admin.reports', compact('reports', 'stats'));
    }

    /**
     * Admin: Mark equipment from report as needing maintenance
     */
    public function markForMaintenance(Request $request, \App\Models\RentalIssueReport $report)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'units' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Move units to maintenance
        $product->increment('maintenance_count', $validated['units']);
        $product->decrement('rental_stock', min($validated['units'], $product->rental_stock ?? 0));
        $product->update(['equipment_status' => 'maintenance']);

        // Update report status
        $report->update(['status' => 'under_review']);

        // Log note
        $timestamp = now()->format('Y-m-d H:i');
        $adminNote = "[{$timestamp}] Marked {$validated['units']} unit(s) for maintenance by " . auth()->user()->username . " - From Report #{$report->id}";
        if (!empty($validated['notes'])) {
            $adminNote .= "\n" . $validated['notes'];
        }
        $existingNotes = $product->maintenance_notes ?? '';
        $product->update([
            'maintenance_notes' => $existingNotes ? $existingNotes . "\n\n---\n\n" . $adminNote : $adminNote,
        ]);

        return back()->with('success', "{$validated['units']} unit(s) of {$product->name} moved to maintenance.");
    }

    /**
     * Admin: Resolve a report
     */
    public function resolveReport(\App\Models\RentalIssueReport $report)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $report->update(['status' => 'resolved']);

        return back()->with('success', 'Report marked as resolved.');
    }
}
