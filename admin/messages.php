<?php
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Messages';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM contact_messages WHERE id = $id");
}

if (isset($_GET['mark_read'])) {
    $id = intval($_GET['mark_read']);
    $conn->query("UPDATE contact_messages SET is_read = 1 WHERE id = $id");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-md-block bg-dark sidebar">
                <div class="position-sticky pt-3">
                    <h5 class="text-white px-3 mb-4">Admin Panel</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/dashboard.php">
                                <i class="fas fa-chart-line"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/portfolio.php">
                                <i class="fas fa-briefcase"></i> Portfolio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/services.php">
                                <i class="fas fa-cogs"></i> Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/about.php">
                                <i class="fas fa-user"></i> About
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo SITE_URL; ?>/admin/messages.php">
                                <i class="fas fa-envelope"></i> Messages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/social.php">
                                <i class="fas fa-share-alt"></i> Social Links
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                    <h1>Contact Messages</h1>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Message</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
                                        while ($msg = $result->fetch_assoc()):
                                        ?>
                                            <tr <?php echo !$msg['is_read'] ? 'class="table-light"' : ''; ?>>
                                                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                                <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($msg['message'], 0, 50)); ?>...</td>
                                                <td><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></td>
                                                <td>
                                                    <?php if ($msg['is_read']): ?>
                                                        <span class="badge bg-success">Read</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Unread</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!$msg['is_read']): ?>
                                                        <a href="?mark_read=<?php echo $msg['id']; ?>" class="btn btn-sm btn-info">Mark Read</a>
                                                    <?php endif; ?>
                                                    <a href="?delete=<?php echo $msg['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
