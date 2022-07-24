<?php

namespace ImanRjb\JwtAuth\Services\TokenGenerator;

use Symfony\Component\HttpFoundation\Response;

class TokenGeneratorService
{
    private $network;

    public function getNewToken()
    {
        return 'Iman';
    }
}
