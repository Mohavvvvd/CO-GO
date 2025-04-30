<?php
include("../config/connect.php");
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Not authenticated"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $_SESSION['user_id'];

if (empty($data['password'])) {
    http_response_code(400);
    echo json_encode(["message" => "Password required"]);
    exit;
}

$stmt = $conx->prepare("SELECT password FROM utilisateur WHERE idUser = ?");
$stmt->bind_param('s', $id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user || !password_verify($data['password'], $user['password'])) {
    http_response_code(403);
    echo json_encode(["message" => "Incorrect password"]);
    exit;
}
$fields = [];
$params = [];
$types = '';

$map = [
    'username' => 'username',
    'email' => 'email',
    'telephone' => 'telephone',
    'nom' => 'nom',
    'prenom' => 'prenom'
];

foreach ($map as $key => $column) {
    if (isset($data[$key])) {
        $fields[] = "$column = ?";
        $params[] = $data[$key];
        $types .= 's';
    }
}

if (empty($fields)) {
    http_response_code(400);
    echo json_encode(["message" => "No valid fields to update"]);
    exit;
}

$query = "UPDATE utilisateur SET " . implode(", ", $fields) . " WHERE idUser = ?";
$params[] = $id;
$types .= 's';

$stmt = $conx->prepare($query);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(["message" => "Update successful"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Update failed"]);
}
?>
