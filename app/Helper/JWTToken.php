<?php

namespace App\Helper;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken
{
    public static function CreateToken($userEmail):string
    {
        $key=  config('jwt.key');
        $payload = [
            'iss' => env('JWT_ISSUER'),
            'iat' => time(),
            'exp' => time() + 3600,
            'userEmail' => $userEmail,
        ];
        return JWT::encode($payload, $key, 'HS256');
    }
    public static function VerifyToken($token):string
    {
        try{
            $key=  config('jwt.key');
            $decoded =  JWT::decode($token, new Key($key, 'HS256'));
            return $decoded->userEmail;
        }
        catch (Exception $exception){
             return 'unauthorized';
        }
    }

}
