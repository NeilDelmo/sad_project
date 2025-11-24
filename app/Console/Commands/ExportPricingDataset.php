<?php

namespace App\Console\Commands;

use App\Models\PricingPredictionLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class ExportPricingDataset extends Command
{
    protected $signature = 'pricing:export-dataset
        {--days=30 : Number of days of logs to include}
        {--path= : Output CSV destination}
        {--limit=0 : Maximum rows to export (0 = unlimited)}';

    protected $description = 'Export pricing prediction logs to CSV for ML training.';

    private const DEFAULT_OUTPUT = 'python/pricing_dataset.csv';

    public function handle(): int
    {
        $days = max((int) $this->option('days'), 1);
        $limit = (int) $this->option('limit');
        $path = $this->option('path') ?: App::basePath(self::DEFAULT_OUTPUT);
        $cutoff = Carbon::now()->subDays($days);

        $query = PricingPredictionLog::query()
            ->whereNotNull('multiplier')
            ->whereNotNull('features')
            ->where('created_at', '>=', $cutoff)
            ->orderBy('created_at');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $logs = $query->get();

        if ($logs->isEmpty()) {
            $this->warn('No pricing logs found for the given window.');
            return Command::SUCCESS;
        }

        File::ensureDirectoryExists(dirname($path));
        $handle = fopen($path, 'w');

        $headers = [
            'product_id',
            'offer_id',
            'listing_id',
            'context',
            'freshness_score',
            'available_quantity',
            'demand_factor',
            'seasonality_factor',
            'time_of_day',
            'vendor_rating',
            'category_id',
            'vendor_total_items',
            'vendor_total_quantity',
            'demand_score',
            'recent_retail_orders',
            'wholesale_acceptance_rate',
            'supply_pressure',
            'supply_total',
            'retail_median',
            'base_price',
            'market_price',
            'portfolio_factor',
            'optimal_price_multiplier',
            'confidence',
            'used_fallback',
            'runtime_ms',
            'created_at',
        ];

        fputcsv($handle, $headers);

        $count = 0;
        foreach ($logs as $log) {
            $features = $log->features ?? [];
            $signals = $log->signals ?? [];
            $extra = $log->extra ?? [];

            $row = [
                $log->product_id,
                $log->offer_id,
                $log->listing_id,
                $log->context,
                Arr::get($features, 'freshness_score'),
                Arr::get($features, 'available_quantity'),
                Arr::get($features, 'demand_factor'),
                Arr::get($features, 'seasonality_factor'),
                Arr::get($features, 'time_of_day'),
                Arr::get($features, 'vendor_rating'),
                Arr::get($features, 'category_id'),
                Arr::get($features, 'vendor_total_items'),
                Arr::get($features, 'vendor_total_quantity'),
                Arr::get($signals, 'demand.score'),
                Arr::get($signals, 'demand.recent_retail_orders'),
                Arr::get($signals, 'wholesale.acceptance_rate'),
                Arr::get($signals, 'supply.pressure'),
                Arr::get($signals, 'supply.total_supply'),
                Arr::get($signals, 'retail.median'),
                Arr::get($extra, 'base_price'),
                Arr::get($extra, 'market_price'),
                Arr::get($extra, 'portfolio_factor'),
                $log->multiplier,
                $log->confidence,
                $log->used_fallback ? 1 : 0,
                $log->runtime_ms,
                $log->created_at?->toDateTimeString(),
            ];

            fputcsv($handle, $row);
            $count++;
        }

        fclose($handle);

        $this->info("Exported {$count} rows to {$path}");
        return Command::SUCCESS;
    }
}
