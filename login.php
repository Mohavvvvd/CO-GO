<?php
session_start();
include("./config/connect.php");

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (!empty($email) && !empty($password)) {
        $stmt = $conx->prepare("SELECT idAdmin, AdminName, email, password FROM admin WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['idAdmin'];
            $_SESSION['admin_name'] = $admin['AdminName'];
            $_SESSION['admin_email'] = $admin['email'];
            
            header("Location: admin.php");
            exit;
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Please enter both email and password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Co&Go</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .login-card {
      width: 100%;
      max-width: 400px;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .logo {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 1.8rem;
      font-weight: bold;
      color: #0d6efd;
    }
  </style>
</head>
<body>
  <div class="login-card bg-white">
    <div class="logo">Co&Go</div>
    <h2 class="text-center mb-4">Admin Login</h2>
    
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="login.php">
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>