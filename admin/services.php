<?php
require '../config.php';
require '../includes/admin-list-helper.php';
require '../includes/icon-helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Manage Services';
$message = '';
$form_data = [];
$form_errors = [];

// Delete service
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM services WHERE id = $id");
    $message = '<div class="alert alert-success">Service deleted successfully.</div>';
    ErrorLogger::log("Service deleted: ID $id", 'INFO');
}

// Add/Edit service
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Preserve form data
    $form_data = [
        'id' => isset($_POST['id']) ? intval($_POST['id']) : '',
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'icon' => $_POST['icon'] ?? '',
        'tech_icons' => $_POST['tech_icons'] ?? '',
        'status' => $_POST['status'] ?? 'published'
    ];
    
    // Validate required fields
    if (empty($form_data['title'])) {
        $form_errors[] = 'Service title is required.';
    }
    
    if (empty($form_data['description'])) {
        $form_errors[] = 'Service description is required.';
    }
    
    if (empty($form_errors)) {
        $title = $conn->real_escape_string($form_data['title']);
        $description = $conn->real_escape_string($form_data['description']);
        $icon = $conn->real_escape_string($form_data['icon']);
        $tech_icons = $conn->real_escape_string($form_data['tech_icons']);
        $status = $conn->real_escape_string($form_data['status']);
        
        if ($form_data['id']) {
            $id = $form_data['id'];
            if ($conn->query("UPDATE services SET title='$title', description='$description', icon='$icon', tech_icons='$tech_icons', status='$status' WHERE id=$id")) {
                $message = '<div class="alert alert-success">Service updated successfully.</div>';
                ErrorLogger::log("Service updated: ID $id", 'INFO');
                $form_data = [];
            } else {
                $form_errors[] = 'Database error: ' . $conn->error;
            }
        } else {
            if ($conn->query("INSERT INTO services (title, description, icon, tech_icons, status) VALUES ('$title', '$description', '$icon', '$tech_icons', '$status')")) {
                $message = '<div class="alert alert-success">Service added successfully.</div>';
                ErrorLogger::log("Service created: $title", 'INFO');
                $form_data = [];
            } else {
                $form_errors[] = 'Database error: ' . $conn->error;
            }
        }
    }
}

$edit_item = null;
$show_form = false;

// Show form if there are errors (preserve data) or if edit/new is requested
if (!empty($form_errors)) {
    $show_form = true;
    $edit_item = $form_data;
} elseif (isset($_GET['edit'])) {
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
                
                <?php if (!empty($form_errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Errors:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($form_errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

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
                                        <label for="icon" class="form-label">Service Icon</label>
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
    </script>
</body>
</html>
