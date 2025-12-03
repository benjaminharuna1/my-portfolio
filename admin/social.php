<?php
require '../config.php';
require '../includes/icon-helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Manage Social Links';
$message = '';
$form_data = [];
$form_errors = [];

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM social_links WHERE id = $id");
    $message = '<div class="alert alert-success">Social link deleted.</div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Preserve form data
    $form_data = [
        'id' => isset($_POST['id']) ? intval($_POST['id']) : '',
        'platform' => $_POST['platform'] ?? '',
        'url' => $_POST['url'] ?? '',
        'icon' => $_POST['icon'] ?? ''
    ];
    
    // Validate required fields
    if (empty($form_data['platform'])) {
        $form_errors[] = 'Platform name is required.';
    }
    
    if (empty($form_data['url'])) {
        $form_errors[] = 'URL is required.';
    } elseif (!filter_var($form_data['url'], FILTER_VALIDATE_URL)) {
        $form_errors[] = 'Invalid URL format.';
    }
    
    if (empty($form_errors)) {
        $platform = $conn->real_escape_string($form_data['platform']);
        $url = $conn->real_escape_string($form_data['url']);
        $icon = $conn->real_escape_string($form_data['icon']);
        
        if ($form_data['id']) {
            $id = $form_data['id'];
            if ($conn->query("UPDATE social_links SET platform='$platform', url='$url', icon='$icon' WHERE id=$id")) {
                $message = '<div class="alert alert-success">Social link updated.</div>';
                $form_data = [];
            } else {
                $form_errors[] = 'Database error: ' . $conn->error;
            }
        } else {
            if ($conn->query("INSERT INTO social_links (platform, url, icon) VALUES ('$platform', '$url', '$icon')")) {
                $message = '<div class="alert alert-success">Social link added.</div>';
                $form_data = [];
            } else {
                $form_errors[] = 'Database error: ' . $conn->error;
            }
        }
    }
}

$edit_item = null;
if (!empty($form_errors)) {
    $edit_item = $form_data;
} elseif (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_item = $conn->query("SELECT * FROM social_links WHERE id = $id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../includes/admin-head.php'; ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/admin-sidebar.php'; ?>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                    <h1>Manage Social Links</h1>
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

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><?php echo $edit_item ? 'Edit Social Link' : 'Add New Social Link'; ?></h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <?php if ($edit_item): ?>
                                        <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                                    <?php endif; ?>
                                    <div class="mb-3">
                                        <label for="platform" class="form-label">Platform</label>
                                        <input type="text" class="form-control" id="platform" name="platform" value="<?php echo $edit_item ? htmlspecialchars($edit_item['platform']) : ''; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="url" class="form-label">URL</label>
                                        <input type="url" class="form-control" id="url" name="url" value="<?php echo $edit_item ? htmlspecialchars($edit_item['url']) : ''; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="icon" class="form-label">Social Icon</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="icon" name="icon" value="<?php echo $edit_item ? htmlspecialchars($edit_item['icon']) : ''; ?>" placeholder="e.g., fab fa-linkedin-in">
                                            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#iconPickerModal">
                                                <i class="fas fa-search"></i> Browse Icons
                                            </button>
                                        </div>
                                        <small class="text-muted">Enter custom icon name (e.g., photoshop) or Font Awesome class (e.g., fab fa-linkedin-in)</small>
                                        <?php if ($edit_item && !empty($edit_item['icon'])): ?>
                                        <div class="mt-2">
                                            <strong>Preview:</strong>
                                            <div style="font-size: 2rem;">
                                                <?php echo displayIcon($edit_item['icon'], ['size' => 48], 'fa-question'); ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><?php echo $edit_item ? 'Update' : 'Add'; ?></button>
                                    <?php if ($edit_item): ?>
                                        <a href="<?php echo SITE_URL; ?>/admin/social.php" class="btn btn-secondary">Cancel</a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Social Links</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Platform</th>
                                            <th>Icon</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result = $conn->query("SELECT * FROM social_links");
                                        while ($item = $result->fetch_assoc()):
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['platform']); ?></td>
                                                <td><i class="<?php echo htmlspecialchars($item['icon']); ?>"></i></td>
                                                <td>
                                                    <a href="?edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                    <a href="?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
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

        // Common Font Awesome icons for social media
        const commonFontAwesomeIcons = [
            'fab fa-facebook-f', 'fab fa-twitter', 'fab fa-linkedin-in', 'fab fa-instagram',
            'fab fa-github', 'fab fa-youtube', 'fab fa-pinterest-p', 'fab fa-dribbble',
            'fab fa-behance', 'fab fa-codepen', 'fab fa-stack-overflow', 'fab fa-reddit',
            'fab fa-telegram', 'fab fa-whatsapp', 'fab fa-slack', 'fab fa-discord',
            'fab fa-twitch', 'fab fa-tiktok', 'fab fa-snapchat', 'fab fa-medium',
            'fab fa-dev', 'fab fa-hashnode', 'fab fa-mastodon', 'fab fa-bluesky',
            'fas fa-envelope', 'fas fa-phone', 'fas fa-globe', 'fas fa-link'
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
                        <i class="${icon}"></i>
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
