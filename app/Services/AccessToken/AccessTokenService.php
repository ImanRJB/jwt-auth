<?php

namespace ImanRjb\JwtAuth\Services\AccessToken;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use hisorange\BrowserDetect\Exceptions\Exception;
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
            $expiresIn = $now->addMinutes(config('jwt-auth.access_token_lifetime'));
            $expiresInMs = $expiresIn->getTimestampMs();

            $refreshToken = bin2hex(random_bytes(391));
            $refreshTokenHash = hash('sha256', $refreshToken);
            $refreshTokenExpiresAt = $now->addMinutes(config('jwt-auth.refresh_token_lifetime'));


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
                'location' =>  Location::get($ip) ? Location::get($ip)->countryName : null,
            ]);

            $jwt = JWT::encode($data, config('jwt-auth.private_key'), 'RS256');

            $respone = [
                'token_type' => 'Bearer',
                'expires_in' => $expiresInMs,
                'access_token' => $jwt,
                'refresh_token' => $refreshToken,
            ];

            return $respone;
        } catch (\Exception $exception) {
            throw new Exception('User not found');
        }
    }

    public function createFromRefreshToken($refreshToken, $userAgent, $ip, $aud = 'web', $scope = '*')
    {
        $refreshTokenHash = hash('sha256', $refreshToken);
        $token = Token::whereRefreshToken($refreshTokenHash)->first();

        if (!$token) {
            throw new Exception('Token not found.');
        } elseif ($token->refresh_token_expires_at < Carbon::now() or $token->revoked) {
            throw new Exception('Token has been expired.');
        }

        $token->update(['revoked' => 1]);
        return $this->create($token->user, $userAgent, $ip, $aud, $scope);
    }

    public function checkToken($token)
    {
        try {
            $decodeToken = JWT::decode($token, new Key(config('jwt-auth.public_key'), 'RS256'));
            $decodeToken = json_decode(json_encode($decodeToken), true);
            $token = Token::whereId($decodeToken['payload']['jti'])->first();

            if (!$token) {
                throw new Exception('Token not found.');
            } elseif ($token->expires_at < Carbon::now() or $token->revoked) {
                throw new Exception('Token has been expired.');
            }

            return $token->user;
        } catch (\Exception $exception) {
            throw new Exception('Token not found.');
        }
    }

    public function revokeByTokenId($tokenId)
    {
        try {
            $token = Token::whereId($tokenId)->first();

            if (!$token) {
                throw new Exception('Token not found.');
            }

            $token->update(['revoked' => 1]);
            return true;
        } catch (\Exception $exception) {
            throw new Exception('Token not found.');
        }
    }

    public function revokeByToken($token)
    {
        try {
            $decodeToken = JWT::decode($token, new Key(config('jwt-auth.public_key'), 'RS256'));
            $decodeToken = json_decode(json_encode($decodeToken), true);
            $token = Token::whereId($decodeToken['payload']['jti'])->first();

            if (!$token) {
                throw new Exception('Token not found.');
            }

            $token->update(['revoked' => 1]);
            return true;
        } catch (\Exception $exception) {
            throw new Exception('Token not found.');
        }
    }

    public function getActiveTokens($user)
    {
        return Token::whereUserId($user->id)->get();
    }
}
