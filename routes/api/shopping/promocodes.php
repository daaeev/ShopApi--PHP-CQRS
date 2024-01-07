<?php

use Illuminate\Support\Facades\Route;
use Project\Common\Administrators\Role;
use App\Http\Controllers\Api\Promocodes\PromocodesController;

Route::middleware(['auth:admin', 'hasAccess:' . Role::MANAGER->value])
    ->prefix('admin/promocodes')
    ->group(function () {
        Route::post('', [PromocodesController::class, 'create']);
        Route::put('{id}', [PromocodesController::class, 'update']);
        Route::delete('{id}', [PromocodesController::class, 'delete']);
        Route::patch('{id}/activate', [PromocodesController::class, 'activate']);
        Route::patch('{id}/deactivate', [PromocodesController::class, 'deactivate']);

        Route::get('{id}', [PromocodesController::class, 'get']);
        Route::get('', [PromocodesController::class, 'list']);
    });