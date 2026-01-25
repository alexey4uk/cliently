<?php

namespace App\Traits;

use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

trait HasCurrentBusiness
{
    /**
     * Get the current business for the authenticated user.
     * Checks session first, then falls back to the first business.
     */
    protected function getCurrentBusiness(): ?Business
    {
        $user = Auth::user();

        if (! $user) {
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
     * Get the current user's role model in the current business.
     */
    protected function getCurrentBusinessRole(): ?\App\Models\BusinessRole
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return null;
        }

        $user = Auth::user();

        if (! $user) {
            return null;
        }

        // Always query pivot directly from DB for reliability
        $pivotData = DB::table('business_user')
            ->where('user_id', $user->id)
            ->where('business_id', $business->id)
            ->first();

        if (! $pivotData) {
            return null;
        }

        // First try to get role by role_id
        if ($pivotData->role_id) {
            $role = \App\Models\BusinessRole::find($pivotData->role_id);
            if ($role) {
                return $role;
            }
        }

        // Fallback: try to get role by slug (for backward compatibility)
        if ($pivotData->role) {
            $role = \App\Models\BusinessRole::where('slug', $pivotData->role)->first();
            if ($role) {
                // Update role_id for future use
                DB::table('business_user')
                    ->where('user_id', $user->id)
                    ->where('business_id', $business->id)
                    ->update(['role_id' => $role->id]);

                return $role;
            }
        }

        return null;
    }

    /**
     * Set the current business in session (for future multi-business support).
     */
    protected function setCurrentBusiness(Business $business): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        // Verify user has access to this business
        $user->load('businesses');
        if ($user->businesses->contains($business->id)) {
            Session::put('current_business_id', $business->id);
        }
    }
}
