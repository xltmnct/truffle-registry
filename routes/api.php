<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TruffleController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login'])
    ->name('auth.login');

Route::apiResource('truffles', TruffleController::class)
    ->middleware('auth:sanctum')
    ->only([
        'store'
    ]);
