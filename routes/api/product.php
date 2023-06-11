<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Product\ProductController;
use Project\Common\Administrators\Role;

Route::middleware(['auth:admin', 'hasAccess:' . Role::MANAGER->value])->prefix('products')->group(function () {
    Route::post('', [ProductController::class, 'create']);
    Route::put('{id}', [ProductController::class, 'update']);
    Route::delete('{id}', [ProductController::class, 'delete']);

    Route::get('{id}', [ProductController::class, 'get']);
    Route::get('', [ProductController::class, 'list']);
});