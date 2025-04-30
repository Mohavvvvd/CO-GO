<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

include("./config/connect.php");

// Get admin info from session
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? 'admin@coandgo.com';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Co&Go</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      min-height: 100vh;
      background-color: #3f37c9;
    }
    .sidebar a {
      color: #adb5bd;
      text-decoration: none;
      transition: all 0.2s;
    }
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background-color: #0d6efd;
      color: #fff;
    }
    .sidebar .nav-link i {
      font-size: 1.2rem;
    }
    .sidebar .logo {
      font-size: 1.5rem;
      font-weight: bold;
      margin-bottom: 1rem;
    }
    iframe {
      width: 100%;
      height: calc(100vh - 2rem);
      border: none;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .admin-info {
      border-top: 1px solid #495057;
      padding-top: 1rem;
      margin-top: auto;
      width: 100%;
    }
    .admin-info .logout {
      color: #dc3545;
    }
    .admin-info .logout:hover {
      color: #fff;
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row flex-nowrap">
    <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 sidebar d-flex flex-column">
      <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-3 text-white">
        <span class="logo d-none d-sm-inline text-light">CO&GO - Admins</span>
        <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start w-100" id="menu">
          <li class="nav-item w-100">
            <a href="./adminTem/dashboard.php" class="nav-link text-white" target="content-frame">
              <i class="bi bi-house-door"></i> <span class="ms-2 d-none d-sm-inline">Dashboard</span>
            </a>
          </li>
          <li class="w-100">
            <a href="./adminTem/reservation.php" class="nav-link text-white" target="content-frame">
              <i class="bi bi-calendar-check"></i> <span class="ms-2 d-none d-sm-inline">Reservations</span>
            </a>
          </li>
          <li class="w-100">
            <a href="./adminTem/users.php" class="nav-link text-white" target="content-frame">
              <i class="bi bi-people"></i> <span class="ms-2 d-none d-sm-inline">Users</span>
            </a>
          </li>
          <li class="w-100">
            <a href="./adminTem/spaces.php" class="nav-link text-white" target="content-frame">
              <i class="bi bi-building"></i> <span class="ms-2 d-none d-sm-inline">Spaces</span>
            </a>
          </li>
          <li class="w-100">
            <a href="./adminTem/eq.php" class="nav-link text-white" target="content-frame">
              <i class="bi bi-hammer"></i> <span class="ms-2 d-none d-sm-inline">Equipment</span>
            </a>
          </li>
          <li class="w-100">
            <a href="./adminTem/payment.php" class="nav-link text-white" target="content-frame">
              <i class="bi bi-credit-card"></i> <span class="ms-2 d-none d-sm-inline">Payments</span>
            </a>
          </li>
          <li class="w-100">
            <a href="./adminTem/contact.php" class="nav-link text-white" target="content-frame">
              <i class="bi bi-chat"></i> <span class="ms-2 d-none d-sm-inline">Contact</span>
            </a>
          </li>
        </ul>
        <div class="admin-info text-start text-white">
          <div class="fw-bold">Admin: <?php echo htmlspecialchars($admin_name); ?></div>
          <div class="text-secondary small"><?php echo htmlspecialchars($admin_email); ?></div>
          <a href="logout.php" class="logout mt-2 d-block"><i class="bi bi-box-arrow-right"></i> <span class="ms-1">Logout</span></a>
        </div>
      </div>
    </div>
    <div class="col py-3">
      <iframe name="content-frame" src="./adminTem/dashboard.php"></iframe>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>