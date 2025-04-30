<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

include("../config/connect.php");

$searchTerm = '';
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
}


$paymentsQuery = "SELECT p.*, u.nom, u.prenom 
                 FROM paiment p
                 JOIN utilisateur u ON p.idUser = u.idUser";

if (!empty($searchTerm)) {
    $searchTerm = $conx->real_escape_string($searchTerm);
    $paymentsQuery .= " WHERE p.idFacture LIKE '%$searchTerm%'
                        OR u.nom LIKE '%$searchTerm%'
                        OR u.prenom LIKE '%$searchTerm%'
                        OR p.montant LIKE '%$searchTerm%'
                        OR p.idReservation LIKE '%$searchTerm%'";
}

$paymentsQuery .= " ORDER BY p.paymentDate DESC";
$paymentsResult = $conx->query($paymentsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Management - Co&Go</title>
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
        .payment-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            padding: 1rem;
        }
        .credit-card-display {
            font-family: monospace;
            letter-spacing: 2px;
        }
        .amount-badge {
            font-size: 1.1rem;
            padding: 0.5rem 1.2rem;
            border-radius: 25px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card card-shadow">
            <div class="card-header table-header">
                <h2 class="mb-0 text-center"><i class="bi bi-credit-card"></i> Payment Management</h2>
            </div>
            
            <div class="card-body">
                <!-- Search Form -->
                <form method="post" class="mb-4">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control form-control-lg" 
                               name="search" 
                               placeholder="Search by ID, name, amount, or reservation..." 
                               value="<?= htmlspecialchars($searchTerm) ?>">
                        <button class="btn btn-primary btn-lg" type="submit">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </form>

                <!-- Payments Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-header">
                            <tr>
                                <th>Invoice ID</th>
                                <th>Client</th>
                                <th>Card Number</th>
                                <th>Amount</th>
                                <th>Payment Date</th>
                                <th>Reservation ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($paymentsResult->num_rows > 0): ?>
                                <?php while ($row = $paymentsResult->fetch_assoc()): ?>
                                    <tr class="hover-effect align-middle">
                                        <td>#<?= htmlspecialchars($row['idFacture']) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle me-2"></i>
                                                <div>
                                                    <strong><?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?></strong>
                                                    <div class="text-muted small">User ID: <?= htmlspecialchars($row['idUser']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="credit-card-display">
                                            •••• <?= substr(htmlspecialchars($row['numCreditCard']), -4) ?>
                                        </td>
                                        <td>
                                            <span class="amount-badge badge bg-success">
                                                <?= number_format($row['montant'], 2) ?> €
                                            </span>
                                        </td>
                                        <td>
                                            <?= date('M d, Y H:i', strtotime($row['paymentDate'])) ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                #<?= htmlspecialchars($row['idReservation']) ?>
                                            </span>
                                        </td>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-credit-card display-6"></i>
                                        <div class="mt-2">No payments found</div>
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