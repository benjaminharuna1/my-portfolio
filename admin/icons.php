<?php
require '../config.php';
require '../includes/upload.php';
require '../includes/admin-list-helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Manage Icons';
$message = '';

// Create icons table if it doesn't exist
$conn->query("CREATE TABLE IF NOT EXISTS `custom_icons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL UNIQUE,
  `slug` varchar(100) NOT NULL UNIQUE,
  `svg_content` longtext NOT NULL,
  `svg_filename` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `color` varchar(7) DEFAULT '#000000',
  `size` varchar(20) DEFAULT '24',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");

// Delete icon
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $icon = $conn->query("SELECT svg_filename FROM custom_icons WHERE id = $id")->fetch_assoc();
    if ($icon && !empty($icon['svg_filename'])) {
        $file_path = '../uploads/' . $icon['svg_filename'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    $conn->query("DELETE FROM custom_icons WHERE id = $id");
    $message = '<div class="alert alert-success">Icon deleted successfully.</div>';
    ErrorLogger::log("Icon deleted: ID $id", 'INFO');
}

// Add/Edit icon
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $name = $conn->real_escape_string($_POST['name']);
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', trim($_POST['name'])));
    $category = $conn->real_escape_string($_POST['category']);
    $color = $conn->real_escape_string($_POST['color']);
    $size = $conn->real_escape_string($_POST['size']);
    $svg_content = '';
    $svg_filename = '';

    // Handle SVG file upload
    if (isset($_FILES['svg_file']) && $_FILES['svg_file']['error'] === 0) {
        $file = $_FILES['svg_file'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validate SVG file - check extension and MIME type
        if ($file_ext !== 'svg') {
            $message = '<div class="alert alert-danger">Please upload a valid SVG file (.svg extension required).</div>';
        } else {
            // Read SVG content
            $svg_content = file_get_contents($file['tmp_name']);
            
            if ($svg_content === false) {
                $message = '<div class="alert alert-danger">Failed to read SVG file.</div>';
            } else {
                // Ensure UTF-8 encoding
                if (!mb_check_encoding($svg_content, 'UTF-8')) {
                    $svg_content = mb_convert_encoding($svg_content, 'UTF-8');
                }
                
                // Sanitize SVG (remove script tags and dangerous attributes)
                $svg_content = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi', '', $svg_content);
                $svg_content = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $svg_content);
                
                // Escape for database storage
                $svg_content_escaped = $conn->real_escape_string($svg_content);
                
                // Generate filename
                $svg_filename = 'icon_' . uniqid() . '.svg';
                $upload_path = '../uploads/' . $svg_filename;
                
                // Ensure uploads directory exists
                if (!is_dir('../uploads')) {
                    mkdir('../uploads', 0755, true);
                }
                
                // Delete old file if editing
                if (isset($_POST['id']) && $_POST['id']) {
                    $old_icon = $conn->query("SELECT svg_filename FROM custom_icons WHERE id = " . intval($_POST['id']))->fetch_assoc();
                    if ($old_icon && !empty($old_icon['svg_filename'])) {
                        $old_path = '../uploads/' . $old_icon['svg_filename'];
                        if (file_exists($old_path)) {
                            unlink($old_path);
                        }
                    }
                }
                
                // Save SVG file
                if (file_put_contents($upload_path, $svg_content) !== false) {
                    if (isset($_POST['id']) && $_POST['id']) {
                        $id = intval($_POST['id']);
                        if ($conn->query("UPDATE custom_icons SET name='$name', slug='$slug', category='$category', color='$color', size='$size', svg_content='$svg_content_escaped', svg_filename='$svg_filename' WHERE id=$id")) {
                            $message = '<div class="alert alert-success">Icon updated successfully.</div>';
                            ErrorLogger::log("Icon updated: ID $id", 'INFO');
                        } else {
                            $message = '<div class="alert alert-danger">Failed to update icon in database: ' . $conn->error . '</div>';
                            ErrorLogger::log("Icon update failed: " . $conn->error, 'ERROR');
                        }
                    } else {
                        if ($conn->query("INSERT INTO custom_icons (name, slug, category, color, size, svg_content, svg_filename) VALUES ('$name', '$slug', '$category', '$color', '$size', '$svg_content_escaped', '$svg_filename')")) {
                            $message = '<div class="alert alert-success">Icon added successfully.</div>';
                            ErrorLogger::log("Icon created: $name", 'INFO');
                        } else {
                            $message = '<div class="alert alert-danger">Failed to save icon to database: ' . $conn->error . '</div>';
                            ErrorLogger::log("Icon creation failed: " . $conn->error, 'ERROR');
                        }
                    }
                } else {
                    $message = '<div class="alert alert-danger">Failed to save SVG file to disk. Check directory permissions.</div>';
                    ErrorLogger::log("Failed to save SVG file: $upload_path", 'ERROR');
                }
            }
        }
    } elseif (isset($_POST['id']) && $_POST['id']) {
        // Update without changing SVG file
        $id = intval($_POST['id']);
        $conn->query("UPDATE custom_icons SET name='$name', slug='$slug', category='$category', color='$color', size='$size' WHERE id=$id");
        $message = '<div class="alert alert-success">Icon updated successfully.</div>';
        ErrorLogger::log("Icon updated: ID $id", 'INFO');
    } else {
        $message = '<div class="alert alert-danger">Please upload an SVG file.</div>';
    }
}

$edit_item = null;
$show_form = false;
if (isset($_GET['edit'])) {
    $show_form = true;
    if ($_GET['edit'] !== 'new') {
        $id = intval($_GET['edit']);
        $edit_item = $conn->query("SELECT * FROM custom_icons WHERE id = $id")->fetch_assoc();
    }
}

// Get all icons
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pagination = getPaginatedItems($conn, 'custom_icons', $page, 12, 'id DESC');
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
    <style>
        .icon-preview { width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; }
        .icon-preview svg { width: 100%; height: 100%; }
        .icon-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-top: 20px; }
        .icon-card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; transition: all 0.3s ease; }
        .icon-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); transform: translateY(-2px); }
        .icon-card-preview { width: 60px; height: 60px; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; }
        .icon-card-preview svg { width: 100%; height: 100%; }
        .icon-card-name { font-weight: 600; font-size: 0.9rem; margin-bottom: 5px; word-break: break-word; }
        .icon-card-category { font-size: 0.8rem; color: #666; margin-bottom: 10px; }
        .icon-card-actions { display: flex; gap: 5px; justify-content: center; }
        .icon-card-actions a, .icon-card-actions button { padding: 4px 8px; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/admin-sidebar.php'; ?>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                    <h1>Manage Custom Icons</h1>
                    <?php if (!$show_form): ?>
                        <a href="?edit=new" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Icon
                        </a>
                    <?php endif; ?>
                </div>

                <?php echo $message; ?>

                <!-- Icons Grid View -->
                <?php if (!$show_form): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>All Custom Icons (<?php echo $pagination['total_count']; ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pagination['items'])): ?>
                            <div class="alert alert-info">No icons found. <a href="?edit=new">Add your first icon</a></div>
                        <?php else: ?>
                            <div class="icon-grid">
                                <?php foreach ($pagination['items'] as $icon): ?>
                                <div class="icon-card">
                                    <div class="icon-card-preview">
                                        <?php if (!empty($icon['svg_content'])): ?>
                                            <?php echo $icon['svg_content']; ?>
                                        <?php else: ?>
                                            <i class="fas fa-image"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="icon-card-name"><?php echo htmlspecialchars($icon['name']); ?></div>
                                    <?php if (!empty($icon['category'])): ?>
                                        <div class="icon-card-category"><?php echo htmlspecialchars($icon['category']); ?></div>
                                    <?php endif; ?>
                                    <div class="icon-card-actions">
                                        <a href="?edit=<?php echo $icon['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $icon['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this icon?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php displayPagination($pagination['current_page'], $pagination['total_pages'], SITE_URL . '/admin/icons.php'); ?>
                <?php else: ?>

                <!-- Add/Edit Form -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><?php echo $edit_item ? 'Edit Icon' : 'Add New Icon'; ?></h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="save">
                                    <?php if ($edit_item): ?>
                                        <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label for="name" class="form-label">Icon Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $edit_item && !empty($edit_item['name']) ? htmlspecialchars($edit_item['name']) : ''; ?>" placeholder="e.g., Download, Upload, Settings" required>
                                        <small class="text-muted">A descriptive name for your icon</small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="category" class="form-label">Category</label>
                                            <input type="text" class="form-control" id="category" name="category" value="<?php echo $edit_item && !empty($edit_item['category']) ? htmlspecialchars($edit_item['category']) : ''; ?>" placeholder="e.g., Social, UI, Business">
                                            <small class="text-muted">Optional category for organization</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="size" class="form-label">Default Size (px)</label>
                                            <input type="number" class="form-control" id="size" name="size" value="<?php echo $edit_item && !empty($edit_item['size']) ? htmlspecialchars($edit_item['size']) : '24'; ?>" min="16" max="256">
                                            <small class="text-muted">Default display size</small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="color" class="form-label">Default Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="color" name="color" value="<?php echo $edit_item && !empty($edit_item['color']) ? htmlspecialchars($edit_item['color']) : '#000000'; ?>" style="max-width: 60px;">
                                            <input type="text" class="form-control" id="colorHex" value="<?php echo $edit_item && !empty($edit_item['color']) ? htmlspecialchars($edit_item['color']) : '#000000'; ?>" readonly>
                                        </div>
                                        <small class="text-muted">Default color for the icon (can be overridden in usage)</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="svg_file" class="form-label">SVG File <?php echo !$edit_item ? '<span class="text-danger">*</span>' : ''; ?></label>
                                        <input type="file" class="form-control" id="svg_file" name="svg_file" accept=".svg" <?php echo !$edit_item ? 'required' : ''; ?>>
                                        <small class="text-muted">Upload an SVG file. Leave empty to keep existing SVG.</small>
                                    </div>

                                    <?php if ($edit_item && !empty($edit_item['svg_content'])): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Current Icon Preview</label>
                                        <div class="icon-preview">
                                            <?php echo $edit_item['svg_content']; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <div class="btn-group" role="group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> <?php echo ($edit_item && isset($edit_item['id'])) ? 'Update' : 'Add'; ?> Icon
                                        </button>
                                        <a href="<?php echo SITE_URL; ?>/admin/icons.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sync color picker with hex input
        const colorPicker = document.getElementById('color');
        const colorHex = document.getElementById('colorHex');
        
        if (colorPicker && colorHex) {
            colorPicker.addEventListener('change', function() {
                colorHex.value = this.value;
            });
        }
    </script>
</body>
</html>
