<?php
require_once __DIR__ . '/../Core/Auth.php';

class AuthMiddleware {
    public static function authenticate() {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Token no proporcionado']);
            exit;
        }

        $authHeader = $headers['Authorization'];
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['message' => 'Formato de token inválido']);
            exit;
        }

        $token = $matches[1];
        $decoded = Auth::verifyToken($token);

        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['message' => 'Token inválido o expirado']);
            exit;
        }

        return $decoded; // Devuelve los datos del usuario autenticado
    }
}
