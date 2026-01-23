<?php

namespace App\Traits;

use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

trait HasCurrentBusiness
{
    /**
     * Get the current business for the authenticated user.
     * Checks session first, then falls back to the first business.
     *
     * @return Business|null
     */
    protected function getCurrentBusiness(): ?Business
    {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }

        // Check session for current business (for future multi-business support)
        $businessId = Session::get('current_business_id');
        
        if ($businessId) {
            $business = Business::find($businessId);
            // Verify user has access to this business
            if ($business) {
                $user->load('businesses');
                if ($user->businesses->contains($business->id)) {
                    return $business;
                }
            }
            // If business from session is invalid, clear it
            Session::forget('current_business_id');
        }

        // Fallback to first business (MVP approach)
        $user->load('businesses');
        return $user->businesses->first();
    }

    /**
     * Get the current user's role in the current business.
     *
     * @return string|null
     */
    protected function getCurrentBusinessRole(): ?string
    {
        $business = $this->getCurrentBusiness();
        
        if (!$business) {
            return null;
        }

        $user = Auth::user();
        $pivot = $user->businesses()
            ->where('business_id', $business->id)
            ->first();

        return $pivot?->pivot->role;
    }

    /**
     * Set the current business in session (for future multi-business support).
     *
     * @param Business $business
     * @return void
     */
    protected function setCurrentBusiness(Business $business): void
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }
        
        // Verify user has access to this business
        $user->load('businesses');
        if ($user->businesses->contains($business->id)) {
            Session::put('current_business_id', $business->id);
        }
    }
}
