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

// Handle delete functionality
if (isset($_GET['delete'])) {
    $idEquipment = $_GET['delete'];
    $deleteQuery = "DELETE FROM equipement WHERE idEquipment = ?";
    $stmt = $conx->prepare($deleteQuery);
    $stmt->bind_param("s", $idEquipment);
    $stmt->execute();
    $stmt->close();
    echo '<script>window.open("eq.php", "content-frame");</script>';
    exit;
}

// Handle update functionality
if (isset($_POST['update'])) {
    $idEquipment = $_POST['idEquipment'];
    $typeEquipement = $_POST['typeEquipement'];
    $nomEquipment = $_POST['nomEquipment'];
    $prix = $_POST['prix'];
    $nbEquipement = $_POST['nbEquipement'];
    $rating = $_POST['rating'];

    $stmt = $conx->prepare("UPDATE equipement SET 
                          typeEquipement = ?,
                          nomEquipment = ?,
                          prix = ?,
                          nbEquipement = ?,
                          rating = ?
                          WHERE idEquipment = ?");
    $stmt->bind_param("ssdids", $typeEquipement, $nomEquipment, $prix, $nbEquipement, $rating, $idEquipment);
    $stmt->execute();
    $stmt->close();
    echo '<script>window.open("eq.php", "content-frame");</script>';
    exit;   
}

$equipmentQuery = "SELECT * FROM equipement";
if (!empty($searchTerm)) {
    $searchTerm = $conx->real_escape_string($searchTerm);
    $equipmentQuery .= " WHERE nomEquipment LIKE '%$searchTerm%' 
                        OR typeEquipement LIKE '%$searchTerm%'
                        OR idEquipment LIKE '%$searchTerm%'";
}
$equipmentResult = $conx->query($equipmentQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Management - Co&Go</title>
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
        .actions-cell {
            min-width: 150px;
        }
        .equipment-icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        .type-badge {
            font-size: 0.85rem;
            padding: 0.35rem 0.7rem;
            border-radius: 20px;
        }
        .quantity-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card card-shadow">
            <div class="card-header table-header">
                <h2 class="mb-0 text-center"><i class="bi bi-tools"></i> Equipment Management</h2>
            </div>
            
            <div class="card-body">
                <!-- Search Form -->
                <form method="post" class="mb-4">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control form-control-lg" 
                               name="search" 
                               placeholder="Search by name, type, or ID..." 
                               value="<?= htmlspecialchars($searchTerm) ?>">
                        <button class="btn btn-primary btn-lg" type="submit">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </form>

                <!-- Equipment Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-header">
                            <tr>
                                <th>ID</th>
                                <th>Equipment Name</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Rating</th>
                                <th class="actions-cell">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($equipmentResult->num_rows > 0): ?>
                                <?php while ($row = $equipmentResult->fetch_assoc()): ?>
                                    <tr class="hover-effect align-middle">
                                        <td>#<?= htmlspecialchars($row['idEquipment']) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-gear equipment-icon"></i>
                                                <div>
                                                    <strong><?= htmlspecialchars($row['nomEquipment']) ?></strong>
                                                    <div class="text-muted small">ID: <?= htmlspecialchars($row['idEquipment']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="type-badge badge 
                                                <?= match($row['typeEquipement']) {
                                                    'audio' => 'bg-primary',
                                                    'video' => 'bg-success',
                                                    'meuble' => 'bg-warning',
                                                    default => 'bg-secondary'
                                                } ?>">
                                                <?= ucfirst($row['typeEquipement']) ?>
                                            </span>
                                        </td>
                                        <td class="text-success fw-bold">
                                            <?= number_format($row['prix'], 2) ?> €
                                        </td>
                                        <td>
                                            <span class="quantity-badge badge 
                                                <?= $row['nbEquipement'] > 0 ? 'bg-info' : 'bg-danger' ?>">
                                                <i class="bi bi-box"></i> <?= htmlspecialchars($row['nbEquipement']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="rating-stars text-warning">
                                                <?= str_repeat('★', floor($row['rating'])) ?>
                                                <?= ($row['rating'] - floor($row['rating']) >= 0.5 ? '½' : '' )?>
                                            </div>
                                            <small class="text-muted">(<?= number_format($row['rating'], 1) ?>/5)</small>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#updateModal"
                                                    data-id="<?= htmlspecialchars($row['idEquipment']) ?>"
                                                    data-type="<?= htmlspecialchars($row['typeEquipement']) ?>"
                                                    data-name="<?= htmlspecialchars($row['nomEquipment']) ?>"
                                                    data-price="<?= htmlspecialchars($row['prix']) ?>"
                                                    data-quantity="<?= htmlspecialchars($row['nbEquipement']) ?>"
                                                    data-rating="<?= htmlspecialchars($row['rating']) ?>">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <a href="?delete=<?= htmlspecialchars($row['idEquipment']) ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Are you sure? This action cannot be undone.')">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-tools display-6"></i>
                                        <div class="mt-2">No equipment found</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-gear"></i> Edit Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="idEquipment" id="modalIdEquipment">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Equipment Type</label>
                                <select class="form-select" name="typeEquipement" required>
                                    <option value="audio">Audio</option>
                                    <option value="video">Video</option>
                                    <option value="meuble">Furniture</option>
                                    <option value="autre">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Equipment Name</label>
                                <input type="text" class="form-control" name="nomEquipment" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price (€)</label>
                                <input type="number" class="form-control" name="prix" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control" name="nbEquipement" min="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Rating (0-5)</label>
                                <input type="number" class="form-control" name="rating" min="0" max="5" step="0.1" required>
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
        const updateModal = document.getElementById('updateModal');
        updateModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const equipmentData = {
                id: button.getAttribute('data-id'),
                type: button.getAttribute('data-type'),
                name: button.getAttribute('data-name'),
                price: button.getAttribute('data-price'),
                quantity: button.getAttribute('data-quantity'),
                rating: button.getAttribute('data-rating')
            };

            document.getElementById('modalIdEquipment').value = equipmentData.id;
            document.querySelector('#updateModal [name="typeEquipement"]').value = equipmentData.type;
            document.querySelector('#updateModal [name="nomEquipment"]').value = equipmentData.name;
            document.querySelector('#updateModal [name="prix"]').value = equipmentData.price;
            document.querySelector('#updateModal [name="nbEquipement"]').value = equipmentData.quantity;
            document.querySelector('#updateModal [name="rating"]').value = equipmentData.rating;
        });
    </script>
</body>
</html>