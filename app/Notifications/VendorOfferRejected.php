<?php

namespace App\Notifications;

use App\Models\VendorOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class VendorOfferRejected extends Notification
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
        return [
            'type' => 'vendor_offer_rejected',
                        'title' => 'Your Offer Was Rejected',
            'offer_id' => $this->offer->getKey(),
            'product_id' => $this->offer->product_id,
            'product_name' => $this->offer->product?->name,
            'quantity' => (int) $this->offer->quantity,
            'final_price' => (float) ($this->offer->fisherman_counter_price ?? $this->offer->offered_price),
            'message' => sprintf('Your offer of ₱%s for %s was rejected by the fisherman.', 
                number_format($this->offer->offered_price, 2),
                $this->offer->product?->name
            ),
            'status' => $this->offer->status,
        ];
    }
}
