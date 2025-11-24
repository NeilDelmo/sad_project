<?php

namespace App\Notifications;

use App\Models\VendorOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BiddingClosed extends Notification
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
            'type' => 'bidding_closed',
            'title' => 'Bidding Closed',
            'message' => sprintf('Bidding for %s has been closed.', $product?->name ?? 'product'),
            'offer_id' => $this->offer->getKey(),
            'product_id' => $this->offer->product_id,
            'product_name' => $product?->name,
            'quantity' => (int) $this->offer->quantity,
            'status' => 'closed',
            'link' => '/vendor/offers?status=closed',
        ];
    }
}
