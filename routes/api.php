<?php

use Illuminate\Support\Facades\Route;

Route::group([], function () {
    Route::get('handshake', function () {}); // using for generate CSRF token

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'product.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'administrators.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'cart.php';
});