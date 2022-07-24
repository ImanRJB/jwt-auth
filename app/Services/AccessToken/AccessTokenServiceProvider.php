<?php

namespace ImanRjb\JwtAuth\Services\AccessToken;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AccessTokenServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('accessToken', function() {
            return App::make('ImanRjb\JwtAuth\Services\AccessToken\AccessTokenService');
        });
    }
}
