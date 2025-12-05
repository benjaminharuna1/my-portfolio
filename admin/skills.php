<?php
require '../config.php';
require '../includes/admin-list-helper.php';
require '../includes/icon-helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Manage Skills';
$message = '';
$form_data = [];
$form_errors = [];

// Display success message from redirect
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'updated') {
        $message = '<div class="alert alert-success">Skill updated successfully.</div>';
    } elseif ($_GET['success'] === 'created') {
        $message = '<div class="alert alert-success">Skill added successfully.</div>';
    }
}

// Delete skill
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM skills WHERE id = $id");
    $message = '<div class="alert alert-success">Skill deleted successfully.</div>';
    ErrorLogger::log("Skill deleted: ID $id", 'INFO');
}

// Add/Edit skill
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Preserve form data
    $form_data = [
        'id' => isset($_POST['id']) ? intval($_POST['id']) : '',
        'name' => $_POST['name'] ?? '',
        'proficiency' => intval($_POST['proficiency'] ?? 50),
        'category' => $_POST['category'] ?? '',
        'icon' => $_POST['icon'] ?? '',
        'sort_order' => intval($_POST['sort_order'] ?? 0)
    ];
    
    // Validate required fields
    if (empty($form_data['name'])) {
        $form_errors[] = 'Skill name is required.';
    }
    
    if ($form_data['proficiency'] < 0 || $form_data['proficiency'] > 100) {
        $form_errors[] = 'Proficiency must be between 0 and 100.';
    }
    
    if (empty($form_errors)) {
        $name = $conn->real_escape_string($form_data['name']);
        $proficiency = $form_data['proficiency'];
        $category = $conn->real_escape_string($form_data['category']);
        $icon = $conn->real_escape_string($form_data['icon']);
        $sort_order = $form_data['sort_order'];
        
        if ($form_data['id']) {
            $id = $form_data['id'];
            if ($conn->query("UPDATE skills SET name='$name', proficiency=$proficiency, category='$category', icon='$icon', sort_order=$sort_order WHERE id=$id")) {
                ErrorLogger::log("Skill updated: ID $id", 'INFO');
                header('Location: ' . SITE_URL . '/admin/skills.php?success=updated');
                exit;
            } else {
                $form_errors[] = 'Database error: ' . $conn->error;
            }
        } else {
            if ($conn->query("INSERT INTO skills (name, proficiency, category, icon, sort_order) VALUES ('$name', $proficiency, '$category', '$icon', $sort_order)")) {
                ErrorLogger::log("Skill created: $name", 'INFO');
                header('Location: ' . SITE_URL . '/admin/skills.php?success=created');
                exit;
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
        $edit_item = $conn->query("SELECT * FROM skills WHERE id = $id")->fetch_assoc();
    }
}

// Get all skills
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pagination = getPaginatedItems($conn, 'skills', $page, 10, 'sort_order ASC, id DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../includes/admin-head.php'; ?>
    <style>
        .proficiency-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .proficiency-high {
            background-color: #d4edda;
            color: #155724;
        }
        .proficiency-medium {
            background-color: #fff3cd;
            color: #856404;
        }
        .proficiency-low {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/admin-sidebar.php'; ?>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                    <h1>Manage Skills</h1>
                    <?php if (!$edit_item): ?>
                        <a href="?edit=new" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Skill
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

                <!-- Skills List Table -->
                <?php if (!$show_form): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>All Skills (<?php echo $pagination['total_count']; ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Proficiency</th>
                                        <th>Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pagination['items'] as $item): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                                        <td>
                                            <?php if (!empty($item['category'])): ?>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($item['category']); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $prof = $item['proficiency'];
                                            $class = 'proficiency-low';
                                            if ($prof >= 80) $class = 'proficiency-high';
                                            elseif ($prof >= 60) $class = 'proficiency-medium';
                                            ?>
                                            <span class="proficiency-badge <?php echo $class; ?>"><?php echo $prof; ?>%</span>
                                        </td>
                                        <td><?php echo $item['sort_order']; ?></td>
                                        <td>
                                            <a href="?edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this skill?')">
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
                <?php displayPagination($pagination['current_page'], $pagination['total_pages'], SITE_URL . '/admin/skills.php'); ?>
                <?php else: ?>

                <!-- Edit/Add Form -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><?php echo $edit_item && isset($edit_item['id']) ? 'Edit Skill' : 'Add New Skill'; ?></h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <?php if ($edit_item && isset($edit_item['id'])): ?>
                                        <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Skill Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $edit_item && !empty($edit_item['name']) ? htmlspecialchars($edit_item['name']) : ''; ?>" required>
                                        <small class="text-muted">e.g., Web Design, PHP & MySQL, React</small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="proficiency" class="form-label">Proficiency Level *</label>
                                            <div class="input-group">
                                                <input type="range" class="form-range" id="proficiency" name="proficiency" min="0" max="100" value="<?php echo $edit_item && !empty($edit_item['proficiency']) ? $edit_item['proficiency'] : '50'; ?>" oninput="updateProficiencyDisplay(this.value)">
                                                <span class="input-group-text" id="proficiencyDisplay"><?php echo $edit_item && !empty($edit_item['proficiency']) ? $edit_item['proficiency'] : '50'; ?>%</span>
                                            </div>
                                            <small class="text-muted">0-100%</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="category" class="form-label">Category</label>
                                            <input type="text" class="form-control" id="category" name="category" value="<?php echo $edit_item && !empty($edit_item['category']) ? htmlspecialchars($edit_item['category']) : ''; ?>" placeholder="e.g., Design, Backend, Frontend">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="icon" class="form-label">Skill Icon</label>
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
                                        <label for="sort_order" class="form-label">Display Order</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?php echo $edit_item && isset($edit_item['sort_order']) ? $edit_item['sort_order'] : '0'; ?>" min="0">
                                        <small class="text-muted">Lower numbers appear first</small>
                                    </div>

                                    <div class="btn-group" role="group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> <?php echo $edit_item && isset($edit_item['id']) ? 'Update' : 'Add'; ?> Skill
                                        </button>
                                        <a href="<?php echo SITE_URL; ?>/admin/skills.php" class="btn btn-secondary">
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

        function updateProficiencyDisplay(value) {
            document.getElementById('proficiencyDisplay').textContent = value + '%';
        }
    </script>
</body>
</html>
