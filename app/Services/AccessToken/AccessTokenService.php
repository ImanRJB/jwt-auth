<?php

namespace ImanRjb\JwtAuth\Services\AccessToken;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Hash;
use ImanRjb\JwtAuth\Models\Token;
use ImanRjb\JwtAuth\Models\LoginHistory;
use Symfony\Component\HttpFoundation\Response;
use Stevebauman\Location\Facades\Location;

class AccessTokenService
{
    public function create($user, $userAgent, $ip, $aud = 'web', $scope = '*')
    {
        try {
            $now = Carbon::now();
            $nowMs = $now->getTimestampMs();
            $expiresIn = Carbon::now()->addMinutes(config('jwt-auth.access_token_lifetime'));
            $expiresInMs = $expiresIn->getTimestampMs();

            $refreshToken = bin2hex(random_bytes(391));
            $refreshTokenHash = hash('sha256', $refreshToken);
            $refreshTokenExpiresAt = Carbon::now()->addMinutes(config('jwt-auth.refresh_token_lifetime'));


            $token_id = bin2hex(random_bytes(32));

            $data = [
                'header' => [
                    "typ" => "JWT",
                    "alg" => "RS256",
                ],
                'payload' => [
                    "aud" => $aud,
                    "jti" => $token_id,
                    "iat" => $nowMs,
                    "nbf" => $nowMs,
                    "exp" => $expiresInMs,
                    "sub" => $user->id,
                    "scopes" => $scope
                ]
            ];

            $token = Token::create([
                'id' => $token_id,
                'user_id' => $user->id,
                'refresh_token' => $refreshTokenHash,
                'user_agent' => $userAgent,
                'ip' => $ip,
                'scopes' => $scope,
                'created_at' => $now,
                'updated_at' => $now,
                'expires_at' => $expiresIn,
                'refresh_token_expires_at' => $refreshTokenExpiresAt,
            ]);

            LoginHistory::create([
                'user_id' => $user->id,
                'user_agent' => $userAgent,
                'ip' => $ip,
//                'location' =>  Location::get($ip) ? Location::get($ip)->countryName : null,
            ]);

            $jwt = JWT::encode($data, config('jwt-auth.jwt_secret'), 'HS512');

            $respone = [
                'token_type' => 'Bearer',
                'expires_in' => $expiresInMs,
                'access_token' => $jwt,
                'refresh_token' => $refreshToken,
            ];

            return $respone;
        } catch (\Exception $exception) {
            throw new $exception('User not found');
        }
    }

    public function createFromRefreshToken($refreshToken, $userAgent, $ip, $aud = 'web', $scope = '*')
    {
        $refreshTokenHash = hash('sha256', $refreshToken);
        $token = Token::whereRefreshToken($refreshTokenHash)->first();

        if (!$token) {
            return;
        } elseif ($token->refresh_token_expires_at < Carbon::now() or $token->revoked) {
            return;
        } else {
            $token->update(['revoked' => 1]);
            return $this->create($token->user, $userAgent, $ip, $aud, $scope);
        }
        return;
    }

    public function checkToken($token)
    {
        try {
            $decodeToken = JWT::decode($token, new Key(config('jwt-auth.jwt_secret'), 'HS512'));
            $decodeToken = json_decode(json_encode($decodeToken), true);
            $token = Token::whereId($decodeToken['payload']['jti'])->first();

            if ($token and $token->expires_at > Carbon::now() and !$token->revoked) {
                return $token->user;
            }
            return;
        } catch (\Exception $exception) {
            throw new $exception('Token not found.');
        }
    }

    public function revokeByTokenId($tokenId)
    {
        try {
            $token = Token::whereId($tokenId)->first();

            if (!$token) {
                throw new \Exception('Token not found.');
            }

            $token->update(['revoked' => 1]);
            return true;
        } catch (\Exception $exception) {
            throw new $exception('Token not found.');
        }
    }

    public function revokeByToken($token)
    {
        try {
            $decodeToken = JWT::decode($token, new Key(config('jwt-auth.jwt_secret'), 'HS512'));
            $decodeToken = json_decode(json_encode($decodeToken), true);
            $token = Token::whereId($decodeToken['payload']['jti'])->first();

            if (!$token) {
                throw new \Exception('Token not found.');
            }

            $token->update(['revoked' => 1]);
            return true;
        } catch (\Exception $exception) {
            throw new \Exception('Token not found.');
        }
    }

    public function getActiveTokens($user)
    {
        $token = request()->bearerToken();
        $decodeToken = JWT::decode($token, new Key(config('jwt-auth.jwt_secret'), 'HS512'));
        $decodeToken = json_decode(json_encode($decodeToken), true);
        $current = Token::whereId($decodeToken['payload']['jti'])->first();

        $tokens = Token::whereUserId($user->id)->whereRevoked(0)->orderBy('created_at', 'desc')->get();

        foreach ($tokens as $token) {
            if ($token->id == $current->id) {
                $token['current'] = 1;
            } else {
                $token['current'] = 0;
            }
        }

        return $tokens;
    }
}
