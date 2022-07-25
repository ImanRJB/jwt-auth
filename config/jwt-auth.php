<?php
return [
    'jwt_secret' => env('JWT_SECRET'),

    'access_token_lifetime' => env('ACCESS_TOKEN_LIFETIME', 120),
    'refresh_token_lifetime' => env('REFRESH_TOKEN_LIFETIME', 2880)
];
