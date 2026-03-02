<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Создание прав доступа (Spatie) и ролей с описаниями.
     */
    public function run(): void
    {
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
            'client.appointments.view.own' => 'Просмотр только своих записей',
            'client.appointments.create' => 'Создание новых записей',
            'client.appointments.update' => 'Редактирование записей',
            'client.appointments.delete' => 'Удаление записей',
            'client.appointments.export' => 'Экспорт записей',

            // Клиенты
            'client.clients.view' => 'Просмотр списка клиентов',
            'client.clients.view.own' => 'Просмотр только своих клиентов (с записями у этого мастера)',
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
            'panel.tickets.categories.manage' => 'Управление категориями тикетов',

            // Telegram
            'client.telegram.manage' => 'Управление Telegram ботом',
            'panel.telegram.manage' => 'Управление Telegram ботами (админ-панель)',

            // Онлайн-запись
            'client.online_booking.manage' => 'Управление онлайн-записью (включение, ссылки, QR-коды)',

            // Тарифы
            'panel.plans.view' => 'Просмотр тарифов',
            'panel.plans.create' => 'Создание тарифов',
            'panel.plans.update' => 'Редактирование тарифов',
            'panel.plans.delete' => 'Удаление тарифов',

            // Страны (справочник)
            'panel.countries.view' => 'Просмотр списка стран',
            'panel.countries.create' => 'Создание стран',
            'panel.countries.update' => 'Редактирование стран',
            'panel.countries.delete' => 'Удаление стран',

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

            'panel.analytics.view' => 'Просмотр общей аналитики (админ-панель)',
            'panel.analytics.financial' => 'Просмотр финансовой аналитики (админ-панель)',
            'panel.analytics.general' => 'Просмотр общей статистики (админ-панель)',
            'panel.analytics.subscriptions' => 'Просмотр аналитики подписок (админ-панель)',

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

            // Подписки (админ-панель: просмотр и управление статусами/сроками)
            'panel.subscriptions.view' => 'Просмотр подписок пользователей в админ-панели',
            'panel.subscriptions.manage' => 'Управление подписками: смена статуса, продление, выдача на любой срок',

            // Платежи (админ-панель)
            'panel.payments.settings' => 'Настройки платёжных шлюзов (только админ)',
            'panel.payments.view' => 'Просмотр платежей/инвойсов в админ-панели',
            'panel.payments.manage' => 'Управление платежами в админ-панели (возвраты и т.д.)',

            // Управление базовыми правами ролей бизнеса (админ-панель)
            'panel.business.roles.manage' => 'Управление базовыми правами ролей бизнеса (админ-панель)',

            // Системные уведомления
            'panel.notifications.view' => 'Просмотр системных уведомлений (админ-панель)',
            'client.notifications.view' => 'Просмотр системных уведомлений (клиентская часть)',

            // Рассылки (админ-панель)
            'panel.broadcasts.send' => 'Рассылка уведомлений (админ-панель)',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['description' => $description]
            );
        }

        // Создание роли Админ
        // Админ имеет все права админ-панели (panel.*), без доступа к клиентской части (client.*)
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminPermissions = array_filter(array_keys($permissions), fn (string $name) => ! str_starts_with($name, 'client.'));
        $adminRole->syncPermissions($adminPermissions);

        // Создание роли Менеджер
        // Только panel.*, без доступа к клиентской части (client.*)
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerPermissions = [
            'panel.businesses.view',
            'panel.appointments.view',
            'panel.appointments.update',
            'panel.appointments.delete',
            'panel.clients.view',
            'panel.clients.create',
            'panel.clients.update',
            'panel.clients.delete',
            'panel.services.view',
            'panel.services.update',
            'panel.services.delete',
            'panel.locations.view',
            'panel.locations.update',
            'panel.locations.delete',
            'panel.masters.view',
            'panel.masters.update',
            'panel.masters.delete',
            'panel.analytics.view',
            'panel.analytics.general',
            'panel.tickets.view',
            'panel.tickets.update',
            'panel.tickets.delete',
            'panel.tickets.assign',
            'panel.tickets.categories.manage',
            'panel.payments.view',
            'panel.subscriptions.view',
            'panel.subscriptions.manage',
            'panel.business.roles.manage',
            'panel.notifications.view',
            'panel.broadcasts.send',
            'panel.countries.view',
            'panel.countries.create',
            'panel.countries.update',
            'panel.countries.delete',
            'panel.access',
        ];
        $managerRole->syncPermissions($managerPermissions);

        // Создание роли Поддержка
        // Только panel.*, без доступа к клиентской части (client.*)
        $supportRole = Role::firstOrCreate(['name' => 'support', 'guard_name' => 'web']);
        $supportPermissions = [
            'panel.analytics.view',
            'panel.analytics.general',
            'panel.support.view',
            'panel.tickets.view',
            'panel.tickets.update',
            'panel.tickets.assign',
            'panel.notifications.view',
            'panel.access',
        ];
        $supportRole->syncPermissions($supportPermissions);

        // Создание роли Пользователь
        // Единственная роль с доступом к клиентской части (client.*) по умолчанию.
        // Конкретные права в бизнесе проверяются через роль в бизнесе.
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userPermissions = [
            'client.access',
            'client.subscription.pay',
        ];
        $userRole->syncPermissions($userPermissions);
    }
}
