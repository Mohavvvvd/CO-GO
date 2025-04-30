<?php
session_start();
include("../config/connect.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Delete handler
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conx->prepare("DELETE FROM contact WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo '<script>window.open("contact.php", "content-frame");</script>';
    exit;
}

// Search functionality
$searchTerm = '';
$whereClause = '';
if (!empty($_POST['search'])) {
    $searchTerm = "%".trim($_POST['search'])."%";
    $whereClause = "WHERE username LIKE ? OR email LIKE ? OR message LIKE ?";
}

// Fetch messages
$query = "SELECT *, DATE_FORMAT(sent_at, '%b %d, %Y %H:%i') AS formatted_date 
          FROM contact $whereClause 
          ORDER BY sent_at DESC";
$stmt = $conx->prepare($query);

if (!empty($searchTerm)) {
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Co&Go</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .card-shadow { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); }
        .table-header { background: #2c3e50; color: white; }
        .hover-effect:hover { transform: translateY(-2px); transition: all 0.3s ease; }
        .message-preview { max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card card-shadow">
            <div class="card-header table-header">
                <h2 class="mb-0 text-center"><i class="bi bi-envelope"></i> Contact Messages</h2>
            </div>
            
            <div class="card-body">
                <!-- Search Form -->
                <form method="post" class="mb-4">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control form-control-lg" 
                               name="search" 
                               placeholder="Search messages..." 
                               value="<?= htmlspecialchars($_POST['search'] ?? '') ?>">
                        <button class="btn btn-primary btn-lg" type="submit">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </form>

                <!-- Messages Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-header">
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <?php
                                    // Email processing
                                    $email = filter_var(trim($row['email']), FILTER_SANITIZE_EMAIL);
                                    $isValidEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
                                    $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
                                    ?>
                                    <tr class="hover-effect align-middle">
                                        <td><?= $row['id'] ?></td>
                                        <td>
                                            <?php if ($row['idUser']): ?>
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-person-check"></i> <?= htmlspecialchars($row['username']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-person"></i> Guest
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $safeEmail ?></td>
                                        <td class="message-preview" data-fullmessage="<?= htmlspecialchars($row['message']) ?>">
                                            <?= htmlspecialchars($row['message']) ?>
                                        </td>
                                        <td><?= $row['formatted_date'] ?></td>
                                        <td>
                                            <button class="btn btn-info btn-sm view-message">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                            <a href="?delete=<?= $row['id'] ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Delete this message permanently?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                            <?php if ($isValidEmail): ?>
                                                <a href="mailto:<?= $safeEmail ?>" 
                                                   class="btn btn-success btn-sm"
                                                   onclick="return handleMailto(this)">
                                                    <i class="bi bi-reply"></i> Reply
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">Invalid email</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-envelope-slash display-6"></i>
                                        <div class="mt-2">No messages found</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Message View Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-envelope-open"></i> Full Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <pre class="message-content" style="white-space: pre-wrap;"></pre>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // View message functionality
        document.querySelectorAll('.view-message').forEach(button => {
            button.addEventListener('click', () => {
                const fullMessage = button.closest('tr').querySelector('.message-preview').dataset.fullmessage;
                const modal = new bootstrap.Modal(document.getElementById('messageModal'));
                document.querySelector('.message-content').textContent = fullMessage;
                modal.show();
            });
        });

        // Mailto handler with fallback
        function handleMailto(link) {
            try {
                // Try to open default mail client
                window.location.href = link.href;
            } catch (e) {
                // Fallback for browsers that block mailto
                const email = link.href.replace('mailto:', '');
                alert(`Email client not detected. Please contact:\n${email}`);
                navigator.clipboard.writeText(email);
                return false;
            }
            return false;
        }
    </script>
</body>
</html>