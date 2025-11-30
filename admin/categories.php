<?php
require '../config.php';
require '../includes/admin-list-helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Manage Categories';
$message = '';

// Delete category
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM categories WHERE id = $id");
    $message = '<div class="alert alert-success">Category deleted successfully.</div>';
    ErrorLogger::log("Category deleted: ID $id", 'INFO');
}

// Add/Edit category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    $description = $conn->real_escape_string($_POST['description']);
    $color = $conn->real_escape_string($_POST['color']);
    $icon = $conn->real_escape_string($_POST['icon']);
    
    if (isset($_POST['id']) && $_POST['id']) {
        $id = intval($_POST['id']);
        $conn->query("UPDATE categories SET name='$name', slug='$slug', description='$description', color='$color', icon='$icon' WHERE id=$id");
        $message = '<div class="alert alert-success">Category updated successfully.</div>';
        ErrorLogger::log("Category updated: ID $id", 'INFO');
    } else {
        $conn->query("INSERT INTO categories (name, slug, description, color, icon) VALUES ('$name', '$slug', '$description', '$color', '$icon')");
        $message = '<div class="alert alert-success">Category added successfully.</div>';
        ErrorLogger::log("Category created: $name", 'INFO');
    }
}

$edit_item = null;
$show_form = false;
if (isset($_GET['edit'])) {
    $show_form = true;
    if ($_GET['edit'] !== 'new') {
        $id = intval($_GET['edit']);
        $edit_item = $conn->query("SELECT * FROM categories WHERE id = $id")->fetch_assoc();
    }
}

// Get all categories
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pagination = getPaginatedItems($conn, 'categories', $page, 10, 'id DESC');
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
        .color-preview {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 5px;
            border: 2px solid #ddd;
            vertical-align: middle;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/admin-sidebar.php'; ?>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                    <h1>Manage Categories</h1>
                    <?php if (!$edit_item): ?>
                        <a href="?edit=new" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Category
                        </a>
                    <?php endif; ?>
                </div>

                <?php echo $message; ?>

                <!-- Categories List Table -->
                <?php if (!$show_form): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>All Categories (<?php echo $pagination['total_count']; ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Color</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Icon</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pagination['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <span class="color-preview" style="background-color: <?php echo htmlspecialchars($item['color']); ?>;"></span>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                                        <td><code><?php echo htmlspecialchars($item['slug']); ?></code></td>
                                        <td>
                                            <?php if (!empty($item['icon'])): ?>
                                                <i class="fas <?php echo htmlspecialchars($item['icon']); ?>"></i>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(substr($item['description'], 0, 50)); ?><?php echo strlen($item['description']) > 50 ? '...' : ''; ?></td>
                                        <td>
                                            <a href="?edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php displayPagination($pagination['current_page'], $pagination['total_pages'], SITE_URL . '/admin/categories.php'); ?>
                <?php else: ?>

                <!-- Edit/Add Form -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><?php echo $edit_item && isset($edit_item['id']) ? 'Edit Category' : 'Add New Category'; ?></h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <?php if ($edit_item && isset($edit_item['id'])): ?>
                                        <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Category Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $edit_item && !empty($edit_item['name']) ? htmlspecialchars($edit_item['name']) : ''; ?>" required>
                                        <small class="text-muted">The slug will be auto-generated from the name</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo $edit_item && !empty($edit_item['description']) ? htmlspecialchars($edit_item['description']) : ''; ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="color" class="form-label">Category Color</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" id="color" name="color" value="<?php echo $edit_item && !empty($edit_item['color']) ? htmlspecialchars($edit_item['color']) : '#667eea'; ?>" style="max-width: 60px;">
                                                <input type="text" class="form-control" id="colorHex" value="<?php echo $edit_item && !empty($edit_item['color']) ? htmlspecialchars($edit_item['color']) : '#667eea'; ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="icon" class="form-label">Icon (Font Awesome)</label>
                                            <input type="text" class="form-control" id="icon" name="icon" value="<?php echo $edit_item && !empty($edit_item['icon']) ? htmlspecialchars($edit_item['icon']) : ''; ?>" placeholder="fa-folder">
                                            <small class="text-muted">e.g., fa-palette, fa-code, fa-mobile-alt</small>
                                        </div>
                                    </div>

                                    <div class="btn-group" role="group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> <?php echo $edit_item && isset($edit_item['id']) ? 'Update' : 'Add'; ?> Category
                                        </button>
                                        <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="btn btn-secondary">
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
        // Update hex color display when color picker changes
        document.getElementById('color').addEventListener('change', function() {
            document.getElementById('colorHex').value = this.value;
        });
    </script>
</body>
</html>
