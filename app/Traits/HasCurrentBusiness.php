<?php

namespace App\Traits;

use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
            // Кешируем бизнес на 3 минуты
            $business = Cache::remember("business_{$businessId}", 180, function () use ($businessId) {
                return Business::find($businessId);
            });

            // Verify user has access to this business
            if ($business) {
                $userBusinesses = $this->getUserBusinesses($user);
                if ($userBusinesses->contains($business->id)) {
                    return $business;
                }
            }
            // If business from session is invalid, clear it
            Session::forget('current_business_id');
        }

        // Fallback to first business (MVP approach)
        $userBusinesses = $this->getUserBusinesses($user);

        return $userBusinesses->first();
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

        // Получаем pivot данные БЕЗ кэша (они небольшие и редко запрашиваются)
        $pivotData = DB::table('business_user')
            ->where('user_id', $user->id)
            ->where('business_id', $business->id)
            ->first();

        if (! $pivotData) {
            return null;
        }

        $role = null;

        // First try to get role by role_id
        if ($pivotData->role_id) {
            $role = \App\Models\BusinessRole::getCached($pivotData->role_id);
            if ($role) {
                return $role;
            }
        }

        // Fallback: try to get role by slug (for backward compatibility)
        if ($pivotData->role) {
            $role = \App\Models\BusinessRole::getCachedBySlug($pivotData->role);
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
     * Get user businesses with caching.
     * Кэшируем на короткий период (3 минуты) для критичных данных.
     */
    protected function getUserBusinesses($user)
    {
        return Cache::remember("user_businesses_{$user->id}", 180, function () use ($user) {
            return $user->businesses;
        });
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
        $userBusinesses = $this->getUserBusinesses($user);
        if ($userBusinesses->contains($business->id)) {
            Session::put('current_business_id', $business->id);
        }
    }

    /**
     * Clear user businesses cache.
     * Call this method when user's business relations change.
     */
    protected function clearUserBusinessesCache($userId = null): void
    {
        $userId = $userId ?? Auth::id();

        if (! $userId) {
            return;
        }

        Cache::forget("user_businesses_{$userId}");
    }
}
