<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Authorization, Content-Type");

require_once '../config/database.php';
require_once '../app/Controllers/AuthController.php';
require_once '../vendor/autoload.php';
require_once '../app/Middleware/AuthMiddleware.php';


// Obtener URI y método HTTP
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Instanciar conexión a la DB
$db = (new Database())->connect();

// Ruta: /register
if ($uri === '/register' && $method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $auth = new AuthController($db);
    $response = $auth->register($data);

    http_response_code($response['status']);
    echo json_encode(['message' => $response['message']]);
    exit;
}

if ($uri === '/login' && $method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $auth = new AuthController($db);
    $response = $auth->login($data);

    http_response_code($response['status']);
    echo json_encode([
        'message' => $response['message'],
        'token' => $response['token'] ?? null
    ]);
    exit;
}

if ($uri === '/profile' && $method === 'GET') {
    $user = AuthMiddleware::authenticate();

    http_response_code(200);
    echo json_encode([
        'message' => 'Perfil del usuario autenticado',
        'user' => $user
    ]);
    exit;
}

if ($uri === '/users' && $method === 'GET') {
    $userData = AuthMiddleware::authenticate();

    if ($userData->role !== 'admin') {
        http_response_code(403);
        echo json_encode(['message' => 'Acceso denegado: solo administradores']);
        exit;
    }

    // Obtener parámetros de paginación
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

    $userModel = new User($db);
    $usuarios = $userModel->getAllPaginated($page, $limit);

    http_response_code(200);
    echo json_encode([
        'message' => 'Usuarios paginados',
        'data' => $usuarios
    ]);
    exit;
}


// Ruta no encontrada
http_response_code(404);
echo json_encode(['message' => 'Ruta no encontrada']);
