<?php

namespace ImanRjb\JwtAuth\Services\AccessToken;

use Symfony\Component\HttpFoundation\Response;

class AccessTokenService
{
    private $network;

    public function getNewToken()
    {
        return 'Iman';
    }
}
