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

$nombre = trim($data["nombre"] ?? "");
$apellido = trim($data["apellido"] ?? "");
$telefono = trim($data["telefono"] ?? "");
$email = trim($data["email"] ?? "");
$password = trim($data["password"] ?? "");

if (empty($nombre) || empty($apellido) || empty($telefono) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Todos los campos son obligatorios."]);
    exit();
}


$check_sql = "SELECT id FROM users WHERE email = ?";
$check_stmt = $pdo->prepare($check_sql);
$check_stmt->execute([$email]);

if ($check_stmt->rowCount() > 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "El correo ya está registrado."]);
    exit();
}

$password_hashed = password_hash($password, PASSWORD_DEFAULT);


$sql = "INSERT INTO users (nombre, apellido, telefono, email, password) VALUES (?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
if ($stmt->execute([$nombre, $apellido, $telefono, $email, $password_hashed])) {
    http_response_code(201);
    echo json_encode(["success" => true, "message" => "Usuario registrado con éxito"]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Error al registrar usuario"]);
}
?>
