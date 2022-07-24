<?php

namespace ImanRjb\JwtAuth;

use Illuminate\Support\ServiceProvider;

class JwtAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        // For load config files
        if (file_exists(__DIR__ . '/../src/config/jwt-auth.php')) {
            $this->mergeConfigFrom(__DIR__ . '/../src/config/jwt-auth.php', 'jwt-auth');
        }

        $this->app->alias(\ImanRjb\JwtAuth\Services\AccessToken\AccessToken::class, 'AccessToken');
        $this->app->register(\ImanRjb\JwtAuth\Services\AccessToken\AccessTokenServiceProvider::class);
    }
}
