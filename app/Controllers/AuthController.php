<?php
require_once __DIR__ . '/../Models/user.php';
require_once __DIR__ . '/../Core/Auth.php';

class AuthController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($data) {
        if (!isset($data['name'], $data['email'], $data['password'])) {
            return ['status' => 400, 'message' => 'Faltan campos obligatorios'];
        }

        $user = new User($this->db);
        if ($user->findByEmail($data['email'])) {
            return ['status' => 409, 'message' => 'El email ya está registrado'];
        }

        $user->name = htmlspecialchars(strip_tags($data['name']));
        $user->email = htmlspecialchars(strip_tags($data['email']));
        $user->password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->role = 'user';

        if ($user->create()) {
            return ['status' => 201, 'message' => 'Usuario registrado correctamente'];
        } else {
            return ['status' => 500, 'message' => 'Error al registrar'];
        }
    }

    public function login($data) {
    if (!isset($data['email'], $data['password'])) {
        return ['status' => 400, 'message' => 'Email y contraseña requeridos'];
    }

    $userModel = new User($this->db);
    $userData = $userModel->findByEmail($data['email']);

    if (!$userData || !password_verify($data['password'], $userData['password_hash'])) {
        return ['status' => 401, 'message' => 'Credenciales inválidas'];
    }

    // Generar payload para el token
    $payload = [
        "id" => $userData['id'],
        "name" => $userData['name'],
        "email" => $userData['email'],
        "role" => $userData['role']
    ];

    $token = Auth::generateToken($payload);

    return [
        'status' => 200,
        'message' => 'Login exitoso',
        'token' => $token
    ];
}
}
