<?php
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$message = '';
$success = false;

// Check if greeting column exists
$result = $conn->query("SHOW COLUMNS FROM about LIKE 'greeting'");
if ($result->num_rows === 0) {
    // Add the greeting column
    if ($conn->query("ALTER TABLE about ADD COLUMN greeting VARCHAR(255) DEFAULT NULL AFTER id")) {
        $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Migration successful: greeting column added to about table.</div>';
        $success = true;
    } else {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Migration failed: ' . $conn->error . '</div>';
    }
} else {
    $message = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> Migration skipped: greeting column already exists.</div>';
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../includes/admin-head.php'; ?>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Database Migration</h5>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        <?php if ($success): ?>
                            <p class="mt-3">The greeting message feature has been successfully set up. You can now:</p>
                            <ul>
                                <li>Go to <strong>Manage About</strong> to add a greeting message</li>
                                <li>The greeting will appear on the hero section of your homepage</li>
                            </ul>
                            <a href="<?php echo SITE_URL; ?>/admin/about.php" class="btn btn-primary">Go to Manage About</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
