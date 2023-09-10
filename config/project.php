<?php

return [
    'application' => [
        'client-hash-cookie-name' => env('CLIENT_HASH_COOKIE_NAME', 'clientHash'),
        'client-hash-cookie-length' => env('CLIENT_HASH_COOKIE_LENGTH', 40),
        'client-hash-cookie-lifetime-in-minutes' => env('CLIENT_HASH_COOKIE_LIFETIME_IN_MINUTES', 1440),
    ],
    'storage' => [
        'products-images' => 'product_images'
    ]
];