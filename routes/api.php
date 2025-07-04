<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


require __DIR__ . '/auth/index.php';
require __DIR__ . '/tasks/index.php';
