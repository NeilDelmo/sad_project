<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class RetireFancyGear extends Command
{
    protected $signature = 'rentals:retire-fancy-gear {--dry : Show what would change without modifying data}';
    protected $description = 'Mark high-tech/fancy gear as not rentable/retired based on name patterns';

    public function handle(): int
    {
        $patterns = [
            'gps', 'navigation', 'chartplotter', 'autopilot', 'radar', 'sonar', 'fish finder pro',
            'satellite', 'thermal', 'night vision', 'drone', 'ais', 'epirb', 'sat phone'
        ];

        $query = Product::query()->where('is_rentable', true);

        $query->where(function($q) use ($patterns) {
            foreach ($patterns as $p) {
                $q->orWhere('name', 'ILIKE', "%$p%");
                $q->orWhere('description', 'ILIKE', "%$p%");
            }
        });

        $toUpdate = $query->get();

        if ($toUpdate->isEmpty()) {
            $this->info('No matching high-tech items found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$toUpdate->count()} item(s) to retire:");
        foreach ($toUpdate as $prod) {
            $this->line(" - #{$prod->id} {$prod->name}");
        }

        if ($this->option('dry')) {
            $this->info('Dry run complete. No changes made.');
            return Command::SUCCESS;
        }

        $updated = 0;
        foreach ($toUpdate as $prod) {
            $prod->is_rentable = false;
            $prod->equipment_status = 'retired';
            $prod->save();
            $updated++;
        }

        $this->info("Updated {$updated} item(s): set is_rentable=false, equipment_status=retired.");
        return Command::SUCCESS;
    }
}
