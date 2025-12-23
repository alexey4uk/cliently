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
