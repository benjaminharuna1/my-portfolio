<?php
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Admin Dashboard';
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
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block bg-dark sidebar">
                <div class="position-sticky pt-3">
                    <h5 class="text-white px-3 mb-4">Admin Panel</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo SITE_URL; ?>/admin/dashboard.php">
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
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/messages.php">
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

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                    <h1>Dashboard</h1>
                    <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>

                <div class="row mt-4">
                    <div class="col-md-3 mb-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-briefcase fa-2x text-primary mb-3"></i>
                                <h5 class="card-title">Portfolio Items</h5>
                                <p class="card-text display-6">
                                    <?php
                                    $count = $conn->query("SELECT COUNT(*) as count FROM portfolio_items")->fetch_assoc();
                                    echo $count['count'];
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-cogs fa-2x text-success mb-3"></i>
                                <h5 class="card-title">Services</h5>
                                <p class="card-text display-6">
                                    <?php
                                    $count = $conn->query("SELECT COUNT(*) as count FROM services")->fetch_assoc();
                                    echo $count['count'];
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-envelope fa-2x text-warning mb-3"></i>
                                <h5 class="card-title">Messages</h5>
                                <p class="card-text display-6">
                                    <?php
                                    $count = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")->fetch_assoc();
                                    echo $count['count'];
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-share-alt fa-2x text-info mb-3"></i>
                                <h5 class="card-title">Social Links</h5>
                                <p class="card-text display-6">
                                    <?php
                                    $count = $conn->query("SELECT COUNT(*) as count FROM social_links")->fetch_assoc();
                                    echo $count['count'];
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Recent Messages</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Message</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
                                        while ($msg = $result->fetch_assoc()):
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                                <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($msg['message'], 0, 50)); ?>...</td>
                                                <td><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></td>
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
