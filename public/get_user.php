<?php
include "../config/database.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$email = $_GET['email'] ?? '';

if (empty($email)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Email es requerido"]);
    exit();
}

$sql = "SELECT id, nombre, apellido, email, telefono FROM users WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);

$user = $stmt->fetch();

if ($user) {
    echo json_encode(["success" => true, "user" => $user]);
} else {
    http_response_code(404);
    echo json_encode(["success" => false, "error" => "Usuario no encontrado"]);
}
?>