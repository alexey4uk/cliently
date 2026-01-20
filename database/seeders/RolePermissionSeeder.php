<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создание прав доступа с описаниями
        $permissions = [
            // Пользователи
            'users.view' => 'Просмотр списка пользователей',
            'users.create' => 'Создание новых пользователей',
            'users.update' => 'Редактирование пользователей',
            'users.delete' => 'Удаление пользователей',

            // Роли
            'roles.view' => 'Просмотр списка ролей',
            'roles.create' => 'Создание новых ролей',
            'roles.update' => 'Редактирование ролей',
            'roles.delete' => 'Удаление ролей',

            // Бизнесы
            'businesses.view' => 'Просмотр списка бизнесов',
            'businesses.create' => 'Создание новых бизнесов',
            'businesses.update' => 'Редактирование бизнесов',
            'businesses.delete' => 'Удаление бизнесов',

            // Записи
            'appointments.view' => 'Просмотр списка записей',
            'appointments.create' => 'Создание новых записей',
            'appointments.update' => 'Редактирование записей',
            'appointments.delete' => 'Удаление записей',

            // Клиенты
            'clients.view' => 'Просмотр списка клиентов',
            'clients.create' => 'Создание новых клиентов',
            'clients.update' => 'Редактирование клиентов',
            'clients.delete' => 'Удаление клиентов',

            // Услуги
            'services.view' => 'Просмотр списка услуг',
            'services.create' => 'Создание новых услуг',
            'services.update' => 'Редактирование услуг',
            'services.delete' => 'Удаление услуг',

            // Аналитика
            'analytics.view' => 'Просмотр аналитики',

            // Поддержка
            'support.view' => 'Доступ к разделу поддержки',

            // Доступ к админке
            'panel.access' => 'Доступ к админ-панели',

            // Доступ к клиентской части
            'client.access' => 'Доступ к клиентской части',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['description' => $description]
            );
        }

        // Создание роли Админ
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        foreach ($permissions as $name => $description) {
            $adminRole->givePermissionTo($name);
        }

        // Создание роли Менеджер
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerPermissions = [
            'businesses.view',
            'appointments.view',
            'appointments.create',
            'appointments.update',
            'appointments.delete',
            'clients.view',
            'clients.create',
            'clients.update',
            'clients.delete',
            'services.view',
            'services.create',
            'services.update',
            'services.delete',
            'analytics.view',
            'panel.access',
        ];
        foreach ($managerPermissions as $permission) {
            $managerRole->givePermissionTo($permission);
        }

        // Создание роли Поддержка
        $supportRole = Role::firstOrCreate(['name' => 'support', 'guard_name' => 'web']);
        $supportPermissions = [
            'analytics.view',
            'support.view',
            'panel.access',
        ];
        foreach ($supportPermissions as $permission) {
            $supportRole->givePermissionTo($permission);
        }

        // Создание роли Пользователь (для обычных пользователей)
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userPermissions = [
            'client.access',
        ];
        foreach ($userPermissions as $permission) {
            $userRole->givePermissionTo($permission);
        }
    }
}
