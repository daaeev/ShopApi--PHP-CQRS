<?php

use Illuminate\Support\Facades\Route;
use Project\Infrastructure\Laravel\API\Controllers\Catalogue\CatalogueController;

require __DIR__ . DIRECTORY_SEPARATOR . 'product.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'category.php';

Route::prefix('catalogue')->group(function () {
    Route::get('{code}', [CatalogueController::class, 'details']);
    Route::get('', [CatalogueController::class, 'list']);
});