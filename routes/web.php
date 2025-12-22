<?php

use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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

    Route::middleware(['auth', 'onboarded'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('clients', \App\Http\Controllers\ClientController::class);
        Route::resource('services', \App\Http\Controllers\ServiceController::class);

        // Настройки бизнеса
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [BusinessSettingsController::class, 'index'])->name('index');
            
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
});

require __DIR__.'/auth.php';
