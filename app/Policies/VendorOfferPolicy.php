<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VendorOffer;
use Illuminate\Auth\Access\Response;

class VendorOfferPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VendorOffer $vendorOffer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VendorOffer $vendorOffer): bool
    {
        return false;
    }

    /**
     * Determine if the fisherman can respond to the offer.
     */
    public function respond(User $user, VendorOffer $offer): bool
    {
        return $user->id === $offer->fisherman_id;
    }
}
