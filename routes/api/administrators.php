<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Administrators\AdminsController;

Route::group(['middleware' => 'auth:admin', 'prefix' => 'administrators'], function () {
    Route::post('', [AdminsController::class, 'create']);
    Route::put('{id}', [AdminsController::class, 'update']);
    Route::delete('{id}', [AdminsController::class, 'delete']);

    Route::get('{id}', [AdminsController::class, 'get']);
    Route::get('', [AdminsController::class, 'list']);
    Route::get('authorized', [AdminsController::class, 'authorized']);
});

Route::group(['prefix' => 'administrators'], function () {
    Route::post('login', [AdminsController::class, 'login']);
    Route::post('logout', [AdminsController::class, 'logout']);
});