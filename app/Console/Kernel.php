<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('offers:expire')->hourly();
        $schedule->command('rentals:cancel-expired')->everyFiveMinutes();
        // Dynamic pricing recalculation for active listings every hour
        $schedule->command('pricing:update-listings --only-active')->hourly();
        // Example: run fancy-gear retirement nightly (commented by default)
        // $schedule->command('rentals:retire-fancy-gear --dry')->dailyAt('02:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
