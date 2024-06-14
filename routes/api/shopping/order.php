<?php

use Illuminate\Support\Facades\Route;
use Project\Common\Administrators\Role;
use App\Http\Controllers\Api\Orders\OrdersClientController;
use App\Http\Controllers\Api\Orders\OrdersAdminController;

Route::group(['prefix' => 'orders'], function () {
    Route::post('', [OrdersClientController::class, 'create']);
    Route::get('', [OrdersClientController::class, 'get']);
});

Route::middleware(['auth:admin', 'hasAccess:' . Role::MANAGER->value])
    ->prefix('admin/orders')
    ->group(function () {
        Route::get('{id}', [OrdersAdminController::class, 'get']);
        Route::get('', [OrdersAdminController::class, 'list']);

        Route::put('{id}', [OrdersAdminController::class, 'update']);
        Route::delete('{id}', [OrdersAdminController::class, 'delete']);

        Route::post('{id}/offer', [OrdersAdminController::class, 'addOffer']);
        Route::patch('{id}/offer/{offerId}', [OrdersAdminController::class, 'updateOffer']);
        Route::delete('{id}/offer/{offerId}', [OrdersAdminController::class, 'removeOffer']);

        Route::post('{id}/promo', [OrdersAdminController::class, 'addPromo']);
        Route::delete('{id}/promo', [OrdersAdminController::class, 'removePromo']);
    });