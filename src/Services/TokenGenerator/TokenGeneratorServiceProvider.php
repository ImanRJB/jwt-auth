<?php

namespace ImanRjb\JwtAuth\Services\TokenGenerator;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class TokenGeneratorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('tokenGenerator', function() {
            return App::make('ImanRjb\JwtAuth\Services\TokenGenerator\TokenGeneratorService');
        });
    }
}
