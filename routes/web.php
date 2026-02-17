<?php

use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Settings\LocationSettingsController;
use App\Http\Controllers\Settings\MasterScheduleController;
use App\Http\Controllers\Settings\MasterSettingsController;
use App\Http\Controllers\TelegramManagementController;
use App\Http\Controllers\TelegramSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'landing']);
Route::get('/privacy-policy', [
    \App\Http\Controllers\WelcomeController::class,
    'privacyPolicy',
])->name('privacy.policy');
Route::get('/public-offer', [
    \App\Http\Controllers\WelcomeController::class,
    'publicOffer',
])->name('public.offer');

// Первоначальная настройка (создание админа при первом запуске)
Route::get('/setup', [
    \App\Http\Controllers\SetupController::class,
    'show',
])->name('setup');
Route::post('/setup', [\App\Http\Controllers\SetupController::class, 'store']);

// Webhook для bePaid (без CSRF и авторизации)
Route::post('/webhooks/bepaid', [
    \App\Http\Controllers\Webhook\BepaidWebhookController::class,
    'handle',
])
    ->name('webhooks.bepaid')
    ->withoutMiddleware([
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    ]);

// Короткая ссылка для просмотра записи (упрощенная)
Route::get('/a/{token}', [
    \App\Http\Controllers\Public\AppointmentController::class,
    'viewByToken',
])->name('public.appointment.view');
Route::post('/a/{token}/cancel', [
    \App\Http\Controllers\Public\AppointmentController::class,
    'cancelByToken',
])->name('public.appointment.cancel');

// Публичные роуты для онлайн-записи
Route::prefix('book/{slug}')
    ->name('public.appointments.')
    ->group(function () {
        // Шаг 1: Выбор локации
        Route::get('/', [
            \App\Http\Controllers\Public\AppointmentController::class,
            'show',
        ])->name('show');

        // Шаг 2: Выбор услуги
        Route::get('/location/{locationId}', [
            \App\Http\Controllers\Public\AppointmentController::class,
            'selectLocation',
        ])->name('select-location');

        // Шаг 3: Выбор мастера
        Route::get('/location/{locationId}/service/{serviceId}', [
            \App\Http\Controllers\Public\AppointmentController::class,
            'selectService',
        ])->name('select-service');

        // Шаг 4: Выбор даты и времени (конкретный мастер)
        Route::get(
            '/location/{locationId}/service/{serviceId}/master/{masterId}',
            [
                \App\Http\Controllers\Public\AppointmentController::class,
                'selectTime',
            ],
        )->name('select-time');

        // Шаг 4 (альт.): Выбор даты и времени — любой мастер
        Route::get(
            '/location/{locationId}/service/{serviceId}/any',
            [
                \App\Http\Controllers\Public\AppointmentController::class,
                'selectTimeAny',
            ],
        )->name('select-time-any');

        // Сохранение записи
        Route::post('/store', [
            \App\Http\Controllers\Public\AppointmentController::class,
            'store',
        ])->name('store');

        // Страница успеха
        Route::get('/{token}', [
            \App\Http\Controllers\Public\AppointmentController::class,
            'success',
        ])->name('success');
    });

// Страница приглашения: доступна и гостям, и авторизованным (чтобы принять второе приглашение)
Route::get('/invite/{token}', [
    \App\Http\Controllers\Auth\BusinessInvitationController::class,
    'accept',
])->name('invite.accept');

// Создание аккаунта по приглашению — только для гостей
Route::middleware('guest')->group(function () {
    Route::post('/invite/{token}/activate', [
        \App\Http\Controllers\Auth\BusinessInvitationController::class,
        'activate',
    ])->name('invite.activate');
});

