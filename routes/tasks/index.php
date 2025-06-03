<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;


Route::group(['prefix' => 'tasks', 'middleware' => 'auth:api'], function () {

    Route::get('/', [TaskController::class, 'getAllTasks']);
    Route::post('/', [TaskController::class, 'createTask']);
    Route::put('/{taskID}', [TaskController::class, 'updateTask']);
    Route::delete('/{taskID}', [TaskController::class, 'destroy']);
    Route::post('/status', [TaskController::class, 'filterByStatus']);

});

