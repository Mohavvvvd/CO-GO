<?php
session_start();
include("../config/connect.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Handle search functionality
$searchTerm = '';
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
}

// Fetch reservations with optional search
$reservationsQuery = "SELECT r.idReservation, u.nom, u.prenom, u.email, r.startDate, r.endDate, r.nbPeople, r.montant, r.equipement, r.idPaiment
                      FROM reservation r
                      JOIN utilisateur u ON r.idUser  = u.idUser ";

if (!empty($searchTerm)) {
    $searchTerm = $conx->real_escape_string($searchTerm);
    
    $terms = explode(' ', $searchTerm);
    $conditions = [];
    
    foreach ($terms as $term) {
        if (!empty($term)) {
            $conditions[] = "(r.idReservation LIKE '%$term%' 
                            OR u.nom LIKE '%$term%' 
                            OR u.prenom LIKE '%$term%'
                            OR CONCAT(u.nom, ' ', u.prenom) LIKE '%$term%'
                            OR CONCAT(u.prenom, ' ', u.nom) LIKE '%$term%'
                            OR u.email LIKE '%$term%')";
        }
    }
    
    if (!empty($conditions)) {
        $reservationsQuery .= " WHERE " . implode(' AND ', $conditions);
    }
}

$reservationsQuery .= " ORDER BY r.startDate DESC";
$reservationsResult = $conx->query($reservationsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations - Co&Go</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .card-shadow {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .table-header {
            background: #2c3e50;
            color: white;
        }
        .hover-effect:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        .status-badge {
            font-size: 0.85rem;
            padding: 0.35rem 0.7rem;
            border-radius: 20px;
        }
        .equipment-list {
            max-width: 250px;
            white-space: normal;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card card-shadow">
            <div class="card-header table-header">
                <h2 class="mb-0 text-center"><i class="bi bi-calendar-event"></i> Reservation Management</h2>
            </div>
            
            <div class="card-body">
                <!-- Search Form -->
                <form method="POST" class="mb-4">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control form-control-lg" 
                               name="search" 
                               placeholder="Search by ID, name, or email..." 
                               value="<?= htmlspecialchars($searchTerm) ?>">
                        <button class="btn btn-primary btn-lg" type="submit">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </form>

                <!-- Reservations Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-header">
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Dates</th>
                                <th>People</th>
                                <th>Amount</th>
                                <th>Equipment</th>
                                <th>Payment ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($reservationsResult->num_rows > 0): ?>
                                <?php while ($row = $reservationsResult->fetch_assoc()): ?>
                                    <tr class="hover-effect align-middle">
                                        <td>#<?= htmlspecialchars($row['idReservation']) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle me-2"></i>
                                                <div>
                                                    <strong><?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?></strong>
                                                    <div class="text-muted small"><?= htmlspecialchars($row['email']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-primary"><?= date('M d, Y', strtotime($row['startDate'])) ?></span>
                                                <span class="text-muted small">to</span>
                                                <span class="text-primary"><?= date('M d, Y', strtotime($row['endDate'])) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary status-badge">
                                                <i class="bi bi-people"></i> <?= htmlspecialchars($row['nbPeople']) ?>
                                            </span>
                                        </td>
                                        <td class="text-success fw-bold">
                                            <?= number_format($row['montant'], 2) ?> €
                                        </td>
                                        <td class="equipment-list">
                                            <?php 
                                                $equipments = json_decode($row['equipement'], true);
                                                if (is_array($equipments) && !empty($equipments)) {
                                                    echo '<div class="d-flex flex-wrap gap-1">';
                                                    foreach ($equipments as $equipment) {
                                                        echo '<span class="badge bg-secondary">'.htmlspecialchars($equipment).'</span>';
                                                    }
                                                    echo '</div>';
                                                } else {
                                                    echo '—';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <code class="text-muted"><?= htmlspecialchars($row['idPaiment']) ?></code>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-calendar-x display-6"></i>
                                        <div class="mt-2">No reservations found</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>