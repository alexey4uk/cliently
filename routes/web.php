<?php

use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Settings\LocationSettingsController;
use App\Http\Controllers\Settings\MasterSettingsController;
use App\Http\Controllers\TelegramManagementController;
use App\Http\Controllers\TelegramSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Короткая ссылка для просмотра записи (упрощенная)
Route::get('/a/{token}', [\App\Http\Controllers\Public\AppointmentController::class, 'viewByToken'])->name('public.appointment.view');
Route::post('/a/{token}/cancel', [\App\Http\Controllers\Public\AppointmentController::class, 'cancelByToken'])->name('public.appointment.cancel');

// Публичные роуты для онлайн-записи
Route::prefix('book/{slug}')->name('public.appointments.')->group(function () {
    // Шаг 1: Выбор локации
    Route::get('/', [\App\Http\Controllers\Public\AppointmentController::class, 'show'])->name('show');

    // Шаг 2: Выбор услуги
    Route::get('/location/{locationId}', [\App\Http\Controllers\Public\AppointmentController::class, 'selectLocation'])->name('select-location');

    // Шаг 3: Выбор мастера
    Route::get('/location/{locationId}/service/{serviceId}', [\App\Http\Controllers\Public\AppointmentController::class, 'selectService'])->name('select-service');

    // Шаг 4: Выбор даты и времени
    Route::get('/location/{locationId}/service/{serviceId}/master/{masterId}', [\App\Http\Controllers\Public\AppointmentController::class, 'selectTime'])->name('select-time');

    // Сохранение записи
    Route::post('/store', [\App\Http\Controllers\Public\AppointmentController::class, 'store'])->name('store');

    // Страница успеха
    Route::get('/{token}', [\App\Http\Controllers\Public\AppointmentController::class, 'success'])->name('success');
});

// Business invitations (guest and auth routes)
Route::middleware('guest')->group(function () {
    Route::get('/invite/{token}', [\App\Http\Controllers\Auth\BusinessInvitationController::class, 'accept'])->name('invite.accept');
    Route::post('/invite/{token}/activate', [\App\Http\Controllers\Auth\BusinessInvitationController::class, 'activate'])->name('invite.activate');
});

