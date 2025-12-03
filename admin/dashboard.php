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
    <?php include '../includes/admin-head.php'; ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/admin-sidebar.php'; ?>

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
