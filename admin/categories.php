<?php
require '../config.php';
require '../includes/admin-list-helper.php';
require '../includes/icon-helper.php';

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
                                            <label for="icon" class="form-label">Category Icon</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="icon" name="icon" value="<?php echo $edit_item && !empty($edit_item['icon']) ? htmlspecialchars($edit_item['icon']) : ''; ?>" placeholder="e.g., photoshop or fa-palette">
                                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#iconPickerModal">
                                                    <i class="fas fa-search"></i> Browse Icons
                                                </button>
                                            </div>
                                            <small class="text-muted">Enter custom icon name (e.g., photoshop) or Font Awesome class (e.g., fa-palette)</small>
                                            <?php if ($edit_item && !empty($edit_item['icon'])): ?>
                                            <div class="mt-2">
                                                <strong>Preview:</strong>
                                                <div style="font-size: 2rem;">
                                                    <?php echo displayIcon($edit_item['icon'], ['size' => 48], 'fa-question'); ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>
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

    <!-- Icon Picker Modal -->
    <div class="modal fade" id="iconPickerModal" tabindex="-1" aria-labelledby="iconPickerLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="iconPickerLabel">Select Icon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="custom-icons-tab" data-bs-toggle="tab" data-bs-target="#custom-icons" type="button" role="tab">Custom Icons</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="fontawesome-icons-tab" data-bs-toggle="tab" data-bs-target="#fontawesome-icons" type="button" role="tab">Font Awesome</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Custom Icons Tab -->
                        <div class="tab-pane fade show active" id="custom-icons" role="tabpanel">
                            <div class="row" id="customIconsGrid">
                                <?php
                                $custom_icons = getAllCustomIcons();
                                if (empty($custom_icons)):
                                ?>
                                <div class="col-12">
                                    <p class="text-muted">No custom icons available. <a href="<?php echo SITE_URL; ?>/admin/icons.php">Add custom icons</a></p>
                                </div>
                                <?php else: ?>
                                <?php foreach ($custom_icons as $icon): ?>
                                <div class="col-md-4 mb-3">
                                    <button type="button" class="btn btn-outline-primary w-100 icon-select-btn" data-icon-name="ci-<?php echo htmlspecialchars($icon['name']); ?>">
                                        <div style="font-size: 2rem; margin-bottom: 5px;">
                                            <?php echo $icon['svg_content']; ?>
                                        </div>
                                        <small>ci-<?php echo htmlspecialchars($icon['name']); ?></small>
                                    </button>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Font Awesome Tab -->
                        <div class="tab-pane fade" id="fontawesome-icons" role="tabpanel">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="faSearchInput" placeholder="Search Font Awesome icons...">
                            </div>
                            <div class="row" id="fontawesomeIconsGrid">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle custom icon selection
        document.querySelectorAll('.icon-select-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const iconName = this.getAttribute('data-icon-name');
                document.getElementById('icon').value = iconName;
                
                // Update preview
                location.reload();
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('iconPickerModal'));
                if (modal) modal.hide();
            });
        });

        // Common Font Awesome icons
        const commonFontAwesomeIcons = [
            'fa-palette', 'fa-code', 'fa-pencil-ruler', 'fa-mobile-alt', 'fa-laptop',
            'fa-database', 'fa-server', 'fa-cloud', 'fa-shield-alt', 'fa-lock',
            'fa-key', 'fa-cog', 'fa-tools', 'fa-wrench', 'fa-hammer',
            'fa-paint-brush', 'fa-pen', 'fa-edit', 'fa-copy', 'fa-paste',
            'fa-cut', 'fa-undo', 'fa-redo', 'fa-save', 'fa-download',
            'fa-upload', 'fa-share', 'fa-link', 'fa-chain', 'fa-globe',
            'fa-map', 'fa-location', 'fa-phone', 'fa-envelope', 'fa-comment',
            'fa-star', 'fa-heart', 'fa-thumbs-up', 'fa-check', 'fa-times',
            'fa-plus', 'fa-minus', 'fa-search', 'fa-filter', 'fa-sort'
        ];

        // Populate Font Awesome icons
        function populateFontAwesomeIcons(filter = '') {
            const grid = document.getElementById('fontawesomeIconsGrid');
            grid.innerHTML = '';
            
            const filtered = commonFontAwesomeIcons.filter(icon => 
                icon.toLowerCase().includes(filter.toLowerCase())
            );
            
            filtered.forEach(icon => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline-secondary w-100 icon-select-btn';
                btn.setAttribute('data-icon-name', icon);
                btn.innerHTML = `
                    <div style="font-size: 2rem; margin-bottom: 5px;">
                        <i class="fas ${icon}"></i>
                    </div>
                    <small>${icon}</small>
                `;
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.getElementById('icon').value = icon;
                    location.reload();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('iconPickerModal'));
                    if (modal) modal.hide();
                });
                
                const col = document.createElement('div');
                col.className = 'col-md-4 mb-3';
                col.appendChild(btn);
                grid.appendChild(col);
            });
        }

        // Initialize Font Awesome icons on modal show
        document.getElementById('iconPickerModal').addEventListener('show.bs.modal', function() {
            populateFontAwesomeIcons();
        });

        // Search Font Awesome icons
        document.getElementById('faSearchInput').addEventListener('input', function() {
            populateFontAwesomeIcons(this.value);
        });

        // Update hex color display when color picker changes
        document.getElementById('color').addEventListener('change', function() {
            document.getElementById('colorHex').value = this.value;
        });
    </script>
</body>
</html>
