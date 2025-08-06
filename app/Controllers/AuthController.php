<?php
require_once __DIR__ . '/../Models/User.php';
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

        // Validar formato de email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 400, 'message' => 'El email no es valido'];
        }

        // Validar seguridad de contraseña
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $data['password'])) {
            return [
                'status' => 400,
                'message' => 'La contrasena debe tener al menos 8 caracteres, una mayuscula, un numero y un caracter especial.'
            ];
        }

        $user = new User($this->db);
        if ($user->findByEmail($data['email'])) {
            return ['status' => 409, 'message' => 'El email ya esta registrado'];
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
            return ['status' => 400, 'message' => 'Email y contrasena requeridos'];
        }

        $userModel = new User($this->db);
        $userData = $userModel->findByEmail($data['email']);

        if (!$userData || !password_verify($data['password'], $userData['password_hash'])) {
            return ['status' => 401, 'message' => 'Credenciales invalidas'];
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
