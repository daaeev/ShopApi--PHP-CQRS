<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Cart\CartController;

Route::group(['prefix' => 'cart'], function () {
    Route::post('', [CartController::class, 'addItem']);
    Route::patch('currency', [CartController::class, 'changeCartCurrency']);
    Route::patch('promocode', [CartController::class, 'usePromocode']);
    Route::delete('promocode', [CartController::class, 'removePromocode']);
    Route::patch('{id}', [CartController::class, 'updateItem']);
    Route::delete('{id}', [CartController::class, 'removeItem']);

    Route::get('', [CartController::class, 'getActiveCart']);
});