Route::middleware(['auth'])->group(function () {
    // Welcome/Onboarding page
    Route::middleware(['only.client'])->group(function () {
        Route::get('/welcome', [\App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');
    });

    Route::post('/invite/{token}/accept', [\App\Http\Controllers\Auth\BusinessInvitationController::class, 'store'])->name('invite.store');

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    });

    // === КЛИЕНТСКАЯ ЧАСТЬ ===
    // Для пользователей с доступом к клиентской части
    Route::middleware(['auth', 'only.client'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/refresh', [DashboardController::class, 'refresh'])->name('dashboard.refresh');

        // Аналитика
        Route::middleware(['check.business.permission:client.analytics.view'])->group(function () {
            Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
            Route::get('/analytics/financial', [\App\Http\Controllers\AnalyticsController::class, 'financial'])->name('analytics.financial');
            Route::get('/analytics/general', [\App\Http\Controllers\AnalyticsController::class, 'general'])->name('analytics.general');
        });

        // Клиенты
        Route::middleware(['check.business.permission:client.clients.view'])->group(function () {
            Route::get('/clients', [\App\Http\Controllers\ClientController::class, 'index'])->name('clients.index');
            Route::get('/clients/{client}', [\App\Http\Controllers\ClientController::class, 'show'])->name('clients.show');
        });

        Route::middleware(['check.business.permission:client.clients.create'])->group(function () {
            Route::get('/clients/create', [\App\Http\Controllers\ClientController::class, 'create'])->name('clients.create');
            Route::post('/clients', [\App\Http\Controllers\ClientController::class, 'store'])->name('clients.store');
        });

        Route::middleware(['check.business.permission:client.clients.update'])->group(function () {
            Route::get('/clients/{client}/edit', [\App\Http\Controllers\ClientController::class, 'edit'])->name('clients.edit');
            Route::patch('/clients/{client}', [\App\Http\Controllers\ClientController::class, 'update'])->name('clients.update');
        });

        Route::middleware(['check.business.permission:client.clients.delete'])->group(function () {
            Route::delete('/clients/{client}', [\App\Http\Controllers\ClientController::class, 'destroy'])->name('clients.destroy');
        });

        Route::middleware(['check.business.permission:client.clients.export'])->group(function () {
            Route::get('/clients-export', [\App\Http\Controllers\ClientController::class, 'export'])->name('clients.export');
        });

        // Услуги
        Route::middleware(['check.business.permission:client.services.view'])->group(function () {
            Route::get('/services', [\App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
        });

        Route::middleware(['check.business.permission:client.services.create'])->group(function () {
            Route::get('/services/create', [\App\Http\Controllers\ServiceController::class, 'create'])->name('services.create');
            Route::post('/services', [\App\Http\Controllers\ServiceController::class, 'store'])->name('services.store');
        });

        Route::middleware(['check.business.permission:client.services.update'])->group(function () {
            Route::get('/services/{service}/edit', [\App\Http\Controllers\ServiceController::class, 'edit'])->name('services.edit');
            Route::patch('/services/{service}', [\App\Http\Controllers\ServiceController::class, 'update'])->name('services.update');
        });

        Route::middleware(['check.business.permission:client.services.delete'])->group(function () {
            Route::delete('/services/{service}', [\App\Http\Controllers\ServiceController::class, 'destroy'])->name('services.destroy');
        });

        // Записи
        Route::middleware(['check.business.permission:client.appointments.view'])->group(function () {
            Route::get('/appointments', [\App\Http\Controllers\AppointmentsController::class, 'index'])->name('appointments.index');
            Route::get('/appointments/calendar', [\App\Http\Controllers\AppointmentsController::class, 'calendar'])->name('appointments.calendar');
            Route::get('/appointments/{appointment}', [\App\Http\Controllers\AppointmentsController::class, 'show'])->name('appointments.show');
        });

        Route::middleware(['check.business.permission:client.appointments.create'])->group(function () {
            Route::get('/appointments/create', [\App\Http\Controllers\AppointmentsController::class, 'create'])->name('appointments.create');
            Route::post('/appointments', [\App\Http\Controllers\AppointmentsController::class, 'store'])->name('appointments.store');
        });

        Route::middleware(['check.business.permission:client.appointments.update'])->group(function () {
            Route::get('/appointments/{appointment}/edit', [\App\Http\Controllers\AppointmentsController::class, 'edit'])->name('appointments.edit');
            Route::patch('/appointments/{appointment}', [\App\Http\Controllers\AppointmentsController::class, 'update'])->name('appointments.update');
            Route::patch('/appointments/{appointment}/confirm', [\App\Http\Controllers\AppointmentsController::class, 'confirm'])->name('appointments.confirm');
            Route::patch('/appointments/{appointment}/cancel', [\App\Http\Controllers\AppointmentsController::class, 'cancel'])->name('appointments.cancel');
            Route::patch('/appointments/{appointment}/complete', [\App\Http\Controllers\AppointmentsController::class, 'complete'])->name('appointments.complete');
        });

        Route::middleware(['check.business.permission:client.appointments.delete'])->group(function () {
            Route::delete('/appointments/{appointment}', [\App\Http\Controllers\AppointmentsController::class, 'destroy'])->name('appointments.destroy');
        });

        Route::middleware(['check.business.permission:client.appointments.export'])->group(function () {
            Route::get('/appointments-export', [\App\Http\Controllers\AppointmentsController::class, 'export'])->name('appointments.export');
        });

        // Тикеты (явные роуты без route model binding, используем {id} вместо {ticket})
        // ВАЖНО: Специфичные маршруты (create, edit) должны быть ПЕРЕД динамическими ({id})
        Route::middleware(['check.business.permission:client.tickets.create'])->group(function () {
            Route::get('tickets/create', [\App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
            Route::post('tickets', [\App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
        });

        Route::middleware(['check.business.permission:client.tickets.view'])->group(function () {
            Route::get('tickets', [\App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
            Route::get('tickets/{id}', [\App\Http\Controllers\TicketController::class, 'show'])->name('tickets.show');
        });

        Route::middleware(['check.business.permission:client.tickets.update'])->group(function () {
            Route::get('tickets/{id}/edit', [\App\Http\Controllers\TicketController::class, 'edit'])->name('tickets.edit');
            Route::patch('tickets/{id}', [\App\Http\Controllers\TicketController::class, 'update'])->name('tickets.update');
            Route::post('tickets/{id}/comments', [\App\Http\Controllers\TicketController::class, 'addComment'])->name('tickets.comments.store');
        });

        // Подписки и тарифы
        Route::middleware(['check.business.permission:client.subscription.view'])->group(function () {
            Route::get('/subscription', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscription.index');
            Route::get('/subscription/current', [\App\Http\Controllers\SubscriptionController::class, 'current'])->name('subscription.current');
            Route::get('/subscription/{plan}', [\App\Http\Controllers\SubscriptionController::class, 'show'])->name('subscription.show');
        });

        Route::middleware(['check.business.permission:client.subscription.manage'])->group(function () {
            Route::post('/subscription/{plan}/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
        });

        // Настройки бизнеса
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [BusinessSettingsController::class, 'index'])->name('index');

            // Онлайн-запись
            Route::get('/online-booking', [BusinessSettingsController::class, 'onlineBooking'])->name('online-booking');
            Route::patch('/online-booking', [BusinessSettingsController::class, 'updateOnlineBooking'])->name('online-booking.update');

            // Telegram
            Route::middleware(['check.business.permission:client.telegram.manage'])->group(function () {
                Route::get('/telegram', [TelegramSettingsController::class, 'index'])->name('telegram');
                Route::delete('/telegram/disconnect', [TelegramSettingsController::class, 'disconnect'])->name('telegram.disconnect');
            });

            // Настройки бизнеса (создание доступно без бизнеса)
            Route::get('/business/create', [BusinessSettingsController::class, 'create'])->name('business.create');
            Route::post('/business', [BusinessSettingsController::class, 'store'])->name('business.store');

            Route::middleware(['check.business.permission:client.businesses.update'])->group(function () {
                Route::get('/business', [BusinessSettingsController::class, 'edit'])->name('business.edit');
                Route::patch('/business', [BusinessSettingsController::class, 'update'])->name('business.update');
            });

            // Локации
            Route::middleware(['check.business.permission:client.locations.view'])->group(function () {
                Route::get('/locations', [LocationSettingsController::class, 'index'])->name('locations');
            });

            Route::middleware(['check.business.permission:client.locations.create'])->group(function () {
                Route::get('/locations/create', [LocationSettingsController::class, 'create'])->name('locations.create');
                Route::post('/locations', [LocationSettingsController::class, 'store'])->name('locations.store');
            });

            Route::middleware(['check.business.permission:client.locations.update'])->group(function () {
                Route::get('/locations/{location}/edit', [LocationSettingsController::class, 'edit'])->name('locations.edit');
                Route::patch('/locations/{location}', [LocationSettingsController::class, 'update'])->name('locations.update');
            });

            Route::middleware(['check.business.permission:client.locations.delete'])->group(function () {
                Route::delete('/locations/{location}', [LocationSettingsController::class, 'destroy'])->name('locations.destroy');
            });

            // Мастера
            Route::middleware(['check.business.permission:client.masters.view'])->group(function () {
                Route::get('/masters', [MasterSettingsController::class, 'index'])->name('masters');
            });

            Route::middleware(['check.business.permission:client.masters.create'])->group(function () {
                Route::get('/masters/create', [MasterSettingsController::class, 'create'])->name('masters.create');
                Route::post('/masters', [MasterSettingsController::class, 'store'])->name('masters.store');
            });

            Route::middleware(['check.business.permission:client.masters.update'])->group(function () {
                Route::get('/masters/{master}/edit', [MasterSettingsController::class, 'edit'])->name('masters.edit');
                Route::patch('/masters/{master}', [MasterSettingsController::class, 'update'])->name('masters.update');
            });

            Route::middleware(['check.business.permission:client.masters.delete'])->group(function () {
                Route::delete('/masters/{master}', [MasterSettingsController::class, 'destroy'])->name('masters.destroy');
            });

            // Пользователи бизнеса
            Route::middleware(['check.business.permission:client.business.users.view'])->group(function () {
                Route::get('/users', [\App\Http\Controllers\Settings\BusinessUsersController::class, 'index'])->name('users.index');
            });

            Route::middleware(['check.business.permission:client.business.users.create'])->group(function () {
                Route::get('/users/create', [\App\Http\Controllers\Settings\BusinessUsersController::class, 'create'])->name('users.create');
                Route::post('/users/invite', [\App\Http\Controllers\Settings\BusinessUsersController::class, 'storeInvitation'])->name('users.invite');
                Route::post('/users/manual', [\App\Http\Controllers\Settings\BusinessUsersController::class, 'storeManual'])->name('users.manual');
                Route::post('/users/{invitation}/resend', [\App\Http\Controllers\Settings\BusinessUsersController::class, 'resendInvitation'])->name('users.resend');
            });

            Route::middleware(['check.business.permission:client.business.users.update'])->group(function () {
                Route::get('/users/{user}/edit', [\App\Http\Controllers\Settings\BusinessUsersController::class, 'edit'])->name('users.edit');
                Route::patch('/users/{user}', [\App\Http\Controllers\Settings\BusinessUsersController::class, 'update'])->name('users.update');
            });

            Route::middleware(['check.business.permission:client.business.users.delete'])->group(function () {
                Route::delete('/users/{user}', [\App\Http\Controllers\Settings\BusinessUsersController::class, 'destroy'])->name('users.destroy');
            });

            // Управление правами ролей бизнеса
            Route::middleware(['check.business.permission:client.business.roles.manage'])->group(function () {
                Route::get('/roles', [\App\Http\Controllers\Settings\BusinessRolePermissionsController::class, 'index'])->name('roles.index');
                Route::get('/roles/create', [\App\Http\Controllers\Settings\BusinessRolePermissionsController::class, 'create'])->name('roles.create');
                Route::post('/roles', [\App\Http\Controllers\Settings\BusinessRolePermissionsController::class, 'store'])->name('roles.store');
                Route::get('/roles/{role}', [\App\Http\Controllers\Settings\BusinessRolePermissionsController::class, 'show'])->name('roles.show');
                Route::put('/roles/{role}', [\App\Http\Controllers\Settings\BusinessRolePermissionsController::class, 'update'])->name('roles.update');
                Route::delete('/roles/{role}', [\App\Http\Controllers\Settings\BusinessRolePermissionsController::class, 'destroy'])->name('roles.destroy');
            });
        });
    });

    // === АДМИНСКАЯ ПАНЕЛЬ ===
    // Для пользователей с доступом к админке
    Route::middleware(['auth', 'only.panel'])->prefix('panel')->name('panel.')->group(function () {
        // Главная панели
        Route::get('/', [\App\Http\Controllers\Panel\PanelController::class, 'index'])->name('index');
        Route::post('/refresh', [\App\Http\Controllers\Panel\PanelController::class, 'refresh'])->name('refresh');

        // Пользователи (только админ)
        Route::middleware(['check.permission:panel.users.view'])->group(function () {
            Route::get('/users', [\App\Http\Controllers\Panel\UserController::class, 'index'])->name('users');
        });

        Route::middleware(['check.permission:panel.users.create'])->group(function () {
            Route::get('/users/create', [\App\Http\Controllers\Panel\UserController::class, 'create'])->name('users.create');
            Route::post('/users', [\App\Http\Controllers\Panel\UserController::class, 'store'])->name('users.store');
        });

        Route::middleware(['check.permission:panel.users.update'])->group(function () {
            Route::get('/users/{user}/edit', [\App\Http\Controllers\Panel\UserController::class, 'edit'])->name('users.edit');
            Route::patch('/users/{user}', [\App\Http\Controllers\Panel\UserController::class, 'update'])->name('users.update');
        });

        Route::middleware(['check.permission:panel.users.delete'])->group(function () {
            Route::delete('/users/{user}', [\App\Http\Controllers\Panel\UserController::class, 'destroy'])->name('users.destroy');
        });

        // Роли (только админ)
        Route::middleware(['check.permission:panel.roles.view'])->group(function () {
            Route::get('/roles', [\App\Http\Controllers\Panel\RoleController::class, 'index'])->name('roles');
        });

        Route::middleware(['check.permission:panel.roles.create'])->group(function () {
            Route::get('/roles/create', [\App\Http\Controllers\Panel\RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [\App\Http\Controllers\Panel\RoleController::class, 'store'])->name('roles.store');
        });

        Route::middleware(['check.permission:panel.roles.update'])->group(function () {
            Route::get('/roles/{role}/edit', [\App\Http\Controllers\Panel\RoleController::class, 'edit'])->name('roles.edit');
            Route::patch('/roles/{role}', [\App\Http\Controllers\Panel\RoleController::class, 'update'])->name('roles.update');
        });

        Route::middleware(['check.permission:panel.roles.delete'])->group(function () {
            Route::delete('/roles/{role}', [\App\Http\Controllers\Panel\RoleController::class, 'destroy'])->name('roles.destroy');
        });

        // Права доступа (только админ)
        Route::middleware(['check.permission:panel.permissions.view'])->group(function () {
            Route::get('/permissions', [\App\Http\Controllers\Panel\PermissionController::class, 'index'])->name('permissions');
        });

        Route::middleware(['check.permission:panel.permissions.create'])->group(function () {
            Route::get('/permissions/create', [\App\Http\Controllers\Panel\PermissionController::class, 'create'])->name('permissions.create');
            Route::post('/permissions', [\App\Http\Controllers\Panel\PermissionController::class, 'store'])->name('permissions.store');
        });

        Route::middleware(['check.permission:panel.permissions.update'])->group(function () {
            Route::get('/permissions/{permission}/edit', [\App\Http\Controllers\Panel\PermissionController::class, 'edit'])->name('permissions.edit');
            Route::patch('/permissions/{permission}', [\App\Http\Controllers\Panel\PermissionController::class, 'update'])->name('permissions.update');
        });

        Route::middleware(['check.permission:panel.permissions.delete'])->group(function () {
            Route::delete('/permissions/{permission}', [\App\Http\Controllers\Panel\PermissionController::class, 'destroy'])->name('permissions.destroy');
        });

        // Бизнесы (админ и менеджер)
        Route::middleware(['check.permission:panel.businesses.view'])->group(function () {
            Route::get('/businesses', [\App\Http\Controllers\Panel\BusinessController::class, 'index'])->name('businesses');
            Route::get('/businesses/{business}', [\App\Http\Controllers\Panel\BusinessController::class, 'show'])->name('businesses.show');
        });

        Route::middleware(['check.permission:panel.businesses.update'])->group(function () {
            Route::get('/businesses/{business}/edit', [\App\Http\Controllers\Panel\BusinessController::class, 'edit'])->name('businesses.edit');
            Route::patch('/businesses/{business}', [\App\Http\Controllers\Panel\BusinessController::class, 'update'])->name('businesses.update');
        });

        Route::middleware(['check.permission:panel.businesses.delete'])->group(function () {
            Route::delete('/businesses/{business}', [\App\Http\Controllers\Panel\BusinessController::class, 'destroy'])->name('businesses.destroy');
        });

        // Записи (админ и менеджер)
        Route::middleware(['check.permission:panel.appointments.view'])->group(function () {
            Route::get('/appointments', [\App\Http\Controllers\Panel\AppointmentController::class, 'index'])->name('appointments');
        });

        Route::middleware(['check.permission:panel.appointments.update'])->group(function () {
            Route::get('/appointments/{appointment}/edit', [\App\Http\Controllers\Panel\AppointmentController::class, 'edit'])->name('appointments.edit');
            Route::patch('/appointments/{appointment}', [\App\Http\Controllers\Panel\AppointmentController::class, 'update'])->name('appointments.update');
        });

        Route::middleware(['check.permission:panel.appointments.delete'])->group(function () {
            Route::delete('/appointments/{appointment}', [\App\Http\Controllers\Panel\AppointmentController::class, 'destroy'])->name('appointments.destroy');
        });

        // Клиенты (админ и менеджер)
        Route::middleware(['check.permission:panel.clients.view'])->group(function () {
            Route::get('/clients', [\App\Http\Controllers\Panel\ClientController::class, 'index'])->name('clients');
        });

        Route::middleware(['check.permission:panel.clients.create'])->group(function () {
            Route::get('/clients/create', [\App\Http\Controllers\Panel\ClientController::class, 'create'])->name('clients.create');
            Route::post('/clients', [\App\Http\Controllers\Panel\ClientController::class, 'store'])->name('clients.store');
        });

        Route::middleware(['check.permission:panel.clients.update'])->group(function () {
            Route::get('/clients/{client}/edit', [\App\Http\Controllers\Panel\ClientController::class, 'edit'])->name('clients.edit');
            Route::patch('/clients/{client}', [\App\Http\Controllers\Panel\ClientController::class, 'update'])->name('clients.update');
        });

        Route::middleware(['check.permission:panel.clients.delete'])->group(function () {
            Route::delete('/clients/{client}', [\App\Http\Controllers\Panel\ClientController::class, 'destroy'])->name('clients.destroy');
        });

        // Услуги (админ и менеджер)
        Route::middleware(['check.permission:panel.services.view'])->group(function () {
            Route::get('/services', [\App\Http\Controllers\Panel\ServiceController::class, 'index'])->name('services');
            Route::get('/services/{service}', [\App\Http\Controllers\Panel\ServiceController::class, 'show'])->name('services.show');
        });

        Route::middleware(['check.permission:panel.services.update'])->group(function () {
            Route::get('/services/{service}/edit', [\App\Http\Controllers\Panel\ServiceController::class, 'edit'])->name('services.edit');
            Route::patch('/services/{service}', [\App\Http\Controllers\Panel\ServiceController::class, 'update'])->name('services.update');
        });

        Route::middleware(['check.permission:panel.services.delete'])->group(function () {
            Route::delete('/services/{service}', [\App\Http\Controllers\Panel\ServiceController::class, 'destroy'])->name('services.destroy');
        });

        // Локации (админ и менеджер)
        Route::middleware(['check.permission:panel.locations.view'])->group(function () {
            Route::get('/locations', [\App\Http\Controllers\Panel\LocationController::class, 'index'])->name('locations');
            Route::get('/locations/{location}', [\App\Http\Controllers\Panel\LocationController::class, 'show'])->name('locations.show');
        });

        Route::middleware(['check.permission:panel.locations.update'])->group(function () {
            Route::get('/locations/{location}/edit', [\App\Http\Controllers\Panel\LocationController::class, 'edit'])->name('locations.edit');
            Route::patch('/locations/{location}', [\App\Http\Controllers\Panel\LocationController::class, 'update'])->name('locations.update');
        });

        Route::middleware(['check.permission:panel.locations.delete'])->group(function () {
            Route::delete('/locations/{location}', [\App\Http\Controllers\Panel\LocationController::class, 'destroy'])->name('locations.destroy');
        });

        // Мастера (админ и менеджер)
        Route::middleware(['check.permission:panel.masters.view'])->group(function () {
            Route::get('/masters', [\App\Http\Controllers\Panel\MasterController::class, 'index'])->name('masters');
            Route::get('/masters/{master}', [\App\Http\Controllers\Panel\MasterController::class, 'show'])->name('masters.show');
        });

        Route::middleware(['check.permission:panel.masters.update'])->group(function () {
            Route::get('/masters/{master}/edit', [\App\Http\Controllers\Panel\MasterController::class, 'edit'])->name('masters.edit');
            Route::patch('/masters/{master}', [\App\Http\Controllers\Panel\MasterController::class, 'update'])->name('masters.update');
        });

        Route::middleware(['check.permission:panel.masters.delete'])->group(function () {
            Route::delete('/masters/{master}', [\App\Http\Controllers\Panel\MasterController::class, 'destroy'])->name('masters.destroy');
        });

        // Аналитика (админ, менеджер, поддержка)
        Route::middleware(['check.permission:panel.analytics.view'])->group(function () {
            Route::get('/analytics', [\App\Http\Controllers\Panel\AnalyticsController::class, 'index'])->name('analytics');
        });

        // Поддержка (админ и поддержка)
        Route::middleware(['check.permission:panel.support.view'])->group(function () {
            Route::get('/support', [\App\Http\Controllers\Panel\SupportController::class, 'index'])->name('support');
        });

        // Настройки тикетов (админ) - должно быть ПЕРЕД resource роутом тикетов
        Route::middleware(['check.permission:panel.tickets.settings'])->group(function () {
            Route::get('/tickets/settings', [\App\Http\Controllers\Settings\TicketSettingsController::class, 'index'])->name('tickets.settings');
            Route::patch('/tickets/settings', [\App\Http\Controllers\Settings\TicketSettingsController::class, 'update'])->name('tickets.settings.update');
        });

        // Тикеты (админ, менеджер, поддержка)
        Route::middleware(['check.permission:panel.tickets.view'])->group(function () {
            Route::get('/tickets', [\App\Http\Controllers\Panel\TicketController::class, 'index'])->name('tickets');
            Route::get('/tickets/{ticket}', [\App\Http\Controllers\Panel\TicketController::class, 'show'])->name('tickets.show');
        });

        Route::middleware(['check.permission:panel.tickets.update'])->group(function () {
            Route::get('/tickets/{ticket}/edit', [\App\Http\Controllers\Panel\TicketController::class, 'edit'])->name('tickets.edit');
            Route::patch('/tickets/{ticket}', [\App\Http\Controllers\Panel\TicketController::class, 'update'])->name('tickets.update');
            Route::post('/tickets/{ticket}/assign', [\App\Http\Controllers\Panel\TicketController::class, 'assign'])->name('tickets.assign');
            Route::post('/tickets/{ticket}/status', [\App\Http\Controllers\Panel\TicketController::class, 'updateStatus'])->name('tickets.status');
            Route::post('/tickets/{ticket}/comments', [\App\Http\Controllers\Panel\TicketController::class, 'addComment'])->name('tickets.comments.store');
        });

        Route::middleware(['check.permission:panel.tickets.delete'])->group(function () {
            Route::delete('/tickets/{ticket}', [\App\Http\Controllers\Panel\TicketController::class, 'destroy'])->name('tickets.destroy');
        });

        // Категории тикетов (админ)
        Route::middleware(['check.permission:panel.tickets.categories.manage'])->group(function () {
            Route::resource('ticket-categories', \App\Http\Controllers\Panel\TicketCategoryController::class);
        });

        // Управление Telegram ботом
        Route::middleware(['check.permission:panel.telegram.manage'])->group(function () {
            Route::get('/telegram-management', [TelegramManagementController::class, 'index'])->name('telegram.management');
            Route::get('/telegram-management/create', [TelegramManagementController::class, 'create'])->name('telegram.management.create');
            Route::post('/telegram-management', [TelegramManagementController::class, 'store'])->name('telegram.management.store');
            Route::get('/telegram-management/{bot}/edit', [TelegramManagementController::class, 'edit'])->name('telegram.management.edit');
            Route::patch('/telegram-management/{bot}', [TelegramManagementController::class, 'update'])->name('telegram.management.update');
            Route::delete('/telegram-management/{bot}', [TelegramManagementController::class, 'destroy'])->name('telegram.management.destroy');
            Route::post('/telegram-management/{bot}/set-webhook', [TelegramManagementController::class, 'setWebhook'])->name('telegram.management.set-webhook');
            Route::post('/telegram-management/{bot}/delete-webhook', [TelegramManagementController::class, 'deleteWebhook'])->name('telegram.management.delete-webhook');
            Route::post('/telegram-management/{bot}/bot-info', [TelegramManagementController::class, 'getBotInfo'])->name('telegram.management.bot-info');
        });

        // Тарифы (админ)
        Route::middleware(['check.permission:panel.plans.view'])->group(function () {
            Route::get('/plans', [\App\Http\Controllers\Panel\PlanController::class, 'index'])->name('plans.index');
        });

        Route::middleware(['check.permission:panel.plans.create'])->group(function () {
            Route::get('/plans/create', [\App\Http\Controllers\Panel\PlanController::class, 'create'])->name('plans.create');
            Route::post('/plans', [\App\Http\Controllers\Panel\PlanController::class, 'store'])->name('plans.store');
        });

        Route::middleware(['check.permission:panel.plans.update'])->group(function () {
            Route::get('/plans/{plan}/edit', [\App\Http\Controllers\Panel\PlanController::class, 'edit'])->name('plans.edit');
            Route::patch('/plans/{plan}', [\App\Http\Controllers\Panel\PlanController::class, 'update'])->name('plans.update');
            Route::post('/plans/{plan}/increment-sort', [\App\Http\Controllers\Panel\PlanController::class, 'incrementSortOrder'])->name('plans.increment-sort');
            Route::post('/plans/{plan}/decrement-sort', [\App\Http\Controllers\Panel\PlanController::class, 'decrementSortOrder'])->name('plans.decrement-sort');
            Route::post('/plans/reorder', [\App\Http\Controllers\Panel\PlanController::class, 'reorder'])->name('plans.reorder');
        });

        Route::middleware(['check.permission:panel.plans.delete'])->group(function () {
            Route::delete('/plans/{plan}', [\App\Http\Controllers\Panel\PlanController::class, 'destroy'])->name('plans.destroy');
        });

        // Свойства тарифов
        Route::middleware(['check.permission:panel.plans.view'])->group(function () {
            Route::get('/plans/properties', [\App\Http\Controllers\Panel\SubscriptionMetricController::class, 'index'])->name('plans.properties.index');
        });

        Route::middleware(['check.permission:panel.plans.create'])->group(function () {
            Route::get('/plans/properties/create', [\App\Http\Controllers\Panel\SubscriptionMetricController::class, 'create'])->name('plans.properties.create');
            Route::post('/plans/properties', [\App\Http\Controllers\Panel\SubscriptionMetricController::class, 'store'])->name('plans.properties.store');
        });

        Route::middleware(['check.permission:panel.plans.update'])->group(function () {
            Route::get('/plans/properties/{metric}/edit', [\App\Http\Controllers\Panel\SubscriptionMetricController::class, 'edit'])->name('plans.properties.edit');
            Route::patch('/plans/properties/{metric}', [\App\Http\Controllers\Panel\SubscriptionMetricController::class, 'update'])->name('plans.properties.update');
            Route::post('/plans/properties/{metric}/increment-sort', [\App\Http\Controllers\Panel\SubscriptionMetricController::class, 'incrementSortOrder'])->name('plans.properties.increment-sort');
            Route::post('/plans/properties/{metric}/decrement-sort', [\App\Http\Controllers\Panel\SubscriptionMetricController::class, 'decrementSortOrder'])->name('plans.properties.decrement-sort');
            Route::post('/plans/properties/reorder', [\App\Http\Controllers\Panel\SubscriptionMetricController::class, 'reorder'])->name('plans.properties.reorder');
        });

        Route::middleware(['check.permission:panel.plans.delete'])->group(function () {
            Route::delete('/plans/properties/{metric}', [\App\Http\Controllers\Panel\SubscriptionMetricController::class, 'destroy'])->name('plans.properties.destroy');
        });

        // Управление базовыми правами ролей бизнеса
        Route::middleware(['check.permission:panel.business.roles.manage'])->group(function () {
            Route::get('/business-roles', [\App\Http\Controllers\Panel\BusinessRolePermissionsController::class, 'index'])->name('business-roles.index');
            Route::get('/business-roles/create', [\App\Http\Controllers\Panel\BusinessRolePermissionsController::class, 'create'])->name('business-roles.create');
            Route::post('/business-roles', [\App\Http\Controllers\Panel\BusinessRolePermissionsController::class, 'store'])->name('business-roles.store');
            Route::get('/business-roles/{role}', [\App\Http\Controllers\Panel\BusinessRolePermissionsController::class, 'show'])->name('business-roles.show');
            Route::put('/business-roles/{role}', [\App\Http\Controllers\Panel\BusinessRolePermissionsController::class, 'update'])->name('business-roles.update');
            Route::delete('/business-roles/{role}', [\App\Http\Controllers\Panel\BusinessRolePermissionsController::class, 'destroy'])->name('business-roles.destroy');
        });
    });
});

require __DIR__ . '/auth.php';
