<?php

namespace App\Console\Commands;

use App\Models\VendorOffer;
use App\Notifications\OfferExpired;
use Illuminate\Console\Command;

class ExpireOffers extends Command
{
    protected $signature = 'offers:expire';
    protected $description = 'Mark expired offers as expired and notify both parties';

    public function handle(): int
    {
        $expired = VendorOffer::whereIn('status', ['pending','countered'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->with(['vendor','fisherman','product'])
            ->get();

        foreach ($expired as $offer) {
            $offer->update(['status' => 'expired']);
            // notify vendor and fisherman
            if ($offer->vendor) {
                $offer->vendor->notify(new OfferExpired($offer));
            }
            if ($offer->fisherman) {
                $offer->fisherman->notify(new OfferExpired($offer));
            }
        }

        $this->info('Expired offers processed: '.$expired->count());
        return 0;
    }
}
