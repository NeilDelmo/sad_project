<?php

namespace App\Console\Commands;

use App\Models\Rental;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancelExpiredRentals extends Command
{
    protected $signature = 'rentals:cancel-expired';
    protected $description = 'Auto-cancel approved rentals that were not picked up before expiry and release reserved stock';

    public function handle(): int
    {
        $expired = Rental::where('status', 'approved')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->with(['rentalItems'])
            ->get();

        $count = 0;

        foreach ($expired as $rental) {
            DB::transaction(function () use ($rental) {
                $rental->refresh();
                if ($rental->status !== 'approved') {
                    return;
                }
                foreach ($rental->rentalItems as $item) {
                    $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                    if (!$product) {
                        continue;
                    }
                    $release = min($item->quantity, max(0, (int) $product->reserved_stock));
                    if ($release > 0) {
                        $product->reserved_stock = max(0, (int) $product->reserved_stock - $release);
                        $product->save();
                    }
                }

                $rental->status = 'cancelled';
                $rental->save();

                // Notify user of auto-cancellation
                try {
                    $rental->user->notify(new \App\Notifications\RentalApprovalCancelled($rental));
                } catch (\Throwable $t) {
                    // Silent fail
                }
            });

            $count++;
        }

        $this->info("Auto-cancelled {$count} expired rentals.");
        return Command::SUCCESS;
    }
}
