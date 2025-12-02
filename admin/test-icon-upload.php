<?php
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$debug_info = [];

// Check uploads directory
$uploads_dir = '../uploads';
$debug_info['uploads_dir_exists'] = is_dir($uploads_dir);
$debug_info['uploads_dir_writable'] = is_writable($uploads_dir);
$debug_info['uploads_dir_path'] = realpath($uploads_dir);

// Check custom_icons table
$table_check = $conn->query("SHOW TABLES LIKE 'custom_icons'");
$debug_info['custom_icons_table_exists'] = $table_check && $table_check->num_rows > 0;

// Check table structure
if ($debug_info['custom_icons_table_exists']) {
    $columns = $conn->query("DESCRIBE custom_icons");
    $debug_info['table_columns'] = [];
    while ($col = $columns->fetch_assoc()) {
        $debug_info['table_columns'][] = $col['Field'] . ' (' . $col['Type'] . ')';
    }
}

// Test file upload
$test_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_svg'])) {
    $file = $_FILES['test_svg'];
    $debug_info['file_upload'] = [
        'name' => $file['name'],
        'type' => $file['type'],
        'size' => $file['size'],
        'error' => $file['error'],
        'tmp_name' => $file['tmp_name']
    ];
    
    if ($file['error'] === 0) {
        $svg_content = file_get_contents($file['tmp_name']);
        $debug_info['file_read_success'] = $svg_content !== false;
        $debug_info['file_size'] = strlen($svg_content);
        $debug_info['file_encoding'] = mb_detect_encoding($svg_content);
        
        // Try to sanitize
        $sanitized = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi', '', $svg_content);
        $sanitized = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $sanitized);
        $debug_info['sanitization_success'] = true;
        $debug_info['sanitized_size'] = strlen($sanitized);
        
        // Try to escape for database
        $escaped = $conn->real_escape_string($sanitized);
        $debug_info['escape_success'] = true;
        $debug_info['escaped_size'] = strlen($escaped);
        
        // Try to insert test record
        $test_name = 'test_icon_' . time();
        $test_slug = 'test-icon-' . time();
        $query = "INSERT INTO custom_icons (name, slug, svg_content, category, color, size) VALUES ('$test_name', '$test_slug', '$escaped', 'Test', '#000000', '24')";
        
        if ($conn->query($query)) {
            $debug_info['database_insert_success'] = true;
            $debug_info['inserted_id'] = $conn->insert_id;
            $test_message = '<div class="alert alert-success">Test icon inserted successfully! ID: ' . $conn->insert_id . '</div>';
            
            // Try to retrieve it
            $retrieve = $conn->query("SELECT * FROM custom_icons WHERE id = " . $conn->insert_id);
            if ($retrieve && $retrieve->num_rows > 0) {
                $retrieved = $retrieve->fetch_assoc();
                $debug_info['retrieval_success'] = true;
                $debug_info['retrieved_svg_length'] = strlen($retrieved['svg_content']);
            }
        } else {
            $debug_info['database_insert_success'] = false;
            $debug_info['database_error'] = $conn->error;
            $test_message = '<div class="alert alert-danger">Database insert failed: ' . $conn->error . '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Icon Upload Test - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <h1>Icon Upload Debug Test</h1>
                
                <?php echo $test_message; ?>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>System Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Uploads Directory Exists:</strong></td>
                                <td><?php echo $debug_info['uploads_dir_exists'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Uploads Directory Writable:</strong></td>
                                <td><?php echo $debug_info['uploads_dir_writable'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Uploads Directory Path:</strong></td>
                                <td><code><?php echo $debug_info['uploads_dir_path']; ?></code></td>
                            </tr>
                            <tr>
                                <td><strong>Custom Icons Table Exists:</strong></td>
                                <td><?php echo $debug_info['custom_icons_table_exists'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></td>
                            </tr>
                            <?php if ($debug_info['custom_icons_table_exists']): ?>
                            <tr>
                                <td colspan="2"><strong>Table Columns:</strong></td>
                            </tr>
                            <?php foreach ($debug_info['table_columns'] as $col): ?>
                            <tr>
                                <td></td>
                                <td><code><?php echo $col; ?></code></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>Test SVG Upload</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="test_svg" class="form-label">Select SVG File</label>
                                <input type="file" class="form-control" id="test_svg" name="test_svg" accept=".svg" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Test Upload</button>
                        </form>
                    </div>
                </div>

                <?php if (!empty($_POST)): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Debug Information</h5>
                    </div>
                    <div class="card-body">
                        <pre><?php echo json_encode($debug_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?></pre>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="<?php echo SITE_URL; ?>/admin/icons.php" class="btn btn-secondary">Back to Icons</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
