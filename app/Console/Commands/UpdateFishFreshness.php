<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MarketplaceListing;

class UpdateFishFreshness extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fish:freshness-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update freshness levels for active marketplace listings and unlist spoiled fish';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $thresholds = config('fish.freshness_threshold_minutes', []);
        $spoiledLevel = 'Spoiled';
        $countUpdated = 0;
        $countSpoiled = 0;

        MarketplaceListing::where('status', 'active')
            ->whereNull('unlisted_at')
            ->chunk(200, function ($listings) use ($thresholds, $spoiledLevel, &$countUpdated, &$countSpoiled) {
                foreach ($listings as $listing) {
                    if (!$listing->listing_date) {
                        continue;
                    }

                    $minutes = $listing->listing_date->diffInMinutes();
                    $level = $spoiledLevel;

                    foreach ($thresholds as $name => $maxMinutes) {
                        if ($minutes <= $maxMinutes) {
                            $level = $name;
                            break;
                        }
                    }

                    // Update freshness level if changed
                    if ($listing->freshness_level !== $level) {
                        $listing->freshness_level = $level;
                        $countUpdated++;
                    }

                    // Auto-unlist when spoiled
                    if ($level === $spoiledLevel && !$listing->unlisted_at) {
                        $listing->unlisted_at = now();
                        $listing->status = 'inactive';
                        $countSpoiled++;

                        // TODO: Send notification to supplier
                        // event(new FishSpoiled($listing));
                    }

                    $listing->saveQuietly();
                }
            });

        $this->info("Freshness updated: {$countUpdated} listings");
        $this->info("Spoiled/unlisted: {$countSpoiled} listings");

        return Command::SUCCESS;
    }
}
