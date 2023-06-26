<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Cart\CartController;

Route::group(['prefix' => 'cart'], function () {
    Route::post('', [CartController::class, 'addItem']);
    Route::patch('currency', [CartController::class, 'changeCartCurrency']);
    Route::patch('{id}', [CartController::class, 'updateItem']);
    Route::delete('{id}', [CartController::class, 'removeItem']);

    Route::get('', [CartController::class, 'getActiveCart']);
});