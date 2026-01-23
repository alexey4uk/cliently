<?php

namespace Database\Seeders;

use App\Models\BusinessRolePermission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class DefaultBusinessRolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all permissions from Spatie Permission
        $allPermissions = Permission::pluck('name')->toArray();

        // Filter out panel.* permissions (those are for admin panel only)
        $clientPermissions = array_filter($allPermissions, function ($permission) {
            return !str_starts_with($permission, 'panel.')
                && $permission !== 'client.access'
                && $permission !== 'panel.access';
        });

        // Owner: all client-side permissions + wildcards and business management
        $ownerPermissions = array_merge(
            array_values($clientPermissions),
            [
                'clients.*',
                'appointments.*',
                'services.*',
                'locations.*',
                'masters.*',
                'businesses.*',
                'analytics.view',
                'telegram.manage',
                'business.users.view',
                'business.users.create',
                'business.users.update',
                'business.users.delete',
                'business.roles.manage',
                'subscription.view',
                'subscription.manage',
            ]
        );

        // Убираем дубликаты
        $ownerPermissions = array_unique($ownerPermissions);

        // Admin: limited access (no delete, no businesses.update, no analytics, no telegram, no business.users.*, no business.roles.manage)
        $adminPermissions = array_filter(array_values($clientPermissions), function ($permission) {
            return !str_ends_with($permission, '.delete')
                && $permission !== 'businesses.update'
                && $permission !== 'analytics.view'
                && $permission !== 'telegram.manage'
                && !str_starts_with($permission, 'business.users.')
                && $permission !== 'business.roles.manage';
        });

        // Add subscription view permission for admin
        $adminPermissions[] = 'subscription.view';

        $adminPermissions = array_values($adminPermissions);

        // Master: only view and create
        $masterPermissions = [
            'clients.view',
            'appointments.view',
            'appointments.create',
            'services.view',
            'locations.view',
            'masters.view',
        ];

        // Seed owner permissions
        foreach ($ownerPermissions as $permission) {
            BusinessRolePermission::updateOrCreate(
                [
                    'business_id' => null,
                    'role' => 'owner',
                    'permission' => $permission,
                ],
                [
                    'granted' => true,
                ]
            );
        }

        // Seed admin permissions
        foreach ($adminPermissions as $permission) {
            BusinessRolePermission::updateOrCreate(
                [
                    'business_id' => null,
                    'role' => 'admin',
                    'permission' => $permission,
                ],
                [
                    'granted' => true,
                ]
            );
        }

        // Seed master permissions
        foreach ($masterPermissions as $permission) {
            BusinessRolePermission::updateOrCreate(
                [
                    'business_id' => null,
                    'role' => 'master',
                    'permission' => $permission,
                ],
                [
                    'granted' => true,
                ]
            );
        }
    }
}
