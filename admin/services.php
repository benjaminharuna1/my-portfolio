<?php
require '../config.php';
require '../includes/admin-list-helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Manage Services';
$message = '';

// Delete service
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM services WHERE id = $id");
    $message = '<div class="alert alert-success">Service deleted successfully.</div>';
    ErrorLogger::log("Service deleted: ID $id", 'INFO');
}

// Add/Edit service
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $icon = $conn->real_escape_string($_POST['icon']);
    $tech_icons = $conn->real_escape_string($_POST['tech_icons']);
    $status = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : 'published';
    
    if (isset($_POST['id']) && $_POST['id']) {
        $id = intval($_POST['id']);
        $conn->query("UPDATE services SET title='$title', description='$description', icon='$icon', tech_icons='$tech_icons', status='$status' WHERE id=$id");
        $message = '<div class="alert alert-success">Service updated successfully.</div>';
        ErrorLogger::log("Service updated: ID $id", 'INFO');
    } else {
        $conn->query("INSERT INTO services (title, description, icon, tech_icons, status) VALUES ('$title', '$description', '$icon', '$tech_icons', '$status')");
        $message = '<div class="alert alert-success">Service added successfully.</div>';
        ErrorLogger::log("Service created: $title", 'INFO');
    }
}

$edit_item = null;
$show_form = false;
if (isset($_GET['edit'])) {
    $show_form = true;
    if ($_GET['edit'] !== 'new') {
        $id = intval($_GET['edit']);
        $edit_item = $conn->query("SELECT * FROM services WHERE id = $id")->fetch_assoc();
    }
    // If edit=new, $edit_item stays null and form shows as "Add New"
}

// Get all services
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pagination = getPaginatedItems($conn, 'services', $page, 10, 'id DESC');
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
            <?php include '../includes/admin-sidebar.php'; ?>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                    <h1>Manage Services</h1>
                    <?php if (!$edit_item): ?>
                        <a href="?edit=new" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Service
                        </a>
                    <?php endif; ?>
                </div>

                <?php echo $message; ?>

                <!-- Services List Table -->
                <?php if (!$show_form): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>All Services (<?php echo $pagination['total_count']; ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $columns = [
                            'title' => 'Title',
                            'description' => 'Description',
                            'icon' => 'Icon'
                        ];
                        displayAdminTable(
                            $pagination['items'],
                            $columns,
                            SITE_URL . '/admin/services.php?edit=%d',
                            SITE_URL . '/admin/services.php?delete=%d'
                        );
                        ?>
                    </div>
                </div>
                <?php displayPagination($pagination['current_page'], $pagination['total_pages'], SITE_URL . '/admin/services.php'); ?>
                <?php else: ?>

                <!-- Edit/Add Form -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><?php echo $edit_item && isset($edit_item['id']) ? 'Edit Service' : 'Add New Service'; ?></h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <?php if ($edit_item && isset($edit_item['id'])): ?>
                                        <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title *</label>
                                        <input type="text" class="form-control" id="title" name="title" value="<?php echo $edit_item && !empty($edit_item['title']) ? htmlspecialchars($edit_item['title']) : ''; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description *</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $edit_item && !empty($edit_item['description']) ? htmlspecialchars($edit_item['description']) : ''; ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="icon" class="form-label">Service Icon (Font Awesome)</label>
                                        <input type="text" class="form-control" id="icon" name="icon" value="<?php echo $edit_item && !empty($edit_item['icon']) ? htmlspecialchars($edit_item['icon']) : ''; ?>" placeholder="fa-palette">
                                        <small class="text-muted">e.g., fa-palette, fa-code, fa-pencil-ruler</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tech_icons" class="form-label">Technology Icons (Comma Separated)</label>
                                        <textarea class="form-control" id="tech_icons" name="tech_icons" rows="2" placeholder="fab fa-php, fab fa-js, fab fa-react, fab fa-laravel"><?php echo $edit_item && !empty($edit_item['tech_icons']) ? htmlspecialchars($edit_item['tech_icons']) : ''; ?></textarea>
                                        <small class="text-muted">Enter Font Awesome icon classes separated by commas.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="published" <?php echo (!$edit_item || $edit_item['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                                            <option value="draft" <?php echo ($edit_item && $edit_item['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                        </select>
                                    </div>

                                    <div class="btn-group" role="group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> <?php echo $edit_item && isset($edit_item['id']) ? 'Update' : 'Add'; ?> Service
                                        </button>
                                        <a href="<?php echo SITE_URL; ?>/admin/services.php" class="btn btn-secondary">
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
</body>
</html>
