<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/slug/check', \App\Http\Controllers\Api\SlugCheckController::class)
    ->middleware('throttle:20,1')
    ->name('api.slug.check');
