<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

include("../config/connect.php");

// Initialize variables
$searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';

// Update functionality
if (isset($_POST['update'])) {
    $numLocal = $_POST['numLocal'];
    $typeLocal = $_POST['typeLocal'];
    $NbLocal = $_POST['NbLocal'];
    $prix = $_POST['prix'];
    $rating = $_POST['rating'];
    $capacite = $_POST['capacite'];
    $surface = $_POST['surface'];

    $stmt = $conx->prepare("UPDATE local SET 
                          typeLocal = ?,
                          NbLocal = ?,
                          prix = ?,
                          rating = ?,
                          capacite = ?,
                          surface = ?
                          WHERE numLocal = ?");
    $stmt->bind_param("siddisi", $typeLocal, $NbLocal, $prix, $rating, $capacite, $surface, $numLocal);
    $stmt->execute();
    echo '<script>window.open("spaces.php", "content-frame");</script>';
    exit;
}

// Fetch data
$query = "SELECT * FROM local";
if (!empty($searchTerm)) {
    $searchTerm = "%$searchTerm%";
    $query = "SELECT * FROM local 
              WHERE typeLocal LIKE ?
              OR capacite LIKE ?";
    $stmt = $conx->prepare($query);
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $localsResult = $stmt->get_result();
} else {
    $localsResult = $conx->query($query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Space Management</title>
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
        .availability-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
        .rating-stars {
            color: #ffc107;
            letter-spacing: 2px;
        }
        .hover-effect:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card card-shadow">
            <div class="card-header table-header">
                <h2 class="mb-0 text-center"><i class="bi bi-buildings"></i> Space Management</h2>
            </div>
            
            <div class="card-body">
                <!-- Search Form -->
                <form method="post" class="mb-4">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control form-control-lg" 
                               name="search" 
                               placeholder="Search by type or capacity..." 
                               value="<?= htmlspecialchars($searchTerm) ?>">
                        <button class="btn btn-primary btn-lg" type="submit">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </form>

                <!-- Spaces Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-header">
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Availability</th>
                                <th>Price</th>
                                <th>Rating</th>
                                <th>Capacity</th>
                                <th>Surface</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($localsResult->num_rows > 0): ?>
                                <?php while ($local = $localsResult->fetch_assoc()): ?>
                                    <tr class="hover-effect align-middle">
                                        <td>#<?= $local['numLocal'] ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-building me-2"></i>
                                                <div>
                                                    <strong><?= ucfirst($local['typeLocal']) ?></strong>
                                                    <?php if ($local['NbLocal'] == 0): ?>
                                                        <div class="text-danger small">Not Available</div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="availability-badge <?= $local['NbLocal'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $local['NbLocal'] ?>
                                            </span>
                                        </td>
                                        <td class="text-success fw-bold">
                                            <?= number_format($local['prix'], 2) ?> TND
                                        </td>
                                        <td>
                                            <div class="rating-stars">
                                                <?= str_repeat('★', floor($local['rating'])) ?>
                                                <?= ($local['rating'] - floor($local['rating']) >= 0.5 ? '½' : '' )?>
                                            </div>
                                            <small class="text-muted">(<?= number_format($local['rating'], 1) ?>/5)</small>
                                        </td>
                                        <td><?= $local['capacite'] ?> people</td>
                                        <td><?= htmlspecialchars($local['surface']) ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal"
                                                    data-local='<?= htmlspecialchars(json_encode($local)) ?>'>
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-info-circle display-6"></i>
                                        <div class="mt-2">No spaces found</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Space</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="numLocal" id="editNumLocal">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Type</label>
                                <select class="form-select" name="typeLocal" required>
                                    <option value="public">Public</option>
                                    <option value="private">Private</option>
                                    <option value="meeting">Meeting</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Available Units</label>
                                <input type="number" class="form-control" name="NbLocal" min="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price (TND)</label>
                                <input type="number" class="form-control" name="prix" step="0.01" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Capacity</label>
                                <input type="number" class="form-control" name="capacite" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Rating (0-5)</label>
                                <input type="number" class="form-control" name="rating" min="0" max="5" step="0.1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Surface Area</label>
                                <input type="text" class="form-control" name="surface" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('editModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const localData = JSON.parse(button.dataset.local);
            
            document.getElementById('editNumLocal').value = localData.numLocal;
            document.querySelector('#editModal [name="typeLocal"]').value = localData.typeLocal;
            document.querySelector('#editModal [name="NbLocal"]').value = localData.NbLocal;
            document.querySelector('#editModal [name="prix"]').value = localData.prix;
            document.querySelector('#editModal [name="rating"]').value = localData.rating;
            document.querySelector('#editModal [name="capacite"]').value = localData.capacite;
            document.querySelector('#editModal [name="surface"]').value = localData.surface;
        });
    </script>
</body>
</html>