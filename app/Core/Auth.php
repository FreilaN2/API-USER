<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    private static $secret_key = "TU_CLAVE_SECRETA";
    private static $encrypt_type = 'HS256';

    public static function generateToken($payload) {
        return JWT::encode($payload, self::$secret_key, self::$encrypt_type);
    }

    public static function verifyToken($token) {
        try {
            return JWT::decode($token, new Key(self::$secret_key, self::$encrypt_type));
        } catch (Exception $e) {
            return null;
        }
    }
}
