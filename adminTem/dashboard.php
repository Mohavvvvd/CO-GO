<?php
session_start();
include("../config/connect.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

function countRecords($conx, $table, $condition = "") {
    $sql = "SELECT COUNT(*) as total FROM $table";
    if (!empty($condition)) {
        $sql .= " WHERE $condition";
    }
    $result = $conx->query($sql);
    return $result->fetch_assoc()['total'];
}
$totalMessages = countRecords($conx, table: "contact"); 
$totalReservations = countRecords($conx, "reservation");
$totalUsers = countRecords($conx, "utilisateur");
$adminCount = countRecords($conx, "admin");
$deletedUsers = 0;

$lastUsersQuery = "SELECT * FROM utilisateur ORDER BY date_joined DESC LIMIT 5";
$lastUsersResult = $conx->query($lastUsersQuery);
$reservationsQuery = "SELECT r.*, u.* 
FROM reservation r
JOIN utilisateur u ON r.idUser  = u.idUser
                      ORDER BY r.startDate DESC
                      LIMIT 5";
$reservationsResult = $conx->query($reservationsQuery);
if (!$reservationsResult) {
    die("Query failed: " . $conx->error);
}


// Fetch unavailable equipment
$EquipmentQuery = "SELECT * FROM equipement";
$availableEquipmentResult = $conx->query($EquipmentQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Overview - Co&Go</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        /* Add your CSS styles here */
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 20px rgba(67, 97, 238, 0.15);
        }
        
        .stat-card {
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1. 5rem;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            background: white;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .messages-count {
    color: #4361ee; /
}
        .stat-card h3 {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .stat-card .count {
            font-size: 2.2rem;
            font-weight: 700;
        }
        
        .total-users { color: var(--primary-color); }
        .active-users { color: #2ecc71; }
        .admins-count { color: #f39c12; }
        .deleted-users { color: #e74c3c; }
        
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-top: 2rem;
        }
        
        .table-title {
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .table-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: var(--accent-color);
        }
        
        .table thead th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 500;
        }
        
        .table tbody tr {
            transition: all 0.2s;
        }
        
        .table tbody tr:hover {
            background-color: rgba(72, 149, 239, 0.1);
        }
        
        .action-btn {
            border-radius: 6px;
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .edit-btn {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .cancel-btn {
            background-color: #e74c3c;
            border-color: #e74c3c;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .badge-admin {
            background-color: var(--primary-color);
        }
        
        .badge-visitor {
            background-color: #2ecc71;
        }
    </style>
</head>
<body>
    <div class="dashboard-header animate__animated animate__fadeIn">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-0">Users Overview</h1>
                    <p class="mb-0 opacity-75">Manage all user accounts and reservations</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge bg-light text-dark p-2">
                        <i class="bi bi-calendar-check me-1"></i>
                        <?php echo date('F j, Y'); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <!-- Statistics Cards -->
        <div class="row animate__animated animate__fadeInUp">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-people-fill text-primary fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h3>Total Users</h3>
                            <div class="count total-users"><?= $totalUsers ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
    <div class="stat-card">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                    <i class="bi bi-calendar-check-fill text-info fs-4"></i>
                </div>
            </div>
            <div>
                <h3>Total Reservations</h3>
                <div class="count reservations-count"><?= $totalReservations ?></div>
            </div>
        </div>
    </div>
</div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-shield-lock-fill text-warning fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h3>Admins</h3>
                            <div class="count admins-count"><?= $adminCount ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
    <div class="stat-card">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                    <i class="bi bi-envelope-fill text-primary fs-4"></i>
                </div>
            </div>
            <div>
                <h3>Total Messages</h3>
                <div class="count messages-count"><?= $totalMessages ?></div>
            </div>
        </div>
    </div>
</div>
        </div>
        
        <!-- Last 5 Users Table -->
        <div class="table-container animate__animated animate__fadeInUp animate__delay-1s">
            <h3 class="table-title">
                <i class="bi bi-person-lines-fill me-2"></i>Last 5 Users Who Joined
            </h3>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Date Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $lastUsersResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['idUser']) ?></td>
                                <td><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['date_joined']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

       <!-- Last 5 Reservations Table -->
<div class="table-container animate__animated animate__fadeInUp animate__delay-1s">
    <h3 class="table-title">
        <i class="bi bi-calendar-event me-2"></i>Last 5 Reservations
    </h3>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Reservation ID</th>
                    <th>User</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>People</th>
                    <th>Price</th>
                    <th>Equipment</th>
                    <th>Payment ID</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $reservationsResult->fetch_assoc()): ?>
                    <tr>
                        <td><span class="badge bg-primary"><?= htmlspecialchars($row['idReservation']) ?></span></td>
                        <td>
                            <?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?>
                            <small class="text-muted d-block">ID: <?= htmlspecialchars($row['idUser']) ?></small>
                        </td>
                        <td><?= date('M j, Y', strtotime($row['startDate'])) ?></td>
                        <td><?= date('M j, Y', strtotime($row['endDate'])) ?></td>
                        <td><?= htmlspecialchars($row['nbPeople']) ?></td>
                        <td><?= number_format($row['montant'], 2) ?>€</td>
                        <td>
                            <?php 
                                $equipments = json_decode($row['equipement'], true);
                                if (is_array($equipments) && !empty($equipments)) {
                                    echo '<ul class="list-unstyled mb-0">';
                                    foreach ($equipments as $equip) {
                                        echo '<li>' . htmlspecialchars($equip) . '</li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo '<span class="text-muted">None</span>';
                                }
                            ?>
                        </td>
                        <td><?= $row['idPaiment'] ? htmlspecialchars($row['idPaiment']) : '<span class="text-muted">Pending</span>' ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
        <!-- Equipment Table -->
        <div class="table-container animate__animated animate__fadeInUp animate__delay-1s">
            <h3 class="table-title">
                <i class="bi bi-tools me-2"></i>Equipment
            </h3>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Rating</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($equipment = $availableEquipmentResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($equipment['idEquipment']) ?></td>
                                <td><?= htmlspecialchars($equipment['nomEquipment']) ?></td>
                                <td><?= htmlspecialchars($equipment['nbEquipement'])??'Not avilable' ?></td>
                                <td><?= htmlspecialchars($equipment['rating']) ?></td>
                                <td><?= htmlspecialchars($equipment['prix']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add animation to table rows on hover
        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.classList.add('animate__pulse');
            });
            row.addEventListener('mouseleave', () => {
                row.classList.remove('animate__pulse');
            });
        });
    </script>
</body>
</html>