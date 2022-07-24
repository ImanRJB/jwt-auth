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

        $this->app->alias(\ImanRjb\JwtAuth\Services\TokenGenerator\TokenGenerator::class, 'TokenGenerator');
        $this->app->register(\ImanRjb\JwtAuth\Services\TokenGenerator\TokenGeneratorServiceProvider::class);
    }
}
