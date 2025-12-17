<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
        Route::get('/password/change', [PasswordController::class, 'edit'])->name('password.edit');
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
    });
});

require __DIR__.'/auth.php';
