<?php

use Illuminate\Support\Facades\Route;
use Project\Common\Administrators\Role;
use App\Http\Controllers\Api\Catalogue\Category\CategoryController;
use App\Http\Controllers\Api\Catalogue\CategoryContent\ContentController;

Route::middleware(['auth:admin', 'hasAccess:' . Role::MANAGER->value])->prefix('admin/catalogue/categories')->group(function () {
    Route::post('', [CategoryController::class, 'create']);
    Route::put('{id}', [CategoryController::class, 'update']);
    Route::delete('{id}', [CategoryController::class, 'delete']);
    Route::get('{id}', [CategoryController::class, 'get']);
    Route::get('', [CategoryController::class, 'list']);

    Route::patch('{id}/content', [ContentController::class, 'updateContent']);
});