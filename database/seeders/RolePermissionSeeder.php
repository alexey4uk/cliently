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
            'appointments.export' => 'Экспорт записей',

            // Клиенты
            'clients.view' => 'Просмотр списка клиентов',
            'clients.create' => 'Создание новых клиентов',
            'clients.update' => 'Редактирование клиентов',
            'clients.delete' => 'Удаление клиентов',
            'clients.export' => 'Экспорт клиентов',

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

            // Тарифы
            'plans.view' => 'Просмотр тарифов',
            'plans.create' => 'Создание тарифов',
            'plans.update' => 'Редактирование тарифов',
            'plans.delete' => 'Удаление тарифов',

            // Доступ к админке
            'panel.access' => 'Доступ к админ-панели',

            // Доступ к клиентской части
            'client.access' => 'Доступ к клиентской части',

            // Админские права (с префиксом panel.) - для работы в админ-панели со всеми данными
            'panel.businesses.view' => 'Просмотр всех бизнесов (админ-панель)',
            'panel.businesses.update' => 'Редактирование бизнесов (админ-панель)',
            'panel.businesses.delete' => 'Удаление бизнесов (админ-панель)',

            'panel.appointments.view' => 'Просмотр всех записей (админ-панель)',
            'panel.appointments.update' => 'Редактирование записей (админ-панель)',
            'panel.appointments.delete' => 'Удаление записей (админ-панель)',

            'panel.clients.view' => 'Просмотр всех клиентов (админ-панель)',
            'panel.clients.create' => 'Создание клиентов (админ-панель)',
            'panel.clients.update' => 'Редактирование клиентов (админ-панель)',
            'panel.clients.delete' => 'Удаление клиентов (админ-панель)',

            'panel.services.view' => 'Просмотр всех услуг (админ-панель)',
            'panel.services.update' => 'Редактирование услуг (админ-панель)',
            'panel.services.delete' => 'Удаление услуг (админ-панель)',

            'panel.locations.view' => 'Просмотр всех локаций (админ-панель)',
            'panel.locations.update' => 'Редактирование локаций (админ-панель)',
            'panel.locations.delete' => 'Удаление локаций (админ-панель)',

            'panel.masters.view' => 'Просмотр всех мастеров (админ-панель)',
            'panel.masters.update' => 'Редактирование мастеров (админ-панель)',
            'panel.masters.delete' => 'Удаление мастеров (админ-панель)',

            'panel.analytics.view' => 'Просмотр аналитики всех бизнесов (админ-панель)',

            'panel.tickets.view' => 'Просмотр всех тикетов (админ-панель)',
            'panel.tickets.update' => 'Редактирование тикетов (админ-панель)',
            'panel.tickets.delete' => 'Удаление тикетов (админ-панель)',
            'panel.tickets.assign' => 'Назначение тикетов (админ-панель)',
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
        // Синхронизируем все права (удаляем старые и добавляем новые)
        $adminRole->syncPermissions(array_keys($permissions));

        // Создание роли Менеджер
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerPermissions = [
            // Бизнесы - только просмотр (админ-панель)
            'panel.businesses.view',
            
            // Записи - полный доступ (админ-панель)
            'panel.appointments.view',
            'panel.appointments.update',
            'panel.appointments.delete',
            
            // Клиенты - полный доступ (админ-панель)
            'panel.clients.view',
            'panel.clients.create',
            'panel.clients.update',
            'panel.clients.delete',
            
            // Услуги - только просмотр и редактирование (админ-панель)
            'panel.services.view',
            'panel.services.update',
            'panel.services.delete',
            
            // Локации - только просмотр и редактирование (админ-панель)
            'panel.locations.view',
            'panel.locations.update',
            'panel.locations.delete',
            
            // Мастера - только просмотр и редактирование (админ-панель)
            'panel.masters.view',
            'panel.masters.update',
            'panel.masters.delete',
            
            // Аналитика (админ-панель)
            'panel.analytics.view',
            
            // Тикеты - полный доступ (админ-панель)
            'panel.tickets.view',
            'panel.tickets.update',
            'panel.tickets.delete',
            'panel.tickets.assign',
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
            'panel.analytics.view',
            'support.view',
            'panel.tickets.view',
            'panel.tickets.update',
            'panel.tickets.assign',
            'panel.access',
        ];
        foreach ($supportPermissions as $permission) {
            $supportRole->givePermissionTo($permission);
        }

        // Создание роли Пользователь (для обычных пользователей)
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userPermissions = [
            'client.access',
            
            // Клиенты - полный доступ для работы в клиентской части
            'clients.view',
            'clients.create',
            'clients.update',
            'clients.delete',
            'clients.export',
            
            // Услуги - полный доступ
            'services.view',
            'services.create',
            'services.update',
            'services.delete',
            
            // Локации - полный доступ
            'locations.view',
            'locations.create',
            'locations.update',
            'locations.delete',
            
            // Мастера - полный доступ
            'masters.view',
            'masters.create',
            'masters.update',
            'masters.delete',
            
            // Записи - полный доступ
            'appointments.view',
            'appointments.create',
            'appointments.update',
            'appointments.delete',
            'appointments.export',
            
            // Бизнесы - создание и редактирование своего бизнеса
            'businesses.create',
            'businesses.update',
            
            // Аналитика
            'analytics.view',
            
            // Тикеты
            'tickets.view',
            'tickets.create',
            'tickets.update',
            'tickets.delete',
            
            // Telegram настройки
            'telegram.manage',
        ];
        foreach ($userPermissions as $permission) {
            $userRole->givePermissionTo($permission);
        }
    }
}
