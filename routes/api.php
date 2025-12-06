<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/slug/check', [\App\Http\Controllers\ApiController::class, 'checkSlug'])->name('api.slug.check');

