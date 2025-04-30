<?php
include("../config/connect.php");

header('Content-Type: application/json');

function sendResponse($status, $message) {
    http_response_code($status);
    echo json_encode(["message" => $message]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['First_Name'], $_POST['Last_Name'], $_POST['user_name'], 
              $_POST['phone'], $_POST['email'], $_POST['password']) &&
        !empty($_POST['First_Name']) &&
        !empty($_POST['Last_Name']) &&
        !empty($_POST['user_name']) &&
        !empty($_POST['phone']) &&
        !empty($_POST['email']) &&
        !empty($_POST['password'])
    ) {
        $prenom = trim(htmlspecialchars($_POST['First_Name']));
        $nom = trim(htmlspecialchars($_POST['Last_Name']));
        $username = trim(htmlspecialchars($_POST['user_name']));
        $telephone = trim($_POST['phone']);
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $passwordRaw = trim($_POST['password']);

        if (!$email) {
            sendResponse(400, "Adresse email invalide.");
        }

        if (strlen($passwordRaw) < 8) {
            sendResponse(400, "Le mot de passe doit contenir au moins 8 caractères.");
        }

        if (!preg_match('/^[0-9]{8}$/', $telephone)) {
            sendResponse(400, "Le numéro de téléphone est invalide.");
        }

        $password = password_hash($passwordRaw, PASSWORD_DEFAULT);
        $idUser = uniqid('user_', true);

        
        $checkQuery = "SELECT * FROM utilisateur WHERE username = ? OR email = ? OR telephone = ?";
        $stmt = $conx->prepare($checkQuery);
        if (!$stmt) {
            sendResponse(500, "Erreur lors de la préparation (check) : " . $conx->error);
        }

        $stmt->bind_param("sss", $username, $email, $telephone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            sendResponse(409, "Nom d'utilisateur, email ou téléphone déjà utilisé.");
        } else {
            $insertQuery = "INSERT INTO utilisateur (idUser, username, nom, prenom, email, password, telephone) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conx->prepare($insertQuery);
            if (!$stmt) {
                sendResponse(500, "Erreur lors de la préparation (insert) : " . $conx->error);
            }

            $stmt->bind_param("sssssss", $idUser, $username, $nom, $prenom, $email, $password, $telephone);

            if ($stmt->execute()) {
                sendResponse(200, "Inscription réussie !");
            } else {
                sendResponse(500, "Erreur lors de l'insertion : " . $stmt->error);
            }
        }

        $stmt->close();
    } else {
        sendResponse(400, "Tous les champs du formulaire doivent être remplis.");
    }
}
?>
