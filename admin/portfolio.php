<?php
require '../config.php';
require '../includes/upload.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Manage Portfolio';
$message = '';

// Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $item = $conn->query("SELECT image_filename FROM portfolio_items WHERE id = $id")->fetch_assoc();
    if ($item && $item['image_filename']) {
        deleteImage($item['image_filename'], '../uploads');
    }
    $conn->query("DELETE FROM portfolio_items WHERE id = $id");
    $message = '<div class="alert alert-success">Portfolio item deleted.</div>';
}

// Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $category = $conn->real_escape_string($_POST['category']);
    $link = $conn->real_escape_string($_POST['link']);
    $image_url = $conn->real_escape_string($_POST['image_url']);
    $image_filename = '';
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload = uploadImage($_FILES['image'], '../uploads');
        if ($upload['success']) {
            $image_filename = $upload['filename'];
            $image_url = $upload['url'];
        } else {
            $message = '<div class="alert alert-danger">' . $upload['message'] . '</div>';
        }
    }
    
    if (isset($_POST['id']) && $_POST['id']) {
        $id = intval($_POST['id']);
        if ($image_filename) {
            $conn->query("UPDATE portfolio_items SET title='$title', description='$description', category='$category', link='$link', image_url='$image_url', image_filename='$image_filename' WHERE id=$id");
        } else {
            $conn->query("UPDATE portfolio_items SET title='$title', description='$description', category='$category', link='$link', image_url='$image_url' WHERE id=$id");
        }
        $message = '<div class="alert alert-success">Portfolio item updated.</div>';
    } else {
        $conn->query("INSERT INTO portfolio_items (title, description, category, link, image_url, image_filename) VALUES ('$title', '$description', '$category', '$link', '$image_url', '$image_filename')");
        $message = '<div class="alert alert-success">Portfolio item added.</div>';
    }
}

$edit_item = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_item = $conn->query("SELECT * FROM portfolio_items WHERE id = $id")->fetch_assoc();
}
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
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block bg-dark sidebar">
                <div class="position-sticky pt-3">
                    <h5 class="text-white px-3 mb-4">Admin Panel</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/dashboard.php">
                                <i class="fas fa-chart-line"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo SITE_URL; ?>/admin/portfolio.php">
                                <i class="fas fa-briefcase"></i> Portfolio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/services.php">
                                <i class="fas fa-cogs"></i> Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/about.php">
                                <i class="fas fa-user"></i> About
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/messages.php">
                                <i class="fas fa-envelope"></i> Messages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/social.php">
                                <i class="fas fa-share-alt"></i> Social Links
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                    <h1>Manage Portfolio</h1>
                </div>

                <?php echo $message; ?>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><?php echo $edit_item ? 'Edit Portfolio Item' : 'Add New Portfolio Item'; ?></h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <?php if ($edit_item): ?>
                                        <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                                    <?php endif; ?>
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="<?php echo $edit_item ? htmlspecialchars($edit_item['title']) : ''; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $edit_item ? htmlspecialchars($edit_item['description']) : ''; ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <input type="text" class="form-control" id="category" name="category" value="<?php echo $edit_item ? htmlspecialchars($edit_item['category']) : ''; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="link" class="form-label">Project Link</label>
                                        <input type="url" class="form-control" id="link" name="link" value="<?php echo $edit_item ? htmlspecialchars($edit_item['link']) : ''; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Upload Image</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        <small class="text-muted">Max 5MB. Formats: JPG, PNG, GIF, WebP</small>
                                        <?php if ($edit_item && $edit_item['image_url']): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo htmlspecialchars($edit_item['image_url']); ?>" alt="Current" style="max-width: 150px; border-radius: 5px;">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <label for="image_url" class="form-label">Or Image URL</label>
                                        <input type="url" class="form-control" id="image_url" name="image_url" value="<?php echo $edit_item ? htmlspecialchars($edit_item['image_url']) : ''; ?>" placeholder="https://...">
                                        <small class="text-muted">Use this if not uploading a file</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><?php echo $edit_item ? 'Update' : 'Add'; ?></button>
                                    <?php if ($edit_item): ?>
                                        <a href="<?php echo SITE_URL; ?>/admin/portfolio.php" class="btn btn-secondary">Cancel</a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Portfolio Items</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result = $conn->query("SELECT * FROM portfolio_items");
                                        while ($item = $result->fetch_assoc()):
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                                <td><?php echo htmlspecialchars($item['category']); ?></td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
