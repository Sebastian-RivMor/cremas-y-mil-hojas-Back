<?php
include "../config/database.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "No se recibieron datos JSON."]);
    exit();
}

$email = trim($data["email"] ?? "");
$password = trim($data["password"] ?? "");

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Correo y contraseña son obligatorios."]);
    exit();
}

// Verificar si el correo existe
$sql = "SELECT id, password FROM users WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);

$user = $stmt->fetch();

// Cambia la parte final del login.php
// Modifica la parte final del login.php
if ($user && password_verify($password, $user['password'])) {
    // Obtener más datos del usuario
    $userData = $pdo->prepare("SELECT id, nombre, apellido, email, telefono FROM users WHERE email = ?");
    $userData->execute([$email]);
    $userDetails = $userData->fetch();
    
    http_response_code(200);
    echo json_encode([
        "success" => true, 
        "message" => "Inicio de sesión exitoso", 
        "user" => [
            "nombre" => $userDetails['nombre'],
            "apellido" => $userDetails['apellido'],
            "email" => $userDetails['email'],
            "telefono" => $userDetails['telefono']
        ]
    ]);
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Correo o contraseña incorrectos"]);
}
?>
