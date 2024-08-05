<?php

use Illuminate\Support\Facades\Route;
use Project\Common\Administrators\Role;
use Project\Infrastructure\Laravel\API\Controllers\Administrators\AdminsController;

Route::middleware(['auth:admin', 'hasAccess:' . Role::ADMIN->value])
    ->prefix('admins')
    ->group(function () {
        Route::post('', [AdminsController::class, 'create']);
        Route::put('{id}', [AdminsController::class, 'update']);
        Route::delete('{id}', [AdminsController::class, 'delete']);

        Route::get('{id}', [AdminsController::class, 'get']);
        Route::get('', [AdminsController::class, 'list']);
        Route::get('authorized', [AdminsController::class, 'authorized']);
    });

Route::group(['prefix' => 'admin'], function () {
    Route::post('login', [AdminsController::class, 'login']);
    Route::post('logout', [AdminsController::class, 'logout']);
});