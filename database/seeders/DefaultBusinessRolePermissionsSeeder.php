<?php

namespace Database\Seeders;

use App\Models\BusinessRole;
use App\Models\BusinessRolePermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        // Keep only client.* permissions (panel.* are admin-only)
        $clientPermissions = array_filter($allPermissions, function ($permission) {
            return str_starts_with($permission, 'client.')
                && $permission !== 'client.access';
        });

        // Owner: all client-side permissions + wildcards and business management
        $ownerPermissions = array_merge(
            array_values($clientPermissions),
            [
                'client.clients.*',
                'client.appointments.*',
                'client.services.*',
                'client.locations.*',
                'client.masters.*',
                'client.businesses.*',
                'client.analytics.view',
                'client.telegram.manage',
                'client.business.users.view',
                'client.business.users.create',
                'client.business.users.update',
                'client.business.users.delete',
                'client.business.roles.manage',
                'client.subscription.view',
                'client.subscription.manage',
            ]
        );

        // Убираем дубликаты
        $ownerPermissions = array_unique($ownerPermissions);

        // Admin: limited access (no delete, no businesses.update, no analytics, no telegram, no business.users.*, no business.roles.manage)
        $adminPermissions = array_filter(array_values($clientPermissions), function ($permission) {
            return ! str_ends_with($permission, '.delete')
                && $permission !== 'client.businesses.update'
                && $permission !== 'client.analytics.view'
                && $permission !== 'client.telegram.manage'
                && ! str_starts_with($permission, 'client.business.users.')
                && $permission !== 'client.business.roles.manage';
        });

        // Add subscription view permission for admin
        $adminPermissions[] = 'client.subscription.view';

        // Remove duplicates
        $adminPermissions = array_unique(array_values($adminPermissions));

        // Master: only view own data and create
        $masterPermissions = [
            'client.clients.view.own', // Только клиенты с записями у этого мастера
            'client.appointments.view.own', // Только записи этого мастера
            'client.appointments.create',
            'client.services.view',
            'client.locations.view',
            'client.masters.view',
        ];

        $ownerRole = BusinessRole::updateOrCreate(
            ['slug' => 'owner'],
            ['name' => 'Владелец', 'description' => 'Полный доступ', 'is_system' => true, 'owner_id' => null]
        );
        $adminRole = BusinessRole::updateOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Администратор', 'description' => 'Управление бизнесом', 'is_system' => true, 'owner_id' => null]
        );
        $masterRole = BusinessRole::updateOrCreate(
            ['slug' => 'master'],
            ['name' => 'Мастер', 'description' => 'Работа с клиентами', 'is_system' => true, 'owner_id' => null]
        );

        $seedPermissions = function (BusinessRole $role, array $permissions): void {
            // Get current permissions for this role
            $currentPermissions = BusinessRolePermission::where('role_id', $role->id)
                ->pluck('permission')
                ->toArray();

            // Remove permissions that are no longer in the list
            $permissionsToRemove = array_diff($currentPermissions, $permissions);
            if (! empty($permissionsToRemove)) {
                BusinessRolePermission::where('role_id', $role->id)
                    ->whereIn('permission', $permissionsToRemove)
                    ->delete();
            }

            // Add or update permissions
            foreach ($permissions as $permission) {
                BusinessRolePermission::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'permission' => $permission,
                    ],
                    [
                        'granted' => true,
                    ]
                );
            }
        };

        $seedPermissions($ownerRole, $ownerPermissions);
        $seedPermissions($adminRole, $adminPermissions);
        $seedPermissions($masterRole, $masterPermissions);

        // Backfill role_id for existing records using role slug
        DB::table('business_user')
            ->whereNull('role_id')
            ->whereNotNull('role')
            ->update(['role_id' => DB::raw('(
                select id from business_roles where slug = business_user.role limit 1
            )')]);

        DB::table('business_user_invitations')
            ->whereNull('role_id')
            ->whereNotNull('role')
            ->update(['role_id' => DB::raw('(
                select id from business_roles where slug = business_user_invitations.role limit 1
            )')]);
    }
}
