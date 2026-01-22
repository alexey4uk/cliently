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

            // Права доступа
            'permissions.view' => 'Просмотр списка прав доступа',
            'permissions.create' => 'Создание новых прав доступа',
            'permissions.update' => 'Редактирование прав доступа',
            'permissions.delete' => 'Удаление прав доступа',

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

            // Локации
            'locations.view' => 'Просмотр списка локаций',
            'locations.create' => 'Создание новых локаций',
            'locations.update' => 'Редактирование локаций',
            'locations.delete' => 'Удаление локаций',

            // Мастера
            'masters.view' => 'Просмотр списка мастеров',
            'masters.create' => 'Создание новых мастеров',
            'masters.update' => 'Редактирование мастеров',
            'masters.delete' => 'Удаление мастеров',

            // Аналитика
            'analytics.view' => 'Просмотр аналитики',

            // Поддержка
            'support.view' => 'Доступ к разделу поддержки',

            // Тикеты
            'tickets.view' => 'Просмотр тикетов',
            'tickets.create' => 'Создание тикетов',
            'tickets.update' => 'Редактирование тикетов',
            'tickets.delete' => 'Удаление тикетов',
            'tickets.assign' => 'Назначение тикетов',
            'tickets.settings' => 'Настройка тикет-системы',
            'tickets.categories.manage' => 'Управление категориями тикетов',

            // Telegram
            'telegram.manage' => 'Управление Telegram ботом',

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
        // Админ имеет все права доступа
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        foreach ($permissions as $name => $description) {
            $adminRole->givePermissionTo($name);
        }

        // Создание роли Менеджер
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerPermissions = [
            // Бизнесы - только просмотр
            'businesses.view',
            
            // Записи - полный доступ
            'appointments.view',
            'appointments.update',
            'appointments.delete',
            
            // Клиенты - полный доступ
            'clients.view',
            'clients.create',
            'clients.update',
            'clients.delete',
            
            // Услуги - только просмотр и редактирование (создание в клиентской части)
            'services.view',
            'services.update',
            'services.delete',
            
            // Локации - только просмотр и редактирование (создание в клиентской части)
            'locations.view',
            'locations.update',
            'locations.delete',
            
            // Мастера - только просмотр и редактирование (создание в клиентской части)
            'masters.view',
            'masters.update',
            'masters.delete',
            
            // Аналитика
            'analytics.view',
            
            // Тикеты - полный доступ
            'tickets.view',
            'tickets.create',
            'tickets.update',
            'tickets.delete',
            'tickets.assign',
            'tickets.categories.manage',
            
            // Доступ к панели
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
            'tickets.view',
            'tickets.create',
            'tickets.update',
            'tickets.assign',
            'panel.access',
        ];
        foreach ($supportPermissions as $permission) {
            $supportRole->givePermissionTo($permission);
        }

        // Создание роли Пользователь (для обычных пользователей)
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userPermissions = [
            'client.access',
            'tickets.view',
            'tickets.create',
        ];
        foreach ($userPermissions as $permission) {
            $userRole->givePermissionTo($permission);
        }
    }
}
