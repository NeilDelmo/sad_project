<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VendorInventory;
use Illuminate\Auth\Access\Response;

class VendorInventoryPolicy
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
    public function view(User $user, VendorInventory $vendorInventory): bool
    {
        return $user->id === $vendorInventory->vendor_id;
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
    public function update(User $user, VendorInventory $vendorInventory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VendorInventory $vendorInventory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VendorInventory $vendorInventory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VendorInventory $vendorInventory): bool
    {
        return false;
    }
}
