<?php

namespace App\Notifications;

use App\Models\VendorOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class VendorOfferAccepted extends Notification implements ShouldQueue
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
            'type' => 'vendor_offer_accepted',
            'offer_id' => $this->offer->getKey(),
            'product_id' => $this->offer->product_id,
            'product_name' => $product?->name,
            'quantity' => (int) $this->offer->quantity,
            'final_price' => (float) ($this->offer->fisherman_counter_price ?? $this->offer->offered_price),
            'status' => $this->offer->status,
        ];
    }
}
<?php

namespace App\Notifications;

use App\Models\VendorOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VendorOfferAccepted extends Notification
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
            'type' => 'vendor_offer_accepted',
            'offer_id' => $this->offer->id,
            'product_id' => $this->offer->product_id,
            'product_name' => $this->offer->product?->name,
            'quantity' => (int) $this->offer->quantity,
            'final_price' => (float) ($this->offer->fisherman_counter_price ?? $this->offer->offered_price),
            'message' => 'Your offer was accepted by the fisherman.',
            'status' => $this->offer->status,
        ];
    }
}
