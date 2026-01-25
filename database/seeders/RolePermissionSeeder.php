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
            'panel.users.view' => 'Просмотр списка пользователей',
            'panel.users.create' => 'Создание новых пользователей',
            'panel.users.update' => 'Редактирование пользователей',
            'panel.users.delete' => 'Удаление пользователей',

            // Роли
            'panel.roles.view' => 'Просмотр списка ролей',
            'panel.roles.create' => 'Создание новых ролей',
            'panel.roles.update' => 'Редактирование ролей',
            'panel.roles.delete' => 'Удаление ролей',

            // Права доступа
            'panel.permissions.view' => 'Просмотр списка прав доступа',
            'panel.permissions.create' => 'Создание новых прав доступа',
            'panel.permissions.update' => 'Редактирование прав доступа',
            'panel.permissions.delete' => 'Удаление прав доступа',

            // Бизнесы
            'client.businesses.view' => 'Просмотр списка бизнесов',
            'client.businesses.create' => 'Создание новых бизнесов',
            'client.businesses.update' => 'Редактирование бизнесов',
            'client.businesses.delete' => 'Удаление бизнесов',

            // Записи
            'client.appointments.view' => 'Просмотр списка записей',
            'client.appointments.create' => 'Создание новых записей',
            'client.appointments.update' => 'Редактирование записей',
            'client.appointments.delete' => 'Удаление записей',
            'client.appointments.export' => 'Экспорт записей',

            // Клиенты
            'client.clients.view' => 'Просмотр списка клиентов',
            'client.clients.create' => 'Создание новых клиентов',
            'client.clients.update' => 'Редактирование клиентов',
            'client.clients.delete' => 'Удаление клиентов',
            'client.clients.export' => 'Экспорт клиентов',

            // Услуги
            'client.services.view' => 'Просмотр списка услуг',
            'client.services.create' => 'Создание новых услуг',
            'client.services.update' => 'Редактирование услуг',
            'client.services.delete' => 'Удаление услуг',

            // Локации
            'client.locations.view' => 'Просмотр списка локаций',
            'client.locations.create' => 'Создание новых локаций',
            'client.locations.update' => 'Редактирование локаций',
            'client.locations.delete' => 'Удаление локаций',

            // Мастера
            'client.masters.view' => 'Просмотр списка мастеров',
            'client.masters.create' => 'Создание новых мастеров',
            'client.masters.update' => 'Редактирование мастеров',
            'client.masters.delete' => 'Удаление мастеров',

            // Аналитика
            'client.analytics.view' => 'Просмотр аналитики',

            // Поддержка
            'panel.support.view' => 'Доступ к разделу поддержки',

            // Тикеты
            'client.tickets.view' => 'Просмотр тикетов',
            'client.tickets.create' => 'Создание тикетов',
            'client.tickets.update' => 'Ответ на тикеты',
            'panel.tickets.settings' => 'Настройка тикет-системы',
            'panel.tickets.categories.manage' => 'Управление категориями тикетов',

            // Telegram
            'client.telegram.manage' => 'Управление Telegram ботом',
            'panel.telegram.manage' => 'Управление Telegram ботами (админ-панель)',

            // Тарифы
            'panel.plans.view' => 'Просмотр тарифов',
            'panel.plans.create' => 'Создание тарифов',
            'panel.plans.update' => 'Редактирование тарифов',
            'panel.plans.delete' => 'Удаление тарифов',

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

            // Управление пользователями бизнеса (клиентская часть)
            'client.business.users.view' => 'Просмотр пользователей бизнеса',
            'client.business.users.create' => 'Добавление пользователей в бизнес',
            'client.business.users.update' => 'Изменение роли пользователя в бизнесе',
            'client.business.users.delete' => 'Удаление пользователя из бизнеса',
            'client.business.roles.manage' => 'Управление правами ролей бизнеса (настройка прав для ролей в своем бизнесе)',

            // Подписки
            'client.subscription.view' => 'Просмотр информации о подписке',
            'client.subscription.manage' => 'Управление подпиской',
            'client.subscription.pay' => 'Оплата подписки',

            // Платежи (админ-панель)
            'panel.payments.settings' => 'Настройки bePaid (только админ)',
            'panel.payments.view' => 'Просмотр платежей/инвойсов в админ панели',
            'panel.payments.manage' => 'Управление платежами в админ панели (возвраты и т.д.)',

            // Управление базовыми правами ролей бизнеса (админ-панель)
            'panel.business.roles.manage' => 'Управление базовыми правами ролей бизнеса (админ-панель)',

            // Системные уведомления
            'panel.notifications.view' => 'Просмотр системных уведомлений (админ-панель)',
            'client.notifications.view' => 'Просмотр системных уведомлений (клиентская часть)',

            // Рассылки (админ-панель)
            'panel.broadcasts.send' => 'Рассылка уведомлений (админ-панель)',
        ];

        foreach ($permissions as $name => $description) {
            Permission::updateOrCreate(
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
            'panel.tickets.categories.manage',

            // Платежи - просмотр (админ-панель)
            'panel.payments.view',

            // Управление базовыми правами ролей бизнеса
            'panel.business.roles.manage',

            // Системные уведомления
            'panel.notifications.view',

            // Рассылки
            'panel.broadcasts.send',

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
            'panel.support.view',
            'panel.tickets.view',
            'panel.tickets.update',
            'panel.tickets.assign',
            'panel.notifications.view',
            'panel.access',
        ];
        foreach ($supportPermissions as $permission) {
            $supportRole->givePermissionTo($permission);
        }

        // Создание роли Пользователь (для обычных пользователей)
        // Теперь роль user имеет только client.access - конкретные права проверяются через роль в бизнесе
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userPermissions = [
            'client.access',
            'client.subscription.pay', // Оплата подписки
        ];
        foreach ($userPermissions as $permission) {
            $userRole->givePermissionTo($permission);
        }
    }
}
