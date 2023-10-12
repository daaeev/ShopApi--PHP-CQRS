<?php

use Illuminate\Support\Facades\Route;
use Project\Common\Administrators\Role;
use App\Http\Controllers\Api\Clients\ClientsController;

Route::middleware(['auth:admin', 'hasAccess:' . Role::ADMIN->value])
    ->prefix('admin/clients')
    ->group(function () {
        Route::get('{id}', [ClientsController::class, 'get']);
        Route::get('', [ClientsController::class, 'list']);
    });