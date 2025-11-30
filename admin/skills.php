<?php
require '../config.php';
require '../includes/admin-list-helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Manage Skills';
$message = '';

// Delete skill
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM skills WHERE id = $id");
    $message = '<div class="alert alert-success">Skill deleted successfully.</div>';
    ErrorLogger::log("Skill deleted: ID $id", 'INFO');
}

// Add/Edit skill
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $proficiency = intval($_POST['proficiency']);
    $category = $conn->real_escape_string($_POST['category']);
    $sort_order = isset($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
    
    // Validate proficiency
    if ($proficiency < 0 || $proficiency > 100) {
        $proficiency = 50;
    }
    
    if (isset($_POST['id']) && $_POST['id']) {
        $id = intval($_POST['id']);
        $conn->query("UPDATE skills SET name='$name', proficiency=$proficiency, category='$category', sort_order=$sort_order WHERE id=$id");
        $message = '<div class="alert alert-success">Skill updated successfully.</div>';
        ErrorLogger::log("Skill updated: ID $id", 'INFO');
    } else {
        $conn->query("INSERT INTO skills (name, proficiency, category, sort_order) VALUES ('$name', $proficiency, '$category', $sort_order)");
        $message = '<div class="alert alert-success">Skill added successfully.</div>';
        ErrorLogger::log("Skill created: $name", 'INFO');
    }
}

$edit_item = null;
$show_form = false;
if (isset($_GET['edit'])) {
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateProficiencyDisplay(value) {
            document.getElementById('proficiencyDisplay').textContent = value + '%';
        }
    </script>
</body>
</html>
