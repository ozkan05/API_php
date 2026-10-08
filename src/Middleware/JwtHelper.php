<?php
namespace App\Middleware;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    private static $secretKey = 'ITcampus-SaintMichel-Annecy-2024-CleSecrete!';
    private static $algorithm = 'HS256';

    public static function generateToken($data, $expiry = 3600)
    {
        $issuedAt = time();
        $expiration = $issuedAt + $expiry;
        $payload = array(
            'iat' => $issuedAt,
            'exp' => $expiration,
            'data' => $data
        );
        return JWT::encode($payload, self::$secretKey, self::$algorithm);
    }

    public static function validateToken($token)
    {
        try {
            return JWT::decode($token, new Key(self::$secretKey, self::$algorithm));
        } catch (Exception $e) {
            return null;
        }
    }
}