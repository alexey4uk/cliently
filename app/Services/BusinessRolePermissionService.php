<?php

namespace App\Services;

use App\Models\BusinessRolePermission;

class BusinessRolePermissionService
{
    /**
     * Get available roles (global list).
     */
    public function getAvailableRoles(bool $includeOwner = true)
    {
        $query = \App\Models\BusinessRole::query()->orderBy('name');

        if (! $includeOwner) {
            $query->where('slug', '!=', 'owner');
        }

        return $query->get();
    }

    /**
     * Get permissions for a role in a specific business (with overrides).
     *
     * @param int $roleId
     * @return array
     */
    public function getPermissionsForRole(int $roleId): array
    {
        return BusinessRolePermission::where('role_id', $roleId)
            ->where('granted', true)
            ->pluck('permission')
            ->toArray();
    }

    /**
     * Get explicitly denied permissions for a role.
     * These are permissions with granted = false that override wildcards.
     *
     * @param int $roleId
     * @return array Array of denied permission names
     */
    public function getDeniedPermissions(int $roleId): array
    {
        return BusinessRolePermission::where('role_id', $roleId)
            ->where('granted', false)
            ->pluck('permission')
            ->toArray();
    }

    /**
     * Check if role has permission.
     * 
     * Priority order:
     * 1. Owner role - has all permissions automatically
     * 2. Explicit deny (granted = false) - HIGHEST PRIORITY
     * 3. Explicit grant (direct permission or granted = true)
     * 4. Wildcard permission (appointments.*)
     * 5. .own permission (if checking base permission like clients.view, also check clients.view.own)
     *
     * @param int $roleId
     * @param string $permission
     * @return bool
     */
    public function hasPermission(int $roleId, string $permission): bool
    {
        // First check: Is this the owner role? Owner has all permissions
        $role = \App\Models\BusinessRole::find($roleId);
        if ($role && $role->slug === 'owner') {
            return true;
        }
        
        // Second check: Is permission explicitly denied?
        $deniedPermissions = $this->getDeniedPermissions($roleId);
        if ($this->checkPermission($deniedPermissions, $permission)) {
            return false; // Explicitly denied, even if covered by wildcard
        }
        
        // Third check: Does role have permission (direct or via wildcard)?
        $permissions = $this->getPermissionsForRole($roleId);
        if ($this->checkPermission($permissions, $permission)) {
            return true;
        }
        
        // Fourth check: If checking base permission (like clients.view), also check .own variant
        // This allows users with clients.view.own to access clients.view routes
        // (the controller will filter data appropriately)
        if (!str_ends_with($permission, '.own')) {
            $ownPermission = $permission . '.own';
            if ($this->checkPermission($permissions, $ownPermission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if role has permission to view own data only.
     * If user has both .view and .view.own, .view takes precedence (can see all).
     *
     * @param int $roleId
     * @param string $basePermission Base permission like 'appointments.view'
     * @return bool Returns true if has .own permission, false if has full permission or no permission
     */
    public function hasOwnDataPermission(int $roleId, string $basePermission): bool
    {
        // Owner role can see all data, not restricted to own
        $role = \App\Models\BusinessRole::find($roleId);
        if ($role && $role->slug === 'owner') {
            return false;
        }
        
        $permissions = $this->getPermissionsForRole($roleId);
        
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
