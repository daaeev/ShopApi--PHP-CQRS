<?php

use Illuminate\Support\Facades\Route;
use Project\Common\Administrators\Role;
use App\Http\Controllers\Api\Catalogue\ProductController;
use App\Http\Controllers\Api\Catalogue\ContentController;

Route::middleware(['auth:admin', 'hasAccess:' . Role::MANAGER->value])->prefix('admin/products')->group(function () {
    Route::post('', [ProductController::class, 'create']);
    Route::put('{id}', [ProductController::class, 'update']);
    Route::delete('{id}', [ProductController::class, 'delete']);

    Route::patch('content/{id}', [ContentController::class, 'updateContent']);
    Route::post('preview/{id}', [ContentController::class, 'updatePreview']);

    Route::get('{id}', [ProductController::class, 'get']);
    Route::get('', [ProductController::class, 'list']);
});