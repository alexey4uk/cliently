<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/slug/check', \App\Http\Controllers\Api\SlugCheckController::class)
    ->middleware('throttle:20,1')
    ->name('api.slug.check');

// API для получения доступных слотов (требует авторизации)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/appointments/available-slots', [\App\Http\Controllers\Api\AppointmentSlotController::class, 'getAvailableSlots'])
        ->name('api.appointments.available-slots');
});

// Публичный API для получения доступных слотов по slug бизнеса
Route::get('/book/{slug}/available-slots', [\App\Http\Controllers\Public\AppointmentSlotController::class, 'getAvailableSlots'])
    ->name('api.public.appointments.available-slots');

// API для системных уведомлений (требует авторизации)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('notifications')->name('api.notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'index'])
            ->name('index');
        Route::get('/unread', [\App\Http\Controllers\Api\NotificationController::class, 'unread'])
            ->name('unread');
        Route::get('/unread-count', [\App\Http\Controllers\Api\NotificationController::class, 'unreadCount'])
            ->name('unread-count');
        Route::post('/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead'])
            ->name('read');
        Route::post('/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead'])
            ->name('read-all');
        Route::post('/unread', [\App\Http\Controllers\Api\NotificationController::class, 'markAsUnread'])
            ->name('unread.update');
        Route::delete('/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy'])
            ->name('delete');
    });
});
