<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Product\ProductController;

Route::group(['middleware' => 'auth:admin', 'prefix' => 'products'], function () {
    Route::post('', [ProductController::class, 'create']);
    Route::put('{id}', [ProductController::class, 'update']);
    Route::delete('{id}', [ProductController::class, 'delete']);

    Route::get('{id}', [ProductController::class, 'get']);
    Route::get('', [ProductController::class, 'list']);
});