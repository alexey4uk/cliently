<?php

use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Settings\DashboardSettingsController;
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

    Route::prefix('onboarding')->name('onboarding.')->middleware(['auth', 'no.onboarded'])->group(function () {
        Route::get('business', [OnboardingController::class, 'business'])->name('business');
        Route::post('business', [OnboardingController::class, 'storeBusiness'])->name('business.store');

        Route::get('location', [OnboardingController::class, 'location'])->name('location');
        Route::post('location', [OnboardingController::class, 'storeLocation'])->name('location.store');

        Route::get('service', [OnboardingController::class, 'service'])->name('service');
        Route::post('service', [OnboardingController::class, 'storeService'])->name('service.store');

        Route::get('master', [OnboardingController::class, 'master'])->name('master');
        Route::post('master', [OnboardingController::class, 'storeMaster'])->name('master.store');

        Route::get('complete', [OnboardingController::class, 'complete'])->name('complete');
    });

    // === КЛИЕНТСКАЯ ЧАСТЬ ===
    // Для пользователей с доступом к клиентской части
    Route::middleware(['auth', 'onboarded', 'only.client'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/refresh', [DashboardController::class, 'refresh'])->name('dashboard.refresh');
        Route::resource('clients', \App\Http\Controllers\ClientController::class);
        Route::resource('services', \App\Http\Controllers\ServiceController::class);
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
            Route::get('/business', [BusinessSettingsController::class, 'edit'])->name('business.edit');
            Route::patch('/business', [BusinessSettingsController::class, 'update'])->name('business.update');

            // Локации
            Route::get('/locations', [BusinessSettingsController::class, 'locations'])->name('locations');
            Route::get('/locations/create', [BusinessSettingsController::class, 'createLocation'])->name('locations.create');
            Route::post('/locations', [BusinessSettingsController::class, 'storeLocation'])->name('locations.store');
            Route::get('/locations/{location}/edit', [BusinessSettingsController::class, 'editLocation'])->name('locations.edit');
            Route::patch('/locations/{location}', [BusinessSettingsController::class, 'updateLocation'])->name('locations.update');
            Route::delete('/locations/{location}', [BusinessSettingsController::class, 'destroyLocation'])->name('locations.destroy');

            // Мастера
            Route::get('/masters', [BusinessSettingsController::class, 'masters'])->name('masters');
            Route::get('/masters/create', [BusinessSettingsController::class, 'createMaster'])->name('masters.create');
            Route::post('/masters', [BusinessSettingsController::class, 'storeMaster'])->name('masters.store');
            Route::get('/masters/{master}/edit', [BusinessSettingsController::class, 'editMaster'])->name('masters.edit');
            Route::patch('/masters/{master}', [BusinessSettingsController::class, 'updateMaster'])->name('masters.update');
            Route::delete('/masters/{master}', [BusinessSettingsController::class, 'destroyMaster'])->name('masters.destroy');
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
            Route::delete('/permissions/{permission}', [\App\Http\Controllers\Panel\PermissionController::class, 'destroy'])->name('permissions.destroy');
        });

        // Бизнесы (админ и менеджер)
        Route::middleware(['check.permission:businesses.view'])->group(function () {
            Route::get('/businesses', [\App\Http\Controllers\Panel\BusinessController::class, 'index'])->name('businesses');
        });

        // Записи (админ и менеджер)
        Route::middleware(['check.permission:appointments.view'])->group(function () {
            Route::get('/appointments', [\App\Http\Controllers\Panel\AppointmentController::class, 'index'])->name('appointments');
        });

        // Клиенты (админ и менеджер)
        Route::middleware(['check.permission:clients.view'])->group(function () {
            Route::get('/clients', [\App\Http\Controllers\Panel\ClientController::class, 'index'])->name('clients');
        });

        // Услуги (админ и менеджер)
        Route::middleware(['check.permission:services.view'])->group(function () {
            Route::get('/services', [\App\Http\Controllers\Panel\ServiceController::class, 'index'])->name('services');
        });

        // Аналитика (админ, менеджер, поддержка)
        Route::middleware(['check.permission:analytics.view'])->group(function () {
            Route::get('/analytics', [\App\Http\Controllers\Panel\AnalyticsController::class, 'index'])->name('analytics');
        });

        // Поддержка (админ и поддержка)
        Route::middleware(['check.permission:support.view'])->group(function () {
            Route::get('/support', [\App\Http\Controllers\Panel\SupportController::class, 'index'])->name('support');
        });
    });
});

require __DIR__.'/auth.php';
