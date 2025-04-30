<?php
include("../config/connect.php");
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

function sendResponse($status, $message) {
    http_response_code($status);
    echo json_encode(["message" => $message]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        isset($_POST['email'], $_POST['password']) &&
        !empty($_POST['email']) &&
        !empty($_POST['password'])
    ) {
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $passwordRaw = trim($_POST['password']);

        if (!$email) {
            sendResponse(400, "Adresse email invalide.");
        }

        if (strlen($passwordRaw) < 8) {
            sendResponse(400, "Le mot de passe doit contenir au moins 8 caractères.");
        }

        error_log('Email: ' . $email);
        error_log('Password: ' . $passwordRaw);

        $stmt = $conx->prepare("SELECT idUser, password, username FROM utilisateur WHERE email = ?");
        if (!$stmt) {
            sendResponse(500, "Erreur de préparation : " . $conx->error);
        }

        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            sendResponse(500, "Erreur d'exécution de la requête : " . $stmt->error);
        }

        $result = $stmt->get_result();
        if (!$result) {
            sendResponse(500, "Erreur de récupération des résultats : " . $stmt->error);
        }

        $user = $result->fetch_assoc();

        if (!$user) {
            sendResponse(401, "Aucun utilisateur trouvé avec cet email.");
        }

        
        if (password_verify($passwordRaw, $user['password'])) {
            $_SESSION['user_id'] = $user['idUser'];
            $_SESSION['username'] = $user['username'];
            sendResponse(200, "Connexion réussie !");
        } else {
            sendResponse(401, "Mot de passe incorrect.");
        }

        $stmt->close();
    } else {
        sendResponse(400, "Tous les champs sont requis.");
    }
} else {
    sendResponse(405, "Méthode non autorisée.");
}

?>
