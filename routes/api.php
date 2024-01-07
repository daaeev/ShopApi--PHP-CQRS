<?php

use Illuminate\Support\Facades\Route;

Route::group([], function () {
    Route::get('handshake', function () {}); // using for generate CSRF token

    require __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'catalogue' . DIRECTORY_SEPARATOR . 'catalogue.php';
    require __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'shopping' . DIRECTORY_SEPARATOR . 'shopping.php';
    require __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'administrators.php';
    require __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'clients.php';
});