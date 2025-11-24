<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExpireProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire products that are older than 3 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = \App\Models\Product::where('status', 'active')
            ->where('created_at', '<', now()->subDays(3))
            ->update(['status' => 'expired']);

        $this->info("Expired {$count} products.");
    }
}
