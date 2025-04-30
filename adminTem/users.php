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
    $idUser   = $_GET['delete'];
    $deleteQuery = "DELETE FROM utilisateur WHERE idUser = ?";
    $stmt = $conx->prepare($deleteQuery);
    $stmt->bind_param("s", $idUser);
    $stmt->execute();
    $stmt->close();
    echo '<script>window.open("users.php", "content-frame");</script>';
    exit;
}

// Handle update functionality
if (isset($_POST['update'])) {
    $idUser   = $_POST['idUser'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $password = $_POST['password'];

    // Hash the password if it's provided
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, telephone = ?, password = ? WHERE idUser = ?";
        $stmt = $conx->prepare($updateQuery);
        $stmt->bind_param("ssssss", $nom, $prenom, $email, $telephone, $hashedPassword, $idUser);
    } else {
        // Update without changing the password
        $updateQuery = "UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, telephone = ? WHERE idUser = ?";
        $stmt = $conx->prepare($updateQuery);
        $stmt->bind_param("sssss", $nom, $prenom, $email, $telephone, $idUser);
    }
    
    $stmt->execute();
    $stmt->close();
    echo '<script>window.open("users.php", "content-frame");</script>';
    exit;   
}

$usersQuery = "SELECT * FROM utilisateur";
if (!empty($searchTerm)) {
    $searchTerm = $conx->real_escape_string($searchTerm);
    $usersQuery .= " WHERE nom LIKE '%$searchTerm%' 
                     OR prenom LIKE '%$searchTerm%' 
                     OR email LIKE '%$searchTerm%'
                     OR username LIKE '%$searchTerm%'
                     OR CONCAT(nom, ' ', prenom) LIKE '%$searchTerm%'
                     OR CONCAT(prenom, ' ', nom) LIKE '%$searchTerm%'
                     OR CONCAT(nom, prenom) LIKE '%".str_replace(' ', '', $searchTerm)."%'";
}
$usersResult = $conx->query($usersQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Co&Go</title>
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
        .user-icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        .x{
            width: 83px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card card-shadow">
            <div class="card-header table-header">
                <h2 class="mb-0 text-center"><i class="bi bi-people"></i> User Management</h2>
            </div>
            
            <div class="card-body">
                <!-- Search Form -->
                <form method="post" class="mb-4">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control form-control-lg" 
                               name="search" 
                               placeholder="Search by name, email, or username..." 
                               value="<?= htmlspecialchars($searchTerm) ?>">
                        <button class="btn btn-primary btn-lg" type="submit">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </form>

                <!-- Users Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-header">
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Password</th>
                                <th class="actions-cell">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($usersResult->num_rows > 0): ?>
                                <?php while ($row = $usersResult->fetch_assoc()): ?>
                                    <tr class="hover-effect align-middle">
                                        <td>#<?= htmlspecialchars($row['idUser']) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle user-icon"></i>
                                                <div>
                                                    <strong><?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?></strong>
                                                    <div class="text-muted small"><?= htmlspecialchars($row['email']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($row['username']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['telephone']) ?></td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-lock"></i> ********
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn x btn-warning btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#updateModal"
                                                    data-id="<?= htmlspecialchars($row['idUser']) ?>"
                                                    data-nom="<?= htmlspecialchars($row['nom']) ?>"
                                                    data-prenom="<?= htmlspecialchars($row['prenom']) ?>"
                                                    data-email="<?= htmlspecialchars($row['email']) ?>"
                                                    data-telephone="<?= htmlspecialchars($row['telephone']) ?>">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <a href="?delete=<?= htmlspecialchars($row['idUser']) ?>" 
                                               class="btn x btn-danger btn-sm"
                                               onclick="return confirm('Are you sure? This action cannot be undone.')">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-people display-6"></i>
                                        <div class="mt-2">No users found</div>
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
                    <h5 class="modal-title"><i class="bi bi-person-gear"></i> Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="idUser" id="modalIdUser">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" id="modalPrenom" name="prenom" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="modalNom" name="nom" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="modalEmail" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="modalTelephone" name="telephone" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">New Password (leave blank to keep current)</label>
                                <input type="password" class="form-control" name="password" placeholder="••••••••">
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
            const id = button.getAttribute('data-id');
            const nom = button.getAttribute('data-nom');
            const prenom = button.getAttribute('data-prenom');
            const email = button.getAttribute('data-email');
            const telephone = button.getAttribute('data-telephone');

            document.getElementById('modalIdUser').value = id;
            document.getElementById('modalNom').value = nom;
            document.getElementById('modalPrenom').value = prenom;
            document.getElementById('modalEmail').value = email;
            document.getElementById('modalTelephone').value = telephone;
        });
    </script>
</body>
</html>