<?php

namespace ImanRjb\JwtAuth;

use Illuminate\Support\ServiceProvider;
use ImanRjb\JwtAuth\Commands\PurgeRevokedTokens;

class JwtAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        // For load config files
        if (file_exists(__DIR__ . '/../config/jwt-auth.php')) {
            $this->mergeConfigFrom(__DIR__ . '/../config/jwt-auth.php', 'jwt-auth');
        }

        if (file_exists(__DIR__ . '/../config/location.php')) {
            $this->mergeConfigFrom(__DIR__ . '/../config/location.php', 'location');
        }
        
        $this->loadMigrationsFrom(__DIR__.'/../migrations');

        $this->app->alias(\ImanRjb\JwtAuth\Services\AccessToken\AccessToken::class, 'AccessToken');
        $this->app->register(\ImanRjb\JwtAuth\Services\AccessToken\AccessTokenServiceProvider::class);

        $this->app->register(\Stevebauman\Location\LocationServiceProvider::class);

        // Commands
        $this->commands([
            PurgeRevokedTokens::class
        ]);
    }
}
