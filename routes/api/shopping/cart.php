<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Cart\CartController;

Route::group(['prefix' => 'cart'], function () {
    Route::post('', [CartController::class, 'addOffer']);
    Route::patch('currency', [CartController::class, 'changeCartCurrency']);
    Route::patch('promocode', [CartController::class, 'usePromocode']);
    Route::delete('promocode', [CartController::class, 'removePromocode']);
    Route::patch('{id}', [CartController::class, 'updateOffer']);
    Route::delete('{id}', [CartController::class, 'removeOffer']);

    Route::get('', [CartController::class, 'getActiveCart']);
});