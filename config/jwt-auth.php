<?php
return [
    'public_key' => env('JWT_PUBLIC_KEY'),
    'private_key' => env('JWT_PRIVATE_KEY'),

    'access_token_lifetime' => env('ACCESS_TOKEN_LIFETIME', 120),
    'refresh_token_lifetime' => env('REFRESH_TOKEN_LIFETIME', 2880)
];
