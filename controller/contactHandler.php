<?php
session_start();
header('Content-Type: application/json');

function sendResponse($status, $message,$success,$userId = null) {
    http_response_code($status);
    echo json_encode(["msg" => $message, "id" => $userId,'success'=>$success]);
    exit;
}

try {
    include("../config/connect.php");

    if (empty($_POST['message'])) {
        sendResponse(400, "Message is required.",false);
    }

    $message = trim($_POST['message']);
    
    if (empty($_SESSION['user_id'])) {
        if (empty($_POST['username']) || empty($_POST['email'])) {
            sendResponse(400, "Name and email are required for guests.",false);
        }

        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $userId = 'visitor';
    } else {
        error_log($_SESSION['user_id']);
        $userId = $_SESSION['user_id'];
        $stmt = $conx->prepare("SELECT email, username FROM utilisateur WHERE idUser = ?");
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        error_log($user['email'].' '.$user['username']);
        if (!$user || empty($user['email']) || empty($user['username'])) {
            sendResponse(400, "Email and username not found for the user.",false);
        }

        $email = $user['email'];
        $username = $user['username'];
    }

    $insert = $conx->prepare("INSERT INTO contact (idUser, username, email, message) VALUES (?, ?, ?, ?)");
    $insert->bind_param("ssss", $userId, $username, $email, $message);
    $insert->execute();

    sendResponse(200, "Thank you $username, your message has been received.", true);

} catch (Exception $e) {
    sendResponse(500, "Server error: " . $e->getMessage(),false);
}
