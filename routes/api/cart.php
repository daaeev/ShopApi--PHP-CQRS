<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Cart\CartController;

Route::group(['prefix' => 'cart'], function () {
    Route::post('', [CartController::class, 'addItem']);
    Route::patch('{id}', [CartController::class, 'updateItem']);
    Route::delete('{id}', [CartController::class, 'removeItem']);
    Route::patch('currency', [CartController::class, 'changeCartCurrency']);

    Route::get('', [CartController::class, 'getActiveCart']);
});