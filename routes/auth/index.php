<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController,};
use App\Http\Controllers\Api\RecoverPasswordController;

/**route public */
Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('/register', [AuthController::class, 'createUser']);
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});


/** route restrict */
Route::group(['prefix' => 'auth', 'middleware' => "auth:api"],function () {
    Route::put('/profile', [AuthController::class, 'updateUser']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/refresh', [AuthController::class, 'refresh']);
});

