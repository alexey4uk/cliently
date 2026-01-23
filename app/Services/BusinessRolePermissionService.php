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
                // Remove permission
                $defaultPermissions = array_filter($defaultPermissions, fn($p) => $p !== $permission);
            }
        }

        return array_values($defaultPermissions);
    }

    /**
     * Check if role has permission.
     *
     * @param int|null $businessId
     * @param string $role
     * @param string $permission
     * @return bool
     */
    public function hasPermission(?int $businessId, string $role, string $permission): bool
    {
        $permissions = $this->getPermissionsForRole($businessId, $role);
        // Check with wildcards (clients.*)
        return $this->checkPermission($permissions, $permission);
    }

    /**
     * Check permission with wildcards support.
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
        foreach ($permissions as $perm) {
            if (str_ends_with($perm, '.*')) {
                $prefix = str_replace('.*', '', $perm);
                if (str_starts_with($permission, $prefix . '.')) {
                    return true;
                }
            }
        }

        return false;
    }
}
