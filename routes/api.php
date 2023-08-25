<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TruffleController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], static function () {
    Route::post('login', [AuthController::class, 'login']);
    //here can be other routes
});

Route::group(['middleware' => 'auth:sanctum'], static function () {
    Route::group(['prefix' => 'truffles'], static function () {
        Route::post('create', [TruffleController::class, 'create']);
        //here can be other routes
    });
});

