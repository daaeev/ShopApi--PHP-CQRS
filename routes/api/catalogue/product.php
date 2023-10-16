<?php

use Illuminate\Support\Facades\Route;
use Project\Common\Administrators\Role;
use App\Http\Controllers\Api\Catalogue\Product\ProductController;
use App\Http\Controllers\Api\Catalogue\ProductContent\ContentController;
use App\Http\Controllers\Api\Catalogue\Settings\SettingsController;

Route::middleware(['auth:admin', 'hasAccess:' . Role::MANAGER->value])
    ->prefix('admin/catalogue/products')
    ->group(function () {
        Route::post('', [ProductController::class, 'create']);
        Route::put('{id}', [ProductController::class, 'update']);
        Route::delete('{id}', [ProductController::class, 'delete']);
        Route::get('{id}', [ProductController::class, 'get']);
        Route::get('', [ProductController::class, 'list']);

        Route::patch('{id}/content', [ContentController::class, 'updateContent']);
        Route::post('{id}/preview', [ContentController::class, 'updatePreview']);
        Route::post('{id}/image', [ContentController::class, 'addImage']);
        Route::delete('image/{id}', [ContentController::class, 'deleteImage']);

        Route::put('{id}/settings', [SettingsController::class, 'update']);
    });