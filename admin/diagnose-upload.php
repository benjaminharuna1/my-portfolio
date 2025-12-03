<?php
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../includes/admin-head.php'; ?>
</head>
<body>
    <div class="container mt-5">
        <h1>Upload System Diagnostics</h1>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5>Configuration</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td><strong>SITE_URL:</strong></td>
                        <td><code><?php echo SITE_URL; ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>__DIR__:</strong></td>
                        <td><code><?php echo __DIR__; ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>dirname(__DIR__):</strong></td>
                        <td><code><?php echo dirname(__DIR__); ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>Uploads folder path:</strong></td>
                        <td><code><?php echo dirname(__DIR__) . '/uploads'; ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>Expected image URL:</strong></td>
                        <td><code><?php echo SITE_URL . '/uploads/img_example.jpg'; ?></code></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Directory Check</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td><strong>Uploads directory exists:</strong></td>
                        <td>
                            <?php 
                            $uploads_dir = dirname(__DIR__) . '/uploads';
                            if (is_dir($uploads_dir)) {
                                echo '<span class="badge bg-success">YES</span>';
                            } else {
                                echo '<span class="badge bg-danger">NO</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Uploads directory writable:</strong></td>
                        <td>
                            <?php 
                            if (is_writable($uploads_dir)) {
                                echo '<span class="badge bg-success">YES</span>';
                            } else {
                                echo '<span class="badge bg-danger">NO</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Files in uploads:</strong></td>
                        <td>
                            <?php 
                            if (is_dir($uploads_dir)) {
                                $files = array_diff(scandir($uploads_dir), ['.', '..']);
                                echo count($files) . ' files';
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Sample Images in Database</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Featured Image URL</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT id, title, featured_image_url FROM portfolio_items LIMIT 5");
                        while ($row = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><code style="font-size: 0.8rem;"><?php echo htmlspecialchars($row['featured_image_url']); ?></code></td>
                                <td>
                                    <?php
                                    if (!empty($row['featured_image_url'])) {
                                        $filename = basename($row['featured_image_url']);
                                        $file_path = dirname(__DIR__) . '/uploads/' . $filename;
                                        if (file_exists($file_path)) {
                                            echo '<span class="badge bg-success">File exists</span>';
                                        } else {
                                            echo '<span class="badge bg-danger">File missing</span>';
                                        }
                                    } else {
                                        echo '<span class="badge bg-warning">No URL</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Test Image Display</h5>
            </div>
            <div class="card-body">
                <?php
                $result = $conn->query("SELECT featured_image_url FROM portfolio_items WHERE featured_image_url IS NOT NULL AND featured_image_url != '' LIMIT 1");
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $url = $row['featured_image_url'];
                    ?>
                    <p><strong>Testing image URL:</strong></p>
                    <code><?php echo htmlspecialchars($url); ?></code>
                    <div class="mt-3">
                        <img src="<?php echo htmlspecialchars($url); ?>" alt="Test" style="max-width: 300px; border: 2px solid #ccc;">
                    </div>
                    <?php
                } else {
                    echo '<p class="text-muted">No portfolio items with images found.</p>';
                }
                ?>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Recommendations</h5>
            </div>
            <div class="card-body">
                <p><strong>If images are not showing:</strong></p>
                <ol>
                    <li>Verify that SITE_URL in .env matches your actual site URL</li>
                    <li>Check that the uploads directory exists and is writable</li>
                    <li>Verify image files exist in the uploads folder</li>
                    <li>Check browser console for 404 errors on image requests</li>
                    <li>Ensure .htaccess allows access to the uploads folder</li>
                </ol>
            </div>
        </div>

        <div class="mt-4 mb-4">
            <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
