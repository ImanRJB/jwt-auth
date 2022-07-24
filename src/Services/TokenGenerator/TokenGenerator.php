<?php

namespace ImanRjb\JwtAuth\Services\TokenGenerator;

use Illuminate\Support\Facades\Facade;

/**
 * @method static getNewAddress($type, $path)
 */

class TokenGenerator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tokenGenerator';
    }
}