Route::middleware(['auth', 'verified.or.oauth'])->group(function () {
    // Welcome/Onboarding page
    Route::middleware(['only.client'])->group(function () {
        Route::get('/welcome', [
            \App\Http\Controllers\WelcomeController::class,
            'index',
        ])->name('welcome');
    });

    Route::post('/invite/{token}/accept', [
        \App\Http\Controllers\Auth\BusinessInvitationController::class,
        'store',
    ])->name('invite.store');

    // Уведомления (доступны всем авторизованным пользователям)
    Route::prefix('notifications')
        ->name('notifications.')
        ->group(function () {
            Route::get('/', [
                \App\Http\Controllers\NotificationController::class,
                'index',
            ])->name('index');
            Route::get('/unread-count', [
                \App\Http\Controllers\NotificationController::class,
                'unreadCount',
            ])->name('unread-count');
            Route::get('/unread', [
                \App\Http\Controllers\NotificationController::class,
                'unread',
            ])->name('unread');
            Route::post('/{id}/read', [
                \App\Http\Controllers\NotificationController::class,
                'markAsRead',
            ])->name('read');
            Route::post('/read-all', [
                \App\Http\Controllers\NotificationController::class,
                'markAllAsRead',
            ])->name('read-all');
            Route::delete('/{id}', [
                \App\Http\Controllers\NotificationController::class,
                'destroy',
            ])->name('destroy');
        });

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name(
            'profile.edit',
        );
        Route::patch('/', [ProfileController::class, 'update'])->name(
            'profile.update',
        );
        Route::patch('/password', [
            ProfileController::class,
            'updatePassword',
        ])->name('profile.password.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name(
            'profile.destroy',
        );
        Route::delete('/avatar', [
            ProfileController::class,
            'deleteAvatar',
        ])->name('profile.avatar.delete');
    });

    // Настройки уведомлений (доступны всем авторизованным пользователям)
    Route::prefix('settings/notifications')
        ->name('settings.notifications.')
        ->group(function () {
            Route::get('/', [
                \App\Http\Controllers\Settings\NotificationSettingsController::class,
                'index',
            ])->name('index');
            Route::post('/', [
                \App\Http\Controllers\Settings\NotificationSettingsController::class,
                'update',
            ])->name('update');
            Route::get('/api', [
                \App\Http\Controllers\Settings\NotificationSettingsController::class,
                'getSettings',
            ])->name('api');
            Route::post('/telegram/disconnect', [
                \App\Http\Controllers\Settings\NotificationSettingsController::class,
                'disconnectTelegram',
            ])->name('telegram.disconnect');
        });

    // === КЛИЕНТСКАЯ ЧАСТЬ ===
    // Для пользователей с доступом к клиентской части
    Route::middleware(['only.client'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name(
            'dashboard',
        );
        Route::post('/dashboard/refresh', [
            DashboardController::class,
            'refresh',
        ])->name('dashboard.refresh');

        // Аналитика
        Route::middleware([
            'check.business.permission:client.analytics.view',
        ])->group(function () {
            Route::get('/analytics', [
                \App\Http\Controllers\AnalyticsController::class,
                'index',
            ])->name('analytics.index');
            Route::get('/analytics/financial', [
                \App\Http\Controllers\AnalyticsController::class,
                'financial',
            ])->name('analytics.financial');
            Route::get('/analytics/general', [
                \App\Http\Controllers\AnalyticsController::class,
                'general',
            ])->name('analytics.general');
            Route::get('/analytics/clients', [
                \App\Http\Controllers\AnalyticsController::class,
                'clients',
            ])->name('analytics.clients');
            Route::get('/analytics/financial/export', [
                \App\Http\Controllers\AnalyticsController::class,
                'exportFinancial',
            ])->name('analytics.financial.export');
            Route::get('/analytics/clients/export', [
                \App\Http\Controllers\AnalyticsController::class,
                'exportClients',
            ])->name('analytics.clients.export');
        });

        // Клиенты
        // ВАЖНО: Специфичные маршруты (create) должны быть ПЕРЕД динамическими ({client})
        Route::middleware([
            'check.business.permission:client.clients.create',
            'check.plan.limit:max_clients',
        ])->group(function () {
            Route::get('/clients/create', [
                \App\Http\Controllers\ClientController::class,
                'create',
            ])->name('clients.create');
            Route::post('/clients', [
                \App\Http\Controllers\ClientController::class,
                'store',
            ])->name('clients.store');
        });

        Route::middleware([
            'check.business.permission:client.clients.view',
        ])->group(function () {
            Route::get('/clients', [
                \App\Http\Controllers\ClientController::class,
                'index',
            ])->name('clients.index');
            Route::get('/clients/{client}', [
                \App\Http\Controllers\ClientController::class,
                'show',
            ])->name('clients.show');
        });

        Route::middleware([
            'check.business.permission:client.clients.update',
        ])->group(function () {
            Route::get('/clients/{client}/edit', [
                \App\Http\Controllers\ClientController::class,
                'edit',
            ])->name('clients.edit');
            Route::patch('/clients/{client}', [
                \App\Http\Controllers\ClientController::class,
                'update',
            ])->name('clients.update');
        });

        Route::middleware([
            'check.business.permission:client.clients.delete',
        ])->group(function () {
            Route::delete('/clients/{client}', [
                \App\Http\Controllers\ClientController::class,
                'destroy',
            ])->name('clients.destroy');
        });

        Route::middleware([
            'check.business.permission:client.clients.export',
        ])->group(function () {
            Route::get('/clients-export', [
                \App\Http\Controllers\ClientController::class,
                'export',
            ])->name('clients.export');
        });

        // Услуги
        // ВАЖНО: /services/create перед /services/{service}
        Route::middleware([
            'check.business.permission:client.services.create',
            'check.plan.limit:max_services',
        ])->group(function () {
            Route::get('/services/create', [
                \App\Http\Controllers\ServiceController::class,
                'create',
            ])->name('services.create');
            Route::post('/services', [
                \App\Http\Controllers\ServiceController::class,
                'store',
            ])->name('services.store');
        });

        Route::middleware([
            'check.business.permission:client.services.view',
        ])->group(function () {
            Route::get('/services', [
                \App\Http\Controllers\ServiceController::class,
                'index',
            ])->name('services.index');
        });

        Route::middleware([
            'check.business.permission:client.services.update',
        ])->group(function () {
            Route::get('/services/{service}/edit', [
                \App\Http\Controllers\ServiceController::class,
                'edit',
            ])->name('services.edit');
            Route::patch('/services/{service}', [
                \App\Http\Controllers\ServiceController::class,
                'update',
            ])->name('services.update');
        });

        Route::middleware([
            'check.business.permission:client.services.delete',
        ])->group(function () {
            Route::delete('/services/{service}', [
                \App\Http\Controllers\ServiceController::class,
                'destroy',
            ])->name('services.destroy');
        });

        // Записи
        // ВАЖНО: Специфичные маршруты (create, calendar) должны быть ПЕРЕД динамическими ({appointment})
        Route::middleware([
            'check.business.permission:client.appointments.create',
            'check.plan.limit:max_appointments_per_month',
        ])->group(function () {
            Route::get('/appointments/create', [
                \App\Http\Controllers\AppointmentsController::class,
                'create',
            ])->name('appointments.create');
            Route::post('/appointments', [
                \App\Http\Controllers\AppointmentsController::class,
                'store',
            ])->name('appointments.store');
        });

        Route::middleware([
            'check.business.permission:client.appointments.view',
        ])->group(function () {
            Route::get('/appointments', [
                \App\Http\Controllers\AppointmentsController::class,
                'index',
            ])->name('appointments.index');
            Route::get('/appointments/calendar', [
                \App\Http\Controllers\AppointmentsController::class,
                'calendar',
            ])->name('appointments.calendar');
            Route::get('/appointments/{appointment}', [
                \App\Http\Controllers\AppointmentsController::class,
                'show',
            ])->name('appointments.show');
        });

        Route::middleware([
            'check.business.permission:client.appointments.update',
        ])->group(function () {
            Route::patch('/appointments/{appointment}/assign-master', [
                \App\Http\Controllers\AppointmentsController::class,
                'assignMaster',
            ])->name('appointments.assign-master');
            Route::get('/appointments/{appointment}/edit', [
                \App\Http\Controllers\AppointmentsController::class,
                'edit',
            ])->name('appointments.edit');
            Route::patch('/appointments/{appointment}', [
                \App\Http\Controllers\AppointmentsController::class,
                'update',
            ])->name('appointments.update');
            Route::patch('/appointments/{appointment}/confirm', [
                \App\Http\Controllers\AppointmentsController::class,
                'confirm',
            ])->name('appointments.confirm');
            Route::patch('/appointments/{appointment}/cancel', [
                \App\Http\Controllers\AppointmentsController::class,
                'cancel',
            ])->name('appointments.cancel');
            Route::patch('/appointments/{appointment}/complete', [
                \App\Http\Controllers\AppointmentsController::class,
                'complete',
            ])->name('appointments.complete');
            Route::post('/appointments/{appointment}/send-telegram-confirmation', [
                \App\Http\Controllers\AppointmentsController::class,
                'sendTelegramConfirmation',
            ])->name('appointments.send-telegram-confirmation');
        });

        Route::middleware([
            'check.business.permission:client.appointments.export',
        ])->group(function () {
            Route::get('/appointments-export', [
                \App\Http\Controllers\AppointmentsController::class,
                'export',
            ])->name('appointments.export');
        });

        // Тикеты (явные роуты без route model binding, используем {id} вместо {ticket})
        // ВАЖНО: Специфичные маршруты (create, edit) должны быть ПЕРЕД динамическими ({id})
        Route::middleware([
            'check.business.permission:client.tickets.create',
        ])->group(function () {
            Route::get('tickets/create', [
                \App\Http\Controllers\TicketController::class,
                'create',
            ])->name('tickets.create');
            Route::post('tickets', [
                \App\Http\Controllers\TicketController::class,
                'store',
            ])->name('tickets.store');
        });

        Route::middleware([
            'check.business.permission:client.tickets.view',
        ])->group(function () {
            Route::get('tickets', [
                \App\Http\Controllers\TicketController::class,
                'index',
            ])->name('tickets.index');
            Route::get('tickets/{id}', [
                \App\Http\Controllers\TicketController::class,
                'show',
            ])->name('tickets.show');
        });

        Route::middleware([
            'check.business.permission:client.tickets.update',
        ])->group(function () {
            Route::get('tickets/{id}/edit', [
                \App\Http\Controllers\TicketController::class,
                'edit',
            ])->name('tickets.edit');
            Route::patch('tickets/{id}', [
                \App\Http\Controllers\TicketController::class,
                'update',
            ])->name('tickets.update');
            Route::post('tickets/{id}/comments', [
                \App\Http\Controllers\TicketController::class,
                'addComment',
            ])->name('tickets.comments.store');
        });

        // Подписки и тарифы
        Route::middleware([
            'check.business.permission:client.subscription.view',
        ])->group(function () {
            Route::get('/subscription', [
                \App\Http\Controllers\SubscriptionController::class,
                'index',
            ])->name('subscription.index');
            Route::get('/subscription/current', [
                \App\Http\Controllers\SubscriptionController::class,
                'current',
            ])->name('subscription.current');
            Route::get('/subscription/{plan}', [
                \App\Http\Controllers\SubscriptionController::class,
                'show',
            ])->name('subscription.show');
        });

        Route::middleware([
            'check.business.permission:client.subscription.manage',
        ])->group(function () {
            Route::post('/subscription/{plan}/subscribe', [
                \App\Http\Controllers\SubscriptionController::class,
                'subscribe',
            ])->name('subscription.subscribe');
            Route::post('/subscription/renew', [
                \App\Http\Controllers\SubscriptionController::class,
                'renew',
            ])->name('subscription.renew');
            Route::post('/subscription/cancel', [
                \App\Http\Controllers\SubscriptionController::class,
                'cancel',
            ])->name('subscription.cancel');
        });

        // Оплата подписки
        Route::middleware([
            'check.business.permission:client.subscription.pay',
        ])->group(function () {
            Route::get('/subscription/payment/{invoice}', [
                \App\Http\Controllers\SubscriptionController::class,
                'payment',
            ])->name('subscription.payment');
            Route::get('/subscription/payment/success', [
                \App\Http\Controllers\SubscriptionController::class,
                'paymentSuccess',
            ])->name('subscription.payment.success');
            Route::get('/subscription/payment/decline', [
                \App\Http\Controllers\SubscriptionController::class,
                'paymentDecline',
            ])->name('subscription.payment.decline');
            Route::get('/subscription/payment/fail', [
                \App\Http\Controllers\SubscriptionController::class,
                'paymentFail',
            ])->name('subscription.payment.fail');
            Route::get('/subscription/payment/cancel', [
                \App\Http\Controllers\SubscriptionController::class,
                'paymentCancel',
            ])->name('subscription.payment.cancel');
        });

        // Настройки бизнеса
        Route::name('settings.')->group(function () {
            Route::get('/settings', [
                BusinessSettingsController::class,
                'index',
            ])->name('index');

            // Онлайн-запись
            Route::middleware([
                'check.business.permission:client.online_booking.manage',
            ])->group(function () {
                Route::get('/settings/online-booking', [
                    BusinessSettingsController::class,
                    'onlineBooking',
                ])->name('online-booking');
                Route::patch('/settings/online-booking', [
                    BusinessSettingsController::class,
                    'updateOnlineBooking',
                ])->name('online-booking.update');
                Route::get('/settings/online-booking/qr', [
                    BusinessSettingsController::class,
                    'onlineBookingQr',
                ])->name('online-booking.qr');
            });

            // Telegram
            Route::middleware([
                'check.business.permission:client.telegram.manage',
            ])->group(function () {
                Route::get('/settings/telegram', [
                    TelegramSettingsController::class,
                    'index',
                ])->name('telegram');
                Route::delete('/settings/telegram/disconnect', [
                    TelegramSettingsController::class,
                    'disconnect',
                ])->name('telegram.disconnect');
            });

            // Управление бизнесами (список, переключение) — доступно и без выбранного бизнеса (чтобы показать пустой список и кнопку «Создать»)
            Route::get('/businesses', [
                BusinessSettingsController::class,
                'businessesIndex',
            ])->name('businesses.index');

            // Настройки бизнеса (создание доступно без бизнеса)
            Route::get('/business/create', [
                BusinessSettingsController::class,
                'create',
            ])->name('business.create');
            Route::post('/business', [
                BusinessSettingsController::class,
                'store',
            ])->name('business.store');
            Route::post('/business/switch', [
                BusinessSettingsController::class,
                'switch',
            ])->name('business.switch');

            Route::middleware([
                'check.business.permission:client.businesses.update',
            ])->group(function () {
                Route::get('/business', [
                    BusinessSettingsController::class,
                    'edit',
                ])->name('business.edit');
                Route::patch('/business', [
                    BusinessSettingsController::class,
                    'update',
                ])->name('business.update');
            });
            Route::middleware([
                'check.business.permission:client.businesses.delete',
            ])->group(function () {
                Route::delete('/business', [
                    BusinessSettingsController::class,
                    'destroy',
                ])->name('business.destroy');
            });

            // Локации: /locations/create перед /locations/{location}
            Route::middleware([
                'check.business.permission:client.locations.create',
                'check.plan.limit:max_locations',
            ])->group(function () {
                Route::get('/locations/create', [
                    LocationSettingsController::class,
                    'create',
                ])->name('locations.create');
                Route::post('/locations', [
                    LocationSettingsController::class,
                    'store',
                ])->name('locations.store');
            });

            Route::middleware([
                'check.business.permission:client.locations.view',
            ])->group(function () {
                Route::get('/locations', [
                    LocationSettingsController::class,
                    'index',
                ])->name('locations');
            });

            Route::middleware([
                'check.business.permission:client.locations.update',
            ])->group(function () {
                Route::get('/locations/{location}/edit', [
                    LocationSettingsController::class,
                    'edit',
                ])->name('locations.edit');
                Route::patch('/locations/{location}', [
                    LocationSettingsController::class,
                    'update',
                ])->name('locations.update');
            });

            Route::middleware([
                'check.business.permission:client.locations.delete',
            ])->group(function () {
                Route::delete('/locations/{location}', [
                    LocationSettingsController::class,
                    'destroy',
                ])->name('locations.destroy');
            });

            // Мастера: /masters/create перед /masters/{master}
            Route::middleware([
                'check.business.permission:client.masters.create',
                'check.plan.limit:max_masters',
            ])->group(function () {
                Route::get('/masters/create', [
                    MasterSettingsController::class,
                    'create',
                ])->name('masters.create');
                Route::post('/masters', [
                    MasterSettingsController::class,
                    'store',
                ])->name('masters.store');
            });

            Route::middleware([
                'check.business.permission:client.masters.view',
            ])->group(function () {
                Route::get('/masters', [
                    MasterSettingsController::class,
                    'index',
                ])->name('masters');
            });

            Route::middleware([
                'check.business.permission:client.masters.update',
            ])->group(function () {
                Route::get('/masters/{master}/edit', [
                    MasterSettingsController::class,
                    'edit',
                ])->name('masters.edit');
                Route::patch('/masters/{master}', [
                    MasterSettingsController::class,
                    'update',
                ])->name('masters.update');

                // Расписание мастера
                Route::get('/masters/{master}/schedule/edit', [
                    MasterScheduleController::class,
                    'edit',
                ])->name('masters.schedule.edit');
                Route::patch('/masters/{master}/schedule', [
                    MasterScheduleController::class,
                    'update',
                ])->name('masters.schedule.update');
            });

            Route::middleware([
                'check.business.permission:client.masters.delete',
            ])->group(function () {
                Route::delete('/masters/{master}', [
                    MasterSettingsController::class,
                    'destroy',
                ])->name('masters.destroy');
            });

            // Пользователи бизнеса: /users/create перед /users/{user}
            Route::middleware([
                'check.business.permission:client.business.users.create',
                'check.plan.limit:max_business_users',
            ])->group(function () {
                Route::get('/users/create', [
                    \App\Http\Controllers\Settings\BusinessUsersController::class,
                    'create',
                ])->name('users.create');
                Route::post('/users/invite', [
                    \App\Http\Controllers\Settings\BusinessUsersController::class,
                    'storeInvitation',
                ])->name('users.invite');
                Route::post('/users/manual', [
                    \App\Http\Controllers\Settings\BusinessUsersController::class,
                    'storeManual',
                ])->name('users.manual');
                Route::post('/users/{invitation}/resend', [
                    \App\Http\Controllers\Settings\BusinessUsersController::class,
                    'resendInvitation',
                ])->name('users.resend');
            });

            Route::middleware([
                'check.business.permission:client.business.users.view',
            ])->group(function () {
                Route::get('/users', [
                    \App\Http\Controllers\Settings\BusinessUsersController::class,
                    'index',
                ])->name('users.index');
            });

            Route::middleware([
                'check.business.permission:client.business.users.update',
            ])->group(function () {
                Route::get('/users/{user}/edit', [
                    \App\Http\Controllers\Settings\BusinessUsersController::class,
                    'edit',
                ])->name('users.edit');
                Route::patch('/users/{user}', [
                    \App\Http\Controllers\Settings\BusinessUsersController::class,
                    'update',
                ])->name('users.update');
            });

            Route::middleware([
                'check.business.permission:client.business.users.delete',
            ])->group(function () {
                Route::delete('/users/{user}', [
                    \App\Http\Controllers\Settings\BusinessUsersController::class,
                    'destroy',
                ])->name('users.destroy');
            });

            // Управление правами ролей бизнеса: /roles/create перед /roles/{role}
            Route::middleware([
                'check.business.permission:client.business.roles.manage',
            ])->group(function () {
                Route::get('/roles', [
                    \App\Http\Controllers\Settings\BusinessRolePermissionsController::class,
                    'index',
                ])->name('roles.index');
                Route::get('/roles/create', [
                    \App\Http\Controllers\Settings\BusinessRolePermissionsController::class,
                    'create',
                ])->name('roles.create');
                Route::post('/roles', [
                    \App\Http\Controllers\Settings\BusinessRolePermissionsController::class,
                    'store',
                ])->name('roles.store');
                Route::get('/roles/{role}', [
                    \App\Http\Controllers\Settings\BusinessRolePermissionsController::class,
                    'show',
                ])->name('roles.show');
                Route::put('/roles/{role}', [
                    \App\Http\Controllers\Settings\BusinessRolePermissionsController::class,
                    'update',
                ])->name('roles.update');
                Route::delete('/roles/{role}', [
                    \App\Http\Controllers\Settings\BusinessRolePermissionsController::class,
                    'destroy',
                ])->name('roles.destroy');
            });
        });
    });

    // === АДМИНСКАЯ ПАНЕЛЬ ===
    // Для пользователей с доступом к админке
    Route::middleware(['only.panel'])
        ->prefix('panel')
        ->name('panel.')
        ->group(function () {
            // Главная панели
            Route::get('/', [
                \App\Http\Controllers\Panel\PanelController::class,
                'index',
            ])->name('index');
            Route::post('/refresh', [
                \App\Http\Controllers\Panel\PanelController::class,
                'refresh',
            ])->name('refresh');

            // Профиль пользователя в панели
            Route::prefix('profile')->group(function () {
                Route::get('/', [ProfileController::class, 'edit'])->name(
                    'profile.edit',
                );
                Route::patch('/', [ProfileController::class, 'update'])->name(
                    'profile.update',
                );
                Route::patch('/password', [
                    ProfileController::class,
                    'updatePassword',
                ])->name('profile.password.update');
                Route::delete('/', [ProfileController::class, 'destroy'])->name(
                    'profile.destroy',
                );
                Route::delete('/avatar', [
                    ProfileController::class,
                    'deleteAvatar',
                ])->name('profile.avatar.delete');
            });

            // Пользователи (только админ): /users/create перед /users/{user}
            Route::middleware(['check.permission:panel.users.create'])->group(
                function () {
                    Route::get('/users/create', [
                        \App\Http\Controllers\Panel\UserController::class,
                        'create',
                    ])->name('users.create');
                    Route::post('/users', [
                        \App\Http\Controllers\Panel\UserController::class,
                        'store',
                    ])->name('users.store');
                },
            );

            Route::middleware(['check.permission:panel.users.view'])->group(
                function () {
                    Route::get('/users', [
                        \App\Http\Controllers\Panel\UserController::class,
                        'index',
                    ])->name('users');
                },
            );

            Route::middleware(['check.permission:panel.users.update'])->group(
                function () {
                    Route::get('/users/{user}/edit', [
                        \App\Http\Controllers\Panel\UserController::class,
                        'edit',
                    ])->name('users.edit');
                    Route::patch('/users/{user}', [
                        \App\Http\Controllers\Panel\UserController::class,
                        'update',
                    ])->name('users.update');
                },
            );

            Route::middleware(['check.permission:panel.users.delete'])->group(
                function () {
                    Route::delete('/users/{user}', [
                        \App\Http\Controllers\Panel\UserController::class,
                        'destroy',
                    ])->name('users.destroy');
                },
            );

            // Роли (только админ): /roles/create перед /roles/{role}
            Route::middleware(['check.permission:panel.roles.create'])->group(
                function () {
                    Route::get('/roles/create', [
                        \App\Http\Controllers\Panel\RoleController::class,
                        'create',
                    ])->name('roles.create');
                    Route::post('/roles', [
                        \App\Http\Controllers\Panel\RoleController::class,
                        'store',
                    ])->name('roles.store');
                },
            );

            Route::middleware(['check.permission:panel.roles.view'])->group(
                function () {
                    Route::get('/roles', [
                        \App\Http\Controllers\Panel\RoleController::class,
                        'index',
                    ])->name('roles');
                },
            );

            Route::middleware(['check.permission:panel.roles.update'])->group(
                function () {
                    Route::get('/roles/{role}/edit', [
                        \App\Http\Controllers\Panel\RoleController::class,
                        'edit',
                    ])->name('roles.edit');
                    Route::patch('/roles/{role}', [
                        \App\Http\Controllers\Panel\RoleController::class,
                        'update',
                    ])->name('roles.update');
                },
            );

            Route::middleware(['check.permission:panel.roles.delete'])->group(
                function () {
                    Route::delete('/roles/{role}', [
                        \App\Http\Controllers\Panel\RoleController::class,
                        'destroy',
                    ])->name('roles.destroy');
                },
            );

            // Права доступа (только админ): /permissions/create перед /permissions/{permission}
            Route::middleware([
                'check.permission:panel.permissions.create',
            ])->group(function () {
                Route::get('/permissions/create', [
                    \App\Http\Controllers\Panel\PermissionController::class,
                    'create',
                ])->name('permissions.create');
                Route::post('/permissions', [
                    \App\Http\Controllers\Panel\PermissionController::class,
                    'store',
                ])->name('permissions.store');
            });

            Route::middleware([
                'check.permission:panel.permissions.view',
            ])->group(function () {
                Route::get('/permissions', [
                    \App\Http\Controllers\Panel\PermissionController::class,
                    'index',
                ])->name('permissions');
            });

            Route::middleware([
                'check.permission:panel.permissions.update',
            ])->group(function () {
                Route::get('/permissions/{permission}/edit', [
                    \App\Http\Controllers\Panel\PermissionController::class,
                    'edit',
                ])->name('permissions.edit');
                Route::patch('/permissions/{permission}', [
                    \App\Http\Controllers\Panel\PermissionController::class,
                    'update',
                ])->name('permissions.update');
            });

            Route::middleware([
                'check.permission:panel.permissions.delete',
            ])->group(function () {
                Route::delete('/permissions/{permission}', [
                    \App\Http\Controllers\Panel\PermissionController::class,
                    'destroy',
                ])->name('permissions.destroy');
            });

            // Бизнесы (админ и менеджер)
            Route::middleware([
                'check.permission:panel.businesses.view',
            ])->group(function () {
                Route::get('/businesses', [
                    \App\Http\Controllers\Panel\BusinessController::class,
                    'index',
                ])->name('businesses');
                Route::get('/businesses/{business}', [
                    \App\Http\Controllers\Panel\BusinessController::class,
                    'show',
                ])->name('businesses.show');
            });

            Route::middleware([
                'check.permission:panel.businesses.update',
            ])->group(function () {
                Route::get('/businesses/{business}/edit', [
                    \App\Http\Controllers\Panel\BusinessController::class,
                    'edit',
                ])->name('businesses.edit');
                Route::patch('/businesses/{business}', [
                    \App\Http\Controllers\Panel\BusinessController::class,
                    'update',
                ])->name('businesses.update');
            });

            Route::middleware([
                'check.permission:panel.businesses.delete',
            ])->group(function () {
                Route::delete('/businesses/{business}', [
                    \App\Http\Controllers\Panel\BusinessController::class,
                    'destroy',
                ])->name('businesses.destroy');
            });

            // Записи (админ и менеджер)
            Route::middleware([
                'check.permission:panel.appointments.view',
            ])->group(function () {
                Route::get('/appointments', [
                    \App\Http\Controllers\Panel\AppointmentController::class,
                    'index',
                ])->name('appointments');
            });

            Route::middleware([
                'check.permission:panel.appointments.update',
            ])->group(function () {
                Route::get('/appointments/{appointment}/edit', [
                    \App\Http\Controllers\Panel\AppointmentController::class,
                    'edit',
                ])->name('appointments.edit');
                Route::patch('/appointments/{appointment}', [
                    \App\Http\Controllers\Panel\AppointmentController::class,
                    'update',
                ])->name('appointments.update');
            });

            Route::middleware([
                'check.permission:panel.appointments.delete',
            ])->group(function () {
                Route::delete('/appointments/{appointment}', [
                    \App\Http\Controllers\Panel\AppointmentController::class,
                    'destroy',
                ])->name('appointments.destroy');
            });

            // Клиенты (админ и менеджер): /clients/create перед /clients/{client}
            Route::middleware(['check.permission:panel.clients.create'])->group(
                function () {
                    Route::get('/clients/create', [
                        \App\Http\Controllers\Panel\ClientController::class,
                        'create',
                    ])->name('clients.create');
                    Route::post('/clients', [
                        \App\Http\Controllers\Panel\ClientController::class,
                        'store',
                    ])->name('clients.store');
                },
            );

            Route::middleware(['check.permission:panel.clients.view'])->group(
                function () {
                    Route::get('/clients', [
                        \App\Http\Controllers\Panel\ClientController::class,
                        'index',
                    ])->name('clients');
                },
            );

            Route::middleware(['check.permission:panel.clients.update'])->group(
                function () {
                    Route::get('/clients/{client}/edit', [
                        \App\Http\Controllers\Panel\ClientController::class,
                        'edit',
                    ])->name('clients.edit');
                    Route::patch('/clients/{client}', [
                        \App\Http\Controllers\Panel\ClientController::class,
                        'update',
                    ])->name('clients.update');
                },
            );

            Route::middleware(['check.permission:panel.clients.delete'])->group(
                function () {
                    Route::delete('/clients/{client}', [
                        \App\Http\Controllers\Panel\ClientController::class,
                        'destroy',
                    ])->name('clients.destroy');
                },
            );

            // Услуги (админ и менеджер)
            Route::middleware(['check.permission:panel.services.view'])->group(
                function () {
                    Route::get('/services', [
                        \App\Http\Controllers\Panel\ServiceController::class,
                        'index',
                    ])->name('services');
                    Route::get('/services/{service}', [
                        \App\Http\Controllers\Panel\ServiceController::class,
                        'show',
                    ])->name('services.show');
                },
            );

            Route::middleware([
                'check.permission:panel.services.update',
            ])->group(function () {
                Route::get('/services/{service}/edit', [
                    \App\Http\Controllers\Panel\ServiceController::class,
                    'edit',
                ])->name('services.edit');
                Route::patch('/services/{service}', [
                    \App\Http\Controllers\Panel\ServiceController::class,
                    'update',
                ])->name('services.update');
            });

            Route::middleware([
                'check.permission:panel.services.delete',
            ])->group(function () {
                Route::delete('/services/{service}', [
                    \App\Http\Controllers\Panel\ServiceController::class,
                    'destroy',
                ])->name('services.destroy');
            });

            // Локации (админ и менеджер)
            Route::middleware(['check.permission:panel.locations.view'])->group(
                function () {
                    Route::get('/locations', [
                        \App\Http\Controllers\Panel\LocationController::class,
                        'index',
                    ])->name('locations');
                    Route::get('/locations/{location}', [
                        \App\Http\Controllers\Panel\LocationController::class,
                        'show',
                    ])->name('locations.show');
                },
            );

            Route::middleware([
                'check.permission:panel.locations.update',
            ])->group(function () {
                Route::get('/locations/{location}/edit', [
                    \App\Http\Controllers\Panel\LocationController::class,
                    'edit',
                ])->name('locations.edit');
                Route::patch('/locations/{location}', [
                    \App\Http\Controllers\Panel\LocationController::class,
                    'update',
                ])->name('locations.update');
            });

            Route::middleware([
                'check.permission:panel.locations.delete',
            ])->group(function () {
                Route::delete('/locations/{location}', [
                    \App\Http\Controllers\Panel\LocationController::class,
                    'destroy',
                ])->name('locations.destroy');
            });

            // Мастера (админ и менеджер)
            Route::middleware(['check.permission:panel.masters.view'])->group(
                function () {
                    Route::get('/masters', [
                        \App\Http\Controllers\Panel\MasterController::class,
                        'index',
                    ])->name('masters');
                    Route::get('/masters/{master}', [
                        \App\Http\Controllers\Panel\MasterController::class,
                        'show',
                    ])->name('masters.show');
                },
            );

            Route::middleware(['check.permission:panel.masters.update'])->group(
                function () {
                    Route::get('/masters/{master}/edit', [
                        \App\Http\Controllers\Panel\MasterController::class,
                        'edit',
                    ])->name('masters.edit');
                    Route::patch('/masters/{master}', [
                        \App\Http\Controllers\Panel\MasterController::class,
                        'update',
                    ])->name('masters.update');
                },
            );

            Route::middleware(['check.permission:panel.masters.delete'])->group(
                function () {
                    Route::delete('/masters/{master}', [
                        \App\Http\Controllers\Panel\MasterController::class,
                        'destroy',
                    ])->name('masters.destroy');
                },
            );

            // Аналитика
            // Общая аналитика (обзор)
            Route::middleware(['check.permission:panel.analytics.view'])->group(
                function () {
                    Route::get('/analytics', [
                        \App\Http\Controllers\Panel\AnalyticsController::class,
                        'index',
                    ])->name('analytics');
                },
            );

            // Финансовая аналитика
            Route::middleware([
                'check.permission:panel.analytics.financial',
            ])->group(function () {
                Route::get('/analytics/financial', [
                    \App\Http\Controllers\Panel\AnalyticsController::class,
                    'financial',
                ])->name('analytics.financial');
            });

            // Общая статистика
            Route::middleware([
                'check.permission:panel.analytics.general',
            ])->group(function () {
                Route::get('/analytics/general', [
                    \App\Http\Controllers\Panel\AnalyticsController::class,
                    'general',
                ])->name('analytics.general');
            });

            // Аналитика подписок
            Route::middleware([
                'check.permission:panel.analytics.subscriptions',
            ])->group(function () {
                Route::get('/analytics/subscriptions', [
                    \App\Http\Controllers\Panel\AnalyticsController::class,
                    'subscriptions',
                ])->name('analytics.subscriptions');
            });

            // Поддержка - перенаправление на тикеты (для обратной совместимости)
            Route::middleware(['check.permission:panel.support.view'])->group(
                function () {
                    Route::get('/support', function () {
                        return redirect()->route('panel.tickets');
                    })->name('support');
                },
            );

            // Рассылки (админ, менеджер)
            Route::middleware([
                'check.permission:panel.broadcasts.send',
            ])->group(function () {
                Route::get('/broadcasts', [
                    \App\Http\Controllers\Panel\BroadcastController::class,
                    'index',
                ])->name('broadcasts.index');
                Route::get('/broadcasts/create', [
                    \App\Http\Controllers\Panel\BroadcastController::class,
                    'create',
                ])->name('broadcasts.create');
                Route::post('/broadcasts', [
                    \App\Http\Controllers\Panel\BroadcastController::class,
                    'store',
                ])->name('broadcasts.store');
            });

            // Тикеты (доступ для всех с panel.access, но видимость зависит от прав)
            // Пользователи с panel.tickets.view видят все тикеты
            // Пользователи без этого права видят только назначенные на них тикеты
            Route::middleware(['check.permission:panel.access'])->group(
                function () {
                    Route::get('/tickets', [
                        \App\Http\Controllers\Panel\TicketController::class,
                        'index',
                    ])->name('tickets');
                    Route::get('/tickets/{ticket}', [
                        \App\Http\Controllers\Panel\TicketController::class,
                        'show',
                    ])->name('tickets.show');
                },
            );

            Route::middleware(['check.permission:panel.tickets.update'])->group(
                function () {
                    Route::get('/tickets/{ticket}/edit', [
                        \App\Http\Controllers\Panel\TicketController::class,
                        'edit',
                    ])->name('tickets.edit');
                    Route::patch('/tickets/{ticket}', [
                        \App\Http\Controllers\Panel\TicketController::class,
                        'update',
                    ])->name('tickets.update');
                    Route::post('/tickets/{ticket}/assign', [
                        \App\Http\Controllers\Panel\TicketController::class,
                        'assign',
                    ])->name('tickets.assign');
                },
            );

            // Изменение статуса и комментарии доступны также назначенным пользователям
            Route::middleware(['check.permission:panel.access'])->group(
                function () {
                    Route::post('/tickets/{ticket}/status', [
                        \App\Http\Controllers\Panel\TicketController::class,
                        'updateStatus',
                    ])->name('tickets.status');
                    Route::post('/tickets/{ticket}/comments', [
                        \App\Http\Controllers\Panel\TicketController::class,
                        'addComment',
                    ])->name('tickets.comments.store');
                },
            );

            Route::middleware(['check.permission:panel.tickets.delete'])->group(
                function () {
                    Route::delete('/tickets/{ticket}', [
                        \App\Http\Controllers\Panel\TicketController::class,
                        'destroy',
                    ])->name('tickets.destroy');
                },
            );

            // Категории тикетов (админ)
            Route::middleware([
                'check.permission:panel.tickets.categories.manage',
            ])->group(function () {
                Route::resource(
                    'ticket-categories',
                    \App\Http\Controllers\Panel\TicketCategoryController::class,
                );
            });

            // Управление Telegram ботом
            Route::middleware([
                'check.permission:panel.telegram.manage',
            ])->group(function () {
                Route::get('/telegram-management', [
                    TelegramManagementController::class,
                    'index',
                ])->name('telegram.management');
                Route::get('/telegram-management/create', [
                    TelegramManagementController::class,
                    'create',
                ])->name('telegram.management.create');
                Route::post('/telegram-management', [
                    TelegramManagementController::class,
                    'store',
                ])->name('telegram.management.store');
                Route::get('/telegram-management/{bot}/edit', [
                    TelegramManagementController::class,
                    'edit',
                ])->name('telegram.management.edit');
                Route::patch('/telegram-management/{bot}', [
                    TelegramManagementController::class,
                    'update',
                ])->name('telegram.management.update');
                Route::delete('/telegram-management/{bot}', [
                    TelegramManagementController::class,
                    'destroy',
                ])->name('telegram.management.destroy');
                Route::post('/telegram-management/{bot}/set-webhook', [
                    TelegramManagementController::class,
                    'setWebhook',
                ])->name('telegram.management.set-webhook');
                Route::post('/telegram-management/{bot}/delete-webhook', [
                    TelegramManagementController::class,
                    'deleteWebhook',
                ])->name('telegram.management.delete-webhook');
                Route::post('/telegram-management/{bot}/bot-info', [
                    TelegramManagementController::class,
                    'getBotInfo',
                ])->name('telegram.management.bot-info');
            });

            // Тарифы (админ): /plans/create перед /plans/{plan}
            Route::middleware(['check.permission:panel.plans.create'])->group(
                function () {
                    Route::get('/plans/create', [
                        \App\Http\Controllers\Panel\PlanController::class,
                        'create',
                    ])->name('plans.create');
                    Route::post('/plans', [
                        \App\Http\Controllers\Panel\PlanController::class,
                        'store',
                    ])->name('plans.store');
                },
            );

            Route::middleware(['check.permission:panel.plans.view'])->group(
                function () {
                    Route::get('/plans', [
                        \App\Http\Controllers\Panel\PlanController::class,
                        'index',
                    ])->name('plans.index');
                },
            );

            Route::middleware(['check.permission:panel.plans.update'])->group(
                function () {
                    Route::get('/plans/{plan}/edit', [
                        \App\Http\Controllers\Panel\PlanController::class,
                        'edit',
                    ])->name('plans.edit');
                    Route::patch('/plans/{plan}', [
                        \App\Http\Controllers\Panel\PlanController::class,
                        'update',
                    ])->name('plans.update');
                    Route::post('/plans/{plan}/increment-sort', [
                        \App\Http\Controllers\Panel\PlanController::class,
                        'incrementSortOrder',
                    ])->name('plans.increment-sort');
                    Route::post('/plans/{plan}/decrement-sort', [
                        \App\Http\Controllers\Panel\PlanController::class,
                        'decrementSortOrder',
                    ])->name('plans.decrement-sort');
                    Route::post('/plans/reorder', [
                        \App\Http\Controllers\Panel\PlanController::class,
                        'reorder',
                    ])->name('plans.reorder');
                },
            );

            Route::middleware(['check.permission:panel.plans.delete'])->group(
                function () {
                    Route::delete('/plans/{plan}', [
                        \App\Http\Controllers\Panel\PlanController::class,
                        'destroy',
                    ])->name('plans.destroy');
                },
            );

            // Свойства тарифов: /plans/properties/create перед /plans/properties/{metric}
            Route::middleware(['check.permission:panel.plans.create'])->group(
                function () {
                    Route::get('/plans/properties/create', [
                        \App\Http\Controllers\Panel\SubscriptionMetricController::class,
                        'create',
                    ])->name('plans.properties.create');
                    Route::post('/plans/properties', [
                        \App\Http\Controllers\Panel\SubscriptionMetricController::class,
                        'store',
                    ])->name('plans.properties.store');
                },
            );

            Route::middleware(['check.permission:panel.plans.view'])->group(
                function () {
                    Route::get('/plans/properties', [
                        \App\Http\Controllers\Panel\SubscriptionMetricController::class,
                        'index',
                    ])->name('plans.properties.index');
                },
            );

            Route::middleware(['check.permission:panel.plans.update'])->group(
                function () {
                    Route::get('/plans/properties/{metric}/edit', [
                        \App\Http\Controllers\Panel\SubscriptionMetricController::class,
                        'edit',
                    ])->name('plans.properties.edit');
                    Route::patch('/plans/properties/{metric}', [
                        \App\Http\Controllers\Panel\SubscriptionMetricController::class,
                        'update',
                    ])->name('plans.properties.update');
                    Route::post('/plans/properties/{metric}/increment-sort', [
                        \App\Http\Controllers\Panel\SubscriptionMetricController::class,
                        'incrementSortOrder',
                    ])->name('plans.properties.increment-sort');
                    Route::post('/plans/properties/{metric}/decrement-sort', [
                        \App\Http\Controllers\Panel\SubscriptionMetricController::class,
                        'decrementSortOrder',
                    ])->name('plans.properties.decrement-sort');
                    Route::post('/plans/properties/reorder', [
                        \App\Http\Controllers\Panel\SubscriptionMetricController::class,
                        'reorder',
                    ])->name('plans.properties.reorder');
                },
            );

            Route::middleware(['check.permission:panel.plans.delete'])->group(
                function () {
                    Route::delete('/plans/properties/{metric}', [
                        \App\Http\Controllers\Panel\SubscriptionMetricController::class,
                        'destroy',
                    ])->name('plans.properties.destroy');
                },
            );

            // Управление подписками пользователей
            Route::middleware(['check.permission:panel.subscriptions.view'])->group(
                function () {
                    Route::get('/subscriptions', [
                        \App\Http\Controllers\Panel\SubscriptionController::class,
                        'index',
                    ])->name('subscriptions.index');
                    Route::get('/subscriptions/{subscription}', [
                        \App\Http\Controllers\Panel\SubscriptionController::class,
                        'show',
                    ])->name('subscriptions.show');
                },
            );
            Route::middleware(['check.permission:panel.subscriptions.manage'])->group(
                function () {
                    Route::post('/subscriptions/{subscription}/cancel', [
                        \App\Http\Controllers\Panel\SubscriptionController::class,
                        'cancel',
                    ])->name('subscriptions.cancel');
                    Route::post('/subscriptions/grant', [
                        \App\Http\Controllers\Panel\SubscriptionController::class,
                        'grant',
                    ])->name('subscriptions.grant');
                },
            );

            // Страны (справочник): /countries/create перед /countries/{country}
            Route::middleware(['check.permission:panel.countries.create'])->group(
                function () {
                    Route::get('/countries/create', [
                        \App\Http\Controllers\Panel\CountryController::class,
                        'create',
                    ])->name('countries.create');
                    Route::post('/countries', [
                        \App\Http\Controllers\Panel\CountryController::class,
                        'store',
                    ])->name('countries.store');
                },
            );

            Route::middleware(['check.permission:panel.countries.view'])->group(
                function () {
                    Route::get('/countries', [
                        \App\Http\Controllers\Panel\CountryController::class,
                        'index',
                    ])->name('countries.index');
                },
            );

            Route::middleware(['check.permission:panel.countries.update'])->group(
                function () {
                    Route::get('/countries/{country}/edit', [
                        \App\Http\Controllers\Panel\CountryController::class,
                        'edit',
                    ])->name('countries.edit');
                    Route::patch('/countries/{country}', [
                        \App\Http\Controllers\Panel\CountryController::class,
                        'update',
                    ])->name('countries.update');
                },
            );

            Route::middleware(['check.permission:panel.countries.delete'])->group(
                function () {
                    Route::delete('/countries/{country}', [
                        \App\Http\Controllers\Panel\CountryController::class,
                        'destroy',
                    ])->name('countries.destroy');
                },
            );

            // Управление базовыми правами ролей бизнеса
            Route::middleware([
                'check.permission:panel.business.roles.manage',
            ])->group(function () {
                Route::get('/business-roles', [
                    \App\Http\Controllers\Panel\BusinessRolePermissionsController::class,
                    'index',
                ])->name('business-roles.index');
                Route::get('/business-roles/create', [
                    \App\Http\Controllers\Panel\BusinessRolePermissionsController::class,
                    'create',
                ])->name('business-roles.create');
                Route::post('/business-roles', [
                    \App\Http\Controllers\Panel\BusinessRolePermissionsController::class,
                    'store',
                ])->name('business-roles.store');
                Route::get('/business-roles/{role}', [
                    \App\Http\Controllers\Panel\BusinessRolePermissionsController::class,
                    'show',
                ])->name('business-roles.show');
                Route::put('/business-roles/{role}', [
                    \App\Http\Controllers\Panel\BusinessRolePermissionsController::class,
                    'update',
                ])->name('business-roles.update');
                Route::delete('/business-roles/{role}', [
                    \App\Http\Controllers\Panel\BusinessRolePermissionsController::class,
                    'destroy',
                ])->name('business-roles.destroy');
            });

            // Уведомления: список (тот же controller, layout по path) и настройки
            Route::get('/notifications', [
                \App\Http\Controllers\NotificationController::class,
                'index',
            ])->name('notifications.index');

            Route::prefix('settings/notifications')
                ->name('settings.notifications.')
                ->group(function () {
                    Route::get('/', [
                        \App\Http\Controllers\Panel\NotificationSettingsController::class,
                        'index',
                    ])->name('index');
                    Route::post('/', [
                        \App\Http\Controllers\Panel\NotificationSettingsController::class,
                        'update',
                    ])->name('update');
                    Route::get('/api', [
                        \App\Http\Controllers\Panel\NotificationSettingsController::class,
                        'getSettings',
                    ])->name('api');
                    Route::post('/telegram/disconnect', [
                        \App\Http\Controllers\Panel\NotificationSettingsController::class,
                        'disconnectTelegram',
                    ])->name('telegram.disconnect');
                });

            // Настройки bePaid перенесены в .env (config/bepaid.php). Маршруты админки удалены.

            // Управление платежами (инвойсы)
            Route::middleware(['check.permission:panel.payments.view'])->group(
                function () {
                    Route::get('/invoices', [
                        \App\Http\Controllers\Panel\InvoiceController::class,
                        'index',
                    ])->name('invoices');
                    Route::get('/invoices/{invoice}', [
                        \App\Http\Controllers\Panel\InvoiceController::class,
                        'show',
                    ])->name('invoices.show');
                },
            );

            Route::middleware([
                'check.permission:panel.payments.manage',
            ])->group(function () {
                Route::post('/invoices/{invoice}/refund', [
                    \App\Http\Controllers\Panel\InvoiceController::class,
                    'refund',
                ])->name('invoices.refund');
            });
        });
});

require __DIR__.'/auth.php';
