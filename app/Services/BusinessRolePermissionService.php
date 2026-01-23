<?php

namespace App\Services;

use App\Models\BusinessRolePermission;

class BusinessRolePermissionService
{
    /**
     * Get default permissions for a role (from DB, where business_id = NULL).
     *
     * @param string $role
     * @return array
     */
    public function getDefaultPermissionsForRole(string $role): array
    {
        return BusinessRolePermission::whereNull('business_id')
            ->where('role', $role)
            ->where('granted', true)
            ->pluck('permission')
            ->toArray();
    }

    /**
     * Get available roles from default permissions (business_id = NULL).
     */
    public function getAvailableRoles(bool $includeOwner = true): array
    {
        $roles = BusinessRolePermission::whereNull('business_id')
            ->distinct()
            ->orderBy('role')
            ->pluck('role')
            ->toArray();

        if (! $includeOwner) {
            $roles = array_values(array_filter($roles, fn($role) => $role !== 'owner'));
        }

        return $roles;
    }

    /**
     * Get permissions for a role in a specific business (with overrides).
     *
     * @param int|null $businessId
     * @param string $role
     * @return array
     */
    public function getPermissionsForRole(?int $businessId, string $role): array
    {
        // Base permissions (business_id = NULL)
        $defaultPermissions = $this->getDefaultPermissionsForRole($role);

        // If no business - return only base permissions
        if (!$businessId) {
            return $defaultPermissions;
        }

        // Overrides for specific business
        $overrides = BusinessRolePermission::where('business_id', $businessId)
            ->where('role', $role)
            ->get()
            ->keyBy('permission');

        // Apply overrides
        foreach ($overrides as $permission => $override) {
            if ($override->granted) {
                // Add permission if it doesn't exist
                if (!in_array($permission, $defaultPermissions)) {
                    $defaultPermissions[] = $permission;
                }
            } else {
                // Remove permission from granted list
                // Note: Explicit denial (granted = false) is handled separately
                // in hasPermission() method and has HIGHER priority than wildcards.
                // This removal only affects the granted permissions list.
                $defaultPermissions = array_filter($defaultPermissions, fn($p) => $p !== $permission);
            }
        }

        return array_values($defaultPermissions);
    }

    /**
     * Get explicitly denied permissions for a role.
     * These are permissions with granted = false that override wildcards.
     *
     * @param int|null $businessId
     * @param string $role
     * @return array Array of denied permission names
     */
    public function getDeniedPermissions(?int $businessId, string $role): array
    {
        $denied = [];
        
        // Get denied permissions from defaults (business_id = NULL)
        $defaultDenied = BusinessRolePermission::whereNull('business_id')
            ->where('role', $role)
            ->where('granted', false)
            ->pluck('permission')
            ->toArray();
        
        $denied = array_merge($denied, $defaultDenied);
        
        // Get denied permissions for specific business
        if ($businessId) {
            $businessDenied = BusinessRolePermission::where('business_id', $businessId)
                ->where('role', $role)
                ->where('granted', false)
                ->pluck('permission')
                ->toArray();
            
            $denied = array_merge($denied, $businessDenied);
        }
        
        return array_unique($denied);
    }

    /**
     * Check if role has permission.
     * 
     * Priority order:
     * 1. Explicit deny (granted = false) - HIGHEST PRIORITY
     * 2. Explicit grant (direct permission or granted = true)
     * 3. Wildcard permission (appointments.*)
     *
     * @param int|null $businessId
     * @param string $role
     * @param string $permission
     * @return bool
     */
    public function hasPermission(?int $businessId, string $role, string $permission): bool
    {
        // First check: Is permission explicitly denied?
        $deniedPermissions = $this->getDeniedPermissions($businessId, $role);
        if ($this->checkPermission($deniedPermissions, $permission)) {
            return false; // Explicitly denied, even if covered by wildcard
        }
        
        // Second check: Does role have permission (direct or via wildcard)?
        $permissions = $this->getPermissionsForRole($businessId, $role);
        return $this->checkPermission($permissions, $permission);
    }

    /**
     * Check if role has permission to view own data only.
     * If user has both .view and .view.own, .view takes precedence (can see all).
     *
     * @param int|null $businessId
     * @param string $role
     * @param string $basePermission Base permission like 'appointments.view'
     * @return bool Returns true if has .own permission, false if has full permission or no permission
     */
    public function hasOwnDataPermission(?int $businessId, string $role, string $basePermission): bool
    {
        $permissions = $this->getPermissionsForRole($businessId, $role);
        
        // If user has full permission (e.g., appointments.view), they can see all data
        if ($this->checkPermission($permissions, $basePermission)) {
            return false; // Not restricted to own data
        }
        
        // Check if user has .own permission (e.g., appointments.view.own)
        $ownPermission = $basePermission . '.own';
        return $this->checkPermission($permissions, $ownPermission);
    }

    /**
     * Check permission with wildcards support.
     * 
     * Note: Wildcards like "appointments.*" match "appointments.view", "appointments.create", etc.
     * but do NOT match "appointments.view.own" (rights with .own suffix are excluded from wildcards).
     *
     * @param array $permissions
     * @param string $permission
     * @return bool
     */
    private function checkPermission(array $permissions, string $permission): bool
    {
        // Direct match
        if (in_array($permission, $permissions)) {
            return true;
        }

        // Check wildcards (clients.* includes clients.view, clients.create, etc.)
        // But excludes rights with .own suffix (e.g., clients.view.own is NOT included in clients.*)
        foreach ($permissions as $perm) {
            if (str_ends_with($perm, '.*')) {
                $prefix = str_replace('.*', '', $perm);
                // Check if permission starts with prefix
                if (str_starts_with($permission, $prefix . '.')) {
                    // Exclude .own permissions from wildcard matching
                    // e.g., appointments.* should NOT match appointments.view.own
                    if (!str_ends_with($permission, '.own')) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
