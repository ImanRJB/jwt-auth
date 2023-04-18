# Laravel and Lumen Package For JWT
[![Latest Version on Packagist](https://img.shields.io/packagist/v/imanrjb/jwt-auth.svg?style=flat-square)](https://packagist.org/packages/imanrjb/jwt-auth)
[![GitHub issues](https://img.shields.io/github/issues/ImanRJB/jwt-auth?style=flat-square)](https://github.com/ImanRJB/jwt-auth/issues)
[![GitHub stars](https://img.shields.io/github/stars/ImanRJB/jwt-auth?style=flat-square)](https://github.com/ImanRJB/jwt-auth/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/ImanRJB/jwt-auth?style=flat-square)](https://github.com/ImanRJB/jwt-auth/network)
[![Total Downloads](https://img.shields.io/packagist/dt/imanrjb/jwt-auth.svg?style=flat-square)](https://packagist.org/packages/imanrjb/jwt-auth)
[![GitHub license](https://img.shields.io/github/license/ImanRJB/jwt-auth?style=flat-square)](https://github.com/ImanRJB/jwt-auth/blob/master/LICENSE)

## <g-emoji class="g-emoji" alias="arrow_down" fallback-src="https://github.githubassets.com/images/icons/emoji/unicode/2b07.png">⬇️</g-emoji> How to install and config [imanrjb/jwt-auth](https://github.com/ImanRJB/jwt-auth) package?

#### Install package
```bash
composer require imanrjb/jwt-auth
```

#### Config package
```php
// Add this lines in "App\Providers\AuthServiceProvider"

public function boot(): void
{
    $this->app['auth']->viaRequest('api', function ($request) {
        $token = $request->bearerToken();
        if($token) {
            return AccessToken::checkToken($token);
        }
        return;
    });
}
```
```php
// Change the "config/auth.php" file

'defaults' => [
    'guard' => 'api',
    'passwords' => 'users',
],

'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'api' => [
        'driver' => 'api',
        'provider' => 'users',
    ],
],
```
```dotenv
# Add this items to .env file

JWT_SECRET=GKPMVOCKpMHHJQ3GprVA0EfTKGJi7227mjeKN009Vndls70226raawkRzDoB97AI
ACCESS_TOKEN_LIFETIME=120
REFRESH_TOKEN_LIFETIME=1200
```

## <g-emoji class="g-emoji" alias="book" fallback-src="https://github.githubassets.com/images/icons/emoji/unicode/1f4d6.png">📖</g-emoji> How to use in routes as middleware

```php
Route::get('user', function () {
   return auth()->user();
})->middleware('auth:api');
```
