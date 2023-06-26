<?php

return [
    'application' => [
        'client-hash-cookie-name' => env('CLIENT_HASH_COOKIE_NAME', 'clientHash'),
        'client-hash-cookie-lifetime-in-minutes' => env('CLIENT_HASH_COOKIE_LIFETIME_IN_MINUTES', 1440),
    ]
];