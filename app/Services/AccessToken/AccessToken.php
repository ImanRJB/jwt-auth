<?php

namespace ImanRjb\JwtAuth\Services\AccessToken;

use Illuminate\Support\Facades\Facade;

/**
 * @method static create($user, $userAgent, $ip, $aud = 'web', $scope = '*')
 * @method static createFromRefreshToken($refreshToken, $userAgent, $ip, $aud = 'web', $scope = '*')
 * @method static checkToken($token)
 * @method static revokeByTokenId($tokenId)
 * @method static revokeByToken($token)
 * @method static getActiveTokens($user)
 */

class AccessToken extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'accessToken';
    }
}
