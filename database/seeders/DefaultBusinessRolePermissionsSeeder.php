<?php

namespace Database\Seeders;

use App\Models\BusinessRole;
use App\Models\BusinessRolePermission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class DefaultBusinessRolePermissionsSeeder extends Seeder
{
    /**
     * Назначение прав по умолчанию для ролей бизнеса (owner, admin, master).
     */
    public function run(): void
    {
        $allPermissions = Permission::pluck('name')->toArray();

        // Только права client.* (panel.* — только для админ-панели)
        $clientPermissions = array_filter($allPermissions, function ($permission) {
            return str_starts_with($permission, 'client.')
                && $permission !== 'client.access';
        });

        // Владелец: все права client.* и управление бизнесом
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
                'client.online_booking.manage',
                'client.business.users.view',
                'client.business.users.create',
                'client.business.users.update',
                'client.business.users.delete',
                'client.business.roles.manage',
                'client.subscription.view',
                'client.subscription.manage',
            ]
        );

        $ownerPermissions = array_unique($ownerPermissions);

        // Админ: ограниченный доступ (без удаления, без analytics/telegram/business.users/business.roles)
        $adminPermissions = array_filter(array_values($clientPermissions), function ($permission) {
            return ! str_ends_with($permission, '.delete')
                && $permission !== 'client.businesses.update'
                && $permission !== 'client.analytics.view'
                && $permission !== 'client.telegram.manage'
                && $permission !== 'client.online_booking.manage'
                && ! str_starts_with($permission, 'client.business.users.')
                && $permission !== 'client.business.roles.manage';
        });

        $adminPermissions[] = 'client.subscription.view';
        $adminPermissions = array_unique(array_values($adminPermissions));

        // Мастер: только свои записи/клиенты и создание
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
            $currentPermissions = BusinessRolePermission::where('role_id', $role->id)
                ->pluck('permission')
                ->toArray();

            $permissionsToRemove = array_diff($currentPermissions, $permissions);
            if (! empty($permissionsToRemove)) {
                BusinessRolePermission::where('role_id', $role->id)
                    ->whereIn('permission', $permissionsToRemove)
                    ->delete();
            }

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
    }
}
