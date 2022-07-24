<?php

namespace ImanRjb\JwtAuth\Services\AccessToken;

use Illuminate\Support\Facades\Facade;

/**
 * @method static getNewAddress($type, $path)
 */

class AccessToken extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'accessToken';
    }
}
