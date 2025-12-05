<?php
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$message = '';
$success = false;

// Check if skills table exists
$result = $conn->query("SHOW TABLES LIKE 'skills'");
if ($result->num_rows === 0) {
    // Create the skills table
    $create_table_sql = "CREATE TABLE IF NOT EXISTS `skills` (
        `id` int NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `proficiency` int NOT NULL CHECK (`proficiency` >= 0 AND `proficiency` <= 100),
        `category` varchar(100) DEFAULT NULL,
        `icon` varchar(100) DEFAULT NULL,
        `sort_order` int DEFAULT '0',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `sort_order` (`sort_order`),
        KEY `category` (`category`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
    
    if ($conn->query($create_table_sql)) {
        // Insert sample skills data
        $insert_sql = "INSERT INTO `skills` (`name`, `proficiency`, `category`, `sort_order`) VALUES
            ('Web Design', 90, 'Design', 1),
            ('Web Development', 85, 'Development', 2),
            ('UI/UX Design', 88, 'Design', 3),
            ('PHP & MySQL', 92, 'Backend', 4)";
        
        if ($conn->query($insert_sql)) {
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Migration successful: skills table created with sample data.</div>';
            $success = true;
        } else {
            $message = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Table created but failed to insert sample data: ' . $conn->error . '</div>';
            $success = true;
        }
    } else {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Migration failed: ' . $conn->error . '</div>';
    }
} else {
    $message = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> Migration skipped: skills table already exists.</div>';
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
                        <h5 class="mb-0">Database Migration - Skills</h5>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        <?php if ($success): ?>
                            <p class="mt-3">The skills management feature has been successfully set up. You can now:</p>
                            <ul>
                                <li>Go to <strong>Skills</strong> in the admin dashboard to manage your skills</li>
                                <li>Add, edit, or delete skills with proficiency levels</li>
                                <li>Organize skills by category and display order</li>
                                <li>Assign custom or Font Awesome icons to each skill</li>
                            </ul>
                            <a href="<?php echo SITE_URL; ?>/admin/skills.php" class="btn btn-primary">Go to Manage Skills</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
