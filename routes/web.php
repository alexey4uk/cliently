<?php

use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Settings\DashboardSettingsController;
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

Route::middleware(['auth'])->group(function () {

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
        Route::resource('clients', \App\Http\Controllers\ClientController::class);
        Route::get('clients-export', [\App\Http\Controllers\ClientController::class, 'export'])->name('clients.export');
        Route::resource('services', \App\Http\Controllers\ServiceController::class);
        Route::get('appointments-export', [\App\Http\Controllers\AppointmentsController::class, 'export'])->name('appointments.export');
        Route::get('appointments/calendar', [\App\Http\Controllers\AppointmentsController::class, 'calendar'])->name('appointments.calendar');
        Route::resource('appointments', \App\Http\Controllers\AppointmentsController::class);
        Route::patch('appointments/{appointment}/confirm', [\App\Http\Controllers\AppointmentsController::class, 'confirm'])->name('appointments.confirm');
        Route::patch('appointments/{appointment}/cancel', [\App\Http\Controllers\AppointmentsController::class, 'cancel'])->name('appointments.cancel');
        Route::patch('appointments/{appointment}/complete', [\App\Http\Controllers\AppointmentsController::class, 'complete'])->name('appointments.complete');

        // Настройки бизнеса
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [BusinessSettingsController::class, 'index'])->name('index');

            // Dashboard settings
            Route::get('/dashboard', [DashboardSettingsController::class, 'index'])->name('dashboard');
            Route::post('/dashboard', [DashboardSettingsController::class, 'update'])->name('dashboard.update');

            // Онлайн-запись
            Route::get('/online-booking', [BusinessSettingsController::class, 'onlineBooking'])->name('online-booking');
            Route::patch('/online-booking', [BusinessSettingsController::class, 'updateOnlineBooking'])->name('online-booking.update');

            // Telegram
            Route::get('/telegram', [TelegramSettingsController::class, 'index'])->name('telegram');
            Route::delete('/telegram/disconnect', [TelegramSettingsController::class, 'disconnect'])->name('telegram.disconnect');

            // Настройки бизнеса
            Route::get('/business/create', [BusinessSettingsController::class, 'create'])->name('business.create');
            Route::post('/business', [BusinessSettingsController::class, 'store'])->name('business.store');
            Route::get('/business', [BusinessSettingsController::class, 'edit'])->name('business.edit');
            Route::patch('/business', [BusinessSettingsController::class, 'update'])->name('business.update');

            // Локации
            Route::get('/locations', [LocationSettingsController::class, 'index'])->name('locations');
            Route::get('/locations/create', [LocationSettingsController::class, 'create'])->name('locations.create');
            Route::post('/locations', [LocationSettingsController::class, 'store'])->name('locations.store');
            Route::get('/locations/{location}/edit', [LocationSettingsController::class, 'edit'])->name('locations.edit');
            Route::patch('/locations/{location}', [LocationSettingsController::class, 'update'])->name('locations.update');
            Route::delete('/locations/{location}', [LocationSettingsController::class, 'destroy'])->name('locations.destroy');

            // Мастера
            Route::get('/masters', [MasterSettingsController::class, 'index'])->name('masters');
            Route::get('/masters/create', [MasterSettingsController::class, 'create'])->name('masters.create');
            Route::post('/masters', [MasterSettingsController::class, 'store'])->name('masters.store');
            Route::get('/masters/{master}/edit', [MasterSettingsController::class, 'edit'])->name('masters.edit');
            Route::patch('/masters/{master}', [MasterSettingsController::class, 'update'])->name('masters.update');
            Route::delete('/masters/{master}', [MasterSettingsController::class, 'destroy'])->name('masters.destroy');
        });
    });

    // === АДМИНСКАЯ ПАНЕЛЬ ===
    // Для пользователей с доступом к админке
    Route::middleware(['auth', 'only.panel'])->prefix('panel')->name('panel.')->group(function () {
        // Главная панели
        Route::get('/', [\App\Http\Controllers\Panel\PanelController::class, 'index'])->name('index');

        // Пользователи (только админ)
        Route::middleware(['check.permission:users.view'])->group(function () {
            Route::get('/users', [\App\Http\Controllers\Panel\UserController::class, 'index'])->name('users');
            Route::get('/users/create', [\App\Http\Controllers\Panel\UserController::class, 'create'])->name('users.create');
            Route::post('/users', [\App\Http\Controllers\Panel\UserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [\App\Http\Controllers\Panel\UserController::class, 'edit'])->name('users.edit');
            Route::patch('/users/{user}', [\App\Http\Controllers\Panel\UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [\App\Http\Controllers\Panel\UserController::class, 'destroy'])->name('users.destroy');
        });

        // Роли (только админ)
        Route::middleware(['check.permission:roles.view'])->group(function () {
            Route::get('/roles', [\App\Http\Controllers\Panel\RoleController::class, 'index'])->name('roles');
            Route::get('/roles/create', [\App\Http\Controllers\Panel\RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [\App\Http\Controllers\Panel\RoleController::class, 'store'])->name('roles.store');
            Route::get('/roles/{role}/edit', [\App\Http\Controllers\Panel\RoleController::class, 'edit'])->name('roles.edit');
            Route::patch('/roles/{role}', [\App\Http\Controllers\Panel\RoleController::class, 'update'])->name('roles.update');
            Route::delete('/roles/{role}', [\App\Http\Controllers\Panel\RoleController::class, 'destroy'])->name('roles.destroy');
        });

        // Права доступа (только админ)
        Route::middleware(['check.permission:roles.view'])->group(function () {
            Route::get('/permissions', [\App\Http\Controllers\Panel\PermissionController::class, 'index'])->name('permissions');
            Route::get('/permissions/create', [\App\Http\Controllers\Panel\PermissionController::class, 'create'])->name('permissions.create');
            Route::post('/permissions', [\App\Http\Controllers\Panel\PermissionController::class, 'store'])->name('permissions.store');
            Route::get('/permissions/{permission}/edit', [\App\Http\Controllers\Panel\PermissionController::class, 'edit'])->name('permissions.edit');
            Route::patch('/permissions/{permission}', [\App\Http\Controllers\Panel\PermissionController::class, 'update'])->name('permissions.update');
            Route::delete('/permissions/{permission}', [\App\Http\Controllers\Panel\PermissionController::class, 'destroy'])->name('permissions.destroy');
        });

        // Бизнесы (админ и менеджер)
        Route::middleware(['check.permission:businesses.view'])->group(function () {
            Route::get('/businesses', [\App\Http\Controllers\Panel\BusinessController::class, 'index'])->name('businesses');
            Route::get('/businesses/{business}', [\App\Http\Controllers\Panel\BusinessController::class, 'show'])->name('businesses.show');
        });

        Route::middleware(['check.permission:businesses.update'])->group(function () {
            Route::get('/businesses/{business}/edit', [\App\Http\Controllers\Panel\BusinessController::class, 'edit'])->name('businesses.edit');
            Route::patch('/businesses/{business}', [\App\Http\Controllers\Panel\BusinessController::class, 'update'])->name('businesses.update');
        });

        Route::middleware(['check.permission:businesses.delete'])->group(function () {
            Route::delete('/businesses/{business}', [\App\Http\Controllers\Panel\BusinessController::class, 'destroy'])->name('businesses.destroy');
        });

        // Записи (админ и менеджер)
        Route::middleware(['check.permission:appointments.view'])->group(function () {
            Route::get('/appointments', [\App\Http\Controllers\Panel\AppointmentController::class, 'index'])->name('appointments');
        });

        Route::middleware(['check.permission:appointments.update'])->group(function () {
            Route::get('/appointments/{appointment}/edit', [\App\Http\Controllers\Panel\AppointmentController::class, 'edit'])->name('appointments.edit');
            Route::patch('/appointments/{appointment}', [\App\Http\Controllers\Panel\AppointmentController::class, 'update'])->name('appointments.update');
        });

        Route::middleware(['check.permission:appointments.delete'])->group(function () {
            Route::delete('/appointments/{appointment}', [\App\Http\Controllers\Panel\AppointmentController::class, 'destroy'])->name('appointments.destroy');
        });

        // Клиенты (админ и менеджер)
        Route::middleware(['check.permission:clients.view'])->group(function () {
            Route::get('/clients', [\App\Http\Controllers\Panel\ClientController::class, 'index'])->name('clients');
        });

        Route::middleware(['check.permission:clients.create'])->group(function () {
            Route::get('/clients/create', [\App\Http\Controllers\Panel\ClientController::class, 'create'])->name('clients.create');
            Route::post('/clients', [\App\Http\Controllers\Panel\ClientController::class, 'store'])->name('clients.store');
        });

        Route::middleware(['check.permission:clients.update'])->group(function () {
            Route::get('/clients/{client}/edit', [\App\Http\Controllers\Panel\ClientController::class, 'edit'])->name('clients.edit');
            Route::patch('/clients/{client}', [\App\Http\Controllers\Panel\ClientController::class, 'update'])->name('clients.update');
        });

        Route::middleware(['check.permission:clients.delete'])->group(function () {
            Route::delete('/clients/{client}', [\App\Http\Controllers\Panel\ClientController::class, 'destroy'])->name('clients.destroy');
        });

        // Услуги (админ и менеджер)
        Route::middleware(['check.permission:services.view'])->group(function () {
            Route::get('/services', [\App\Http\Controllers\Panel\ServiceController::class, 'index'])->name('services');
            Route::get('/services/{service}', [\App\Http\Controllers\Panel\ServiceController::class, 'show'])->name('services.show');
        });

        Route::middleware(['check.permission:services.update'])->group(function () {
            Route::get('/services/{service}/edit', [\App\Http\Controllers\Panel\ServiceController::class, 'edit'])->name('services.edit');
            Route::patch('/services/{service}', [\App\Http\Controllers\Panel\ServiceController::class, 'update'])->name('services.update');
        });

        Route::middleware(['check.permission:services.delete'])->group(function () {
            Route::delete('/services/{service}', [\App\Http\Controllers\Panel\ServiceController::class, 'destroy'])->name('services.destroy');
        });

        // Локации (админ и менеджер)
        Route::middleware(['check.permission:locations.view'])->group(function () {
            Route::get('/locations', [\App\Http\Controllers\Panel\LocationController::class, 'index'])->name('locations');
            Route::get('/locations/{location}', [\App\Http\Controllers\Panel\LocationController::class, 'show'])->name('locations.show');
        });

        Route::middleware(['check.permission:locations.update'])->group(function () {
            Route::get('/locations/{location}/edit', [\App\Http\Controllers\Panel\LocationController::class, 'edit'])->name('locations.edit');
            Route::patch('/locations/{location}', [\App\Http\Controllers\Panel\LocationController::class, 'update'])->name('locations.update');
        });

        Route::middleware(['check.permission:locations.delete'])->group(function () {
            Route::delete('/locations/{location}', [\App\Http\Controllers\Panel\LocationController::class, 'destroy'])->name('locations.destroy');
        });

        // Мастера (админ и менеджер)
        Route::middleware(['check.permission:masters.view'])->group(function () {
            Route::get('/masters', [\App\Http\Controllers\Panel\MasterController::class, 'index'])->name('masters');
            Route::get('/masters/{master}', [\App\Http\Controllers\Panel\MasterController::class, 'show'])->name('masters.show');
        });

        Route::middleware(['check.permission:masters.update'])->group(function () {
            Route::get('/masters/{master}/edit', [\App\Http\Controllers\Panel\MasterController::class, 'edit'])->name('masters.edit');
            Route::patch('/masters/{master}', [\App\Http\Controllers\Panel\MasterController::class, 'update'])->name('masters.update');
        });

        Route::middleware(['check.permission:masters.delete'])->group(function () {
            Route::delete('/masters/{master}', [\App\Http\Controllers\Panel\MasterController::class, 'destroy'])->name('masters.destroy');
        });

        // Аналитика (админ, менеджер, поддержка)
        Route::middleware(['check.permission:analytics.view'])->group(function () {
            Route::get('/analytics', [\App\Http\Controllers\Panel\AnalyticsController::class, 'index'])->name('analytics');
        });

        // Поддержка (админ и поддержка)
        Route::middleware(['check.permission:support.view'])->group(function () {
            Route::get('/support', [\App\Http\Controllers\Panel\SupportController::class, 'index'])->name('support');
        });

        // Управление Telegram ботом
        Route::middleware(['check.permission:telegram.manage'])->group(function () {
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
    });
});

require __DIR__ . '/auth.php';
