<?php

namespace App\Notifications;

use App\Models\VendorOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class VendorAcceptedCounter extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public VendorOffer $offer)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $product = $this->offer->product;
        return [
            'type' => 'vendor_accepted_counter',
            'offer_id' => $this->offer->getKey(),
            'product_id' => $this->offer->product_id,
            'product_name' => $product?->name,
            'accepted_price' => (float) ($this->offer->fisherman_counter_price ?? $this->offer->offered_price),
            'quantity' => (int) $this->offer->quantity,
            'status' => $this->offer->status,
            'link' => '/fisherman/offers?status=accepted',
        ];
    }
}
