<?php

namespace App\Notifications;

use App\Models\VendorOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OfferExpired extends Notification
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
            'type' => 'offer_expired',
                        'title' => 'Offer Expired',
                        'message' => sprintf('Your offer of ₱%s for %s has expired.', 
                            number_format($this->offer->offered_price, 2),
                            $product?->name
                        ),
            'offer_id' => $this->offer->getKey(),
            'product_id' => $this->offer->product_id,
            'product_name' => $product?->name,
            'quantity' => (int) $this->offer->quantity,
            'status' => 'expired',
        ];
    }
}
