<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TruffleController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('truffles', [TruffleController::class, 'store']);
});
