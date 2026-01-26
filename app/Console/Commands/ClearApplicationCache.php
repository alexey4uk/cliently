<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\BusinessRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearApplicationCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-app 
                            {--tag= : Clear cache by tag (dashboard, businesses, roles, permissions, countries, analytics, panel_analytics, subscription, all)}
                            {--user= : Clear cache for specific user ID}
                            {--business= : Clear cache for specific business ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear application-specific cache (countries, roles, permissions, businesses, dashboard, etc.)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tag = $this->option('tag');
        $userId = $this->option('user');
        $businessId = $this->option('business');

        if ($tag) {
            return $this->clearByTag($tag, $userId, $businessId);
        }

        if ($userId) {
            return $this->clearByUser($userId);
        }

        if ($businessId) {
            return $this->clearByBusiness($businessId);
        }

        // Clear all application cache
        return $this->clearAll();
    }

    /**
     * Clear cache by tag.
     */
    protected function clearByTag(string $tag, ?string $userId, ?string $businessId): int
    {
        $tags = [];

        switch ($tag) {
            case 'dashboard':
                if ($userId) {
                    $tags[] = "user_{$userId}";
                }
                $tags[] = 'dashboard';
                break;

            case 'businesses':
                $tags[] = 'businesses';
                if ($businessId) {
                    // Clear specific business cache
                    $business = Business::find($businessId);
                    if ($business) {
                        Cache::forget("business_{$businessId}");
                        Cache::forget("business_slug_{$business->slug}");
                        if ($business->telegram_token) {
                            Cache::forget("business_telegram_token_{$business->telegram_token}");
                        }
                        Cache::forget("business_owner_{$businessId}");
                        $this->info("Business cache cleared for business ID: {$businessId} ({$business->name})");
                    } else {
                        $this->warn("Business with ID {$businessId} not found");
                    }
                } else {
                    Cache::forget('businesses_total_count');
                    Cache::forget('businesses_list_filter');
                    $this->info("Business cache cleared");
                }
                break;

            case 'roles':
                // Clear all role-related cache
                Cache::forget('business_role_owner');
                $this->clearRoleCache();
                $this->info('Roles cache cleared');
                break;

            case 'permissions':
                $this->clearPermissionsCache();
                $this->info('Permissions cache cleared');
                break;

            case 'countries':
                Cache::forget('countries_list');
                // Clear country code caches (approximate - would need to know all codes)
                $this->info('Countries cache cleared');
                break;

            case 'analytics':
                if ($businessId) {
                    $this->clearClientAnalyticsCache($businessId);
                    $this->info("Client analytics cache cleared for business ID: {$businessId}");
                } else {
                    $supportsTags = method_exists(Cache::getStore(), 'tags');
                    if ($supportsTags) {
                        Cache::tags(['analytics'])->flush();
                        $this->info('All client analytics cache cleared');
                    } else {
                        $this->warn('Please specify --business option to clear client analytics cache (tags not supported)');
                    }
                }
                break;

            case 'panel_analytics':
                if ($userId) {
                    $supportsTags = method_exists(Cache::getStore(), 'tags');
                    if ($supportsTags) {
                        Cache::tags(['panel_analytics', "user_{$userId}"])->flush();
                        $this->info("Panel analytics cache cleared for user ID: {$userId}");
                    } else {
                        // Очищаем основные ключи вручную
                        Cache::forget("panel_analytics_overview_{$userId}");
                        $this->info("Panel analytics cache cleared for user ID: {$userId} (partial)");
                    }
                } else {
                    $supportsTags = method_exists(Cache::getStore(), 'tags');
                    if ($supportsTags) {
                        Cache::tags(['panel_analytics'])->flush();
                        $this->info('All panel analytics cache cleared');
                    } else {
                        $this->warn('Please specify --user option to clear panel analytics cache (tags not supported)');
                    }
                }
                break;

            case 'subscription':
                if ($userId) {
                    $this->clearSubscriptionCache($userId);
                    $this->info("Subscription cache cleared for user ID: {$userId}");
                } else {
                    $this->warn('Please specify --user option to clear subscription cache');
                }
                break;

            case 'all':
                return $this->clearAll();

            default:
                $this->error("Unknown tag: {$tag}");
                $this->info('Available tags: dashboard, businesses, roles, permissions, countries, analytics, panel_analytics, subscription, all');
                return 1;
        }

        if (!empty($tags)) {
            Cache::tags($tags)->flush();
            $this->info("Cache cleared for tags: " . implode(', ', $tags));
        }

        return 0;
    }

    /**
     * Clear cache for specific user.
     */
    protected function clearByUser(string $userId): int
    {
        // Clear user-specific cache
        Cache::forget("user_businesses_{$userId}");
        Cache::forget("current_business_{$userId}");
        
        // Clear current business role cache for all businesses of this user
        $user = User::find($userId);
        if ($user) {
            foreach ($user->businesses as $business) {
                Cache::forget("current_business_role_{$userId}_{$business->id}");
                Cache::forget("business_user_pivot_{$userId}_{$business->id}");
            }
        }

        // Clear dashboard cache
        Cache::tags(['dashboard', "user_{$userId}"])->flush();

        // Clear subscription usage cache
        $this->clearSubscriptionCache($userId);

        $this->info("Cache cleared for user ID: {$userId}");
        return 0;
    }

    /**
     * Clear cache for specific business.
     */
    protected function clearByBusiness(string $businessId): int
    {
        // Get business to clear by slug and token
        $business = Business::find($businessId);
        
        if ($business) {
            Cache::forget("business_{$businessId}");
            Cache::forget("business_slug_{$business->slug}");
            if ($business->telegram_token) {
                Cache::forget("business_telegram_token_{$business->telegram_token}");
            }
            Cache::forget("business_owner_{$businessId}");
            
            $this->info("Cache cleared for business ID: {$businessId} ({$business->name})");
        } else {
            $this->warn("Business with ID {$businessId} not found");
        }

        return 0;
    }

    /**
     * Clear all application cache.
     */
    protected function clearAll(): int
    {
        $this->info('Clearing all application cache...');

        // Clear countries
        Cache::forget('countries_list');

        // Clear roles
        Cache::forget('business_role_owner');
        $this->clearRoleCache();

        // Clear permissions
        $this->clearPermissionsCache();

        // Clear businesses
        Cache::forget('businesses_total_count');
        Cache::forget('businesses_list_filter');
        // Clear paginated businesses (first 10 pages)
        for ($page = 1; $page <= 10; $page++) {
            for ($perPage = 10; $perPage <= 50; $perPage += 10) {
                Cache::forget("businesses_paginated_{$page}_{$perPage}");
            }
        }

        // Clear dashboard cache by tags
        $supportsTags = method_exists(Cache::getStore(), 'tags');
        if ($supportsTags) {
            Cache::tags(['dashboard'])->flush();
            Cache::tags(['analytics'])->flush();
            Cache::tags(['panel_analytics'])->flush();
        }

        // Очищаем метрики выручки
        Cache::forget('analytics_revenue_metrics_'.today()->format('Y-m-d'));
        Cache::forget('analytics_invoice_status_stats');

        // Clear pivot caches (approximate - would need to know all user/business combinations)
        // These will expire naturally

        $this->info('All application cache cleared!');
        $this->warn('Note: Some caches (like user_businesses, business_user_pivot) will expire naturally or need specific user/business IDs to clear.');

        return 0;
    }

    /**
     * Clear role cache (approximate - would need to know all role IDs).
     */
    protected function clearRoleCache(): void
    {
        // Get all roles and clear their cache
        $roles = BusinessRole::all();
        foreach ($roles as $role) {
            Cache::forget("business_role_{$role->id}");
            Cache::forget("business_role_slug_{$role->slug}");
        }
    }

    /**
     * Clear permissions cache (approximate - would need to know all role IDs).
     */
    protected function clearPermissionsCache(): void
    {
        // Get all roles and clear their permissions cache
        $roles = BusinessRole::all();
        foreach ($roles as $role) {
            Cache::forget("role_permissions_{$role->id}");
            Cache::forget("role_denied_permissions_{$role->id}");
        }
    }

    /**
     * Clear subscription usage cache for user.
     */
    protected function clearSubscriptionCache(string $userId): void
    {
        $featureKeys = [
            'max_locations',
            'max_masters',
            'max_services',
            'max_clients',
            'max_business_users',
            'max_appointments_per_month',
        ];

        foreach ($featureKeys as $featureKey) {
            // Clear current month cache
            Cache::forget("usage_{$userId}_{$featureKey}_" . now()->format('Y-m'));
            // Clear general cache
            Cache::forget("usage_{$userId}_{$featureKey}");
        }
    }

    /**
     * Clear client analytics cache for business.
     */
    protected function clearClientAnalyticsCache(string $businessId): void
    {
        $supportsTags = method_exists(Cache::getStore(), 'tags');
        if ($supportsTags) {
            Cache::tags(['analytics', "business_{$businessId}"])->flush();
        } else {
            // Очищаем основные ключи вручную
            Cache::forget("analytics_kpi_{$businessId}");
            // Остальные ключи будут очищены при следующем запросе
        }
    }
}
