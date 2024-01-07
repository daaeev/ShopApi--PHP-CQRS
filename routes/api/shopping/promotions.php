<?php

use Illuminate\Support\Facades\Route;
use Project\Common\Administrators\Role;
use App\Http\Controllers\Api\Promotions\PromotionsController;

Route::middleware(['auth:admin', 'hasAccess:' . Role::MANAGER->value])
    ->prefix('admin/promotions')
    ->group(function () {
        Route::get('{id}', [PromotionsController::class, 'get']);
        Route::get('', [PromotionsController::class, 'list']);

        Route::post('', [PromotionsController::class, 'create']);
        Route::put('{id}', [PromotionsController::class, 'update']);
        Route::delete('{id}', [PromotionsController::class, 'delete']);
        Route::post('{id}/discounts', [PromotionsController::class, 'addDiscount']);
        Route::delete('{id}/discounts/{discountId}', [PromotionsController::class, 'removeDiscount']);
        Route::patch('{id}/enable', [PromotionsController::class, 'enable']);
        Route::patch('{id}/disable', [PromotionsController::class, 'disable']);
    });