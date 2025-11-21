<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MarketplaceListing;
use App\Services\DynamicPricingService;

class UpdateListingPrices extends Command
{
    protected $signature = 'pricing:update-listings {--seller= : Filter by seller (user id)} {--only-active : Only update active listings}';

    protected $description = 'Compute dynamic pricing (ML) for marketplace listings and persist suggested/final prices.';

    public function handle(DynamicPricingService $service): int
    {
        $sellerId = $this->option('seller');
        $onlyActive = (bool) $this->option('only-active');

        $query = MarketplaceListing::query()->with(['product', 'seller']);
        if ($sellerId) {
            $query->where('seller_id', $sellerId);
        }
        if ($onlyActive) {
            $query->active();
        }

        $total = 0;
        $updated = 0;

        $this->info('Updating listing prices...');
        $query->chunkById(100, function ($listings) use ($service, &$total, &$updated) {
            foreach ($listings as $listing) {
                $total++;
                $service->applyToListing($listing);
                $listing->save();
                $updated++;
            }
        });

        $this->info("Processed {$total} listings. Updated: {$updated}.");
        return Command::SUCCESS;
    }
}
