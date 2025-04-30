<?php
include("../config/connect.php");
session_start();
header('Content-Type: application/json');


function sendResponse($status, $message) {
    http_response_code($status);
    echo json_encode(["message" => $message]);
    exit;
}
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$id = $_SESSION['user_id'];

$req = $conx->prepare("SELECT * FROM utilisateur WHERE idUser = ?");
if (!$req) {
    sendResponse(500, "error");
}
$req->bind_param('s', $id);
$req->execute();
$res = $req->get_result();
$user = $res->fetch_assoc();

if (!$user) {
    sendResponse(400, "no user found");
    exit;
}
sendResponse(200 , $user);
?>