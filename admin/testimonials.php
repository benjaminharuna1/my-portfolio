<?php
require '../config.php';
require '../includes/upload.php';
require '../includes/admin-list-helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Manage Testimonials';
$message = '';

// Delete testimonial
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $testimonial = $conn->query("SELECT client_image_filename FROM testimonials WHERE id = $id")->fetch_assoc();
    if ($testimonial && !empty($testimonial['client_image_filename'])) {
        deleteImage($testimonial['client_image_filename']);
    }
    $conn->query("DELETE FROM testimonials WHERE id = $id");
    $message = '<div class="alert alert-success">Testimonial deleted.</div>';
    ErrorLogger::log("Testimonial deleted: ID $id", 'INFO');
}

// Add/Edit testimonial
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $client_name = $conn->real_escape_string($_POST['client_name']);
    $client_title = $conn->real_escape_string($_POST['client_title']);
    $client_company = $conn->real_escape_string($_POST['client_company']);
    $testimonial_text = $conn->real_escape_string($_POST['testimonial_text']);
    $rating = intval($_POST['rating']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $client_image_url = $conn->real_escape_string($_POST['client_image_url']);
    $client_image_filename = '';

    // Handle image upload
    if (isset($_FILES['client_image']) && $_FILES['client_image']['error'] === 0) {
        $upload = uploadImage($_FILES['client_image']);
        if ($upload['success']) {
            // Delete old image if exists and new image is being uploaded
            if (isset($_POST['id']) && $_POST['id']) {
                $old_testimonial = $conn->query("SELECT client_image_filename FROM testimonials WHERE id = " . intval($_POST['id']))->fetch_assoc();
                if ($old_testimonial && !empty($old_testimonial['client_image_filename'])) {
                    deleteImage($old_testimonial['client_image_filename']);
                }
            }
            $client_image_filename = $upload['filename'];
            $client_image_url = $upload['url'];
        } else {
            $message = '<div class="alert alert-danger">' . $upload['message'] . '</div>';
        }
    }

    if (isset($_POST['id']) && $_POST['id']) {
        $id = intval($_POST['id']);
        if ($client_image_filename) {
            $conn->query("UPDATE testimonials SET client_name='$client_name', client_title='$client_title', client_company='$client_company', testimonial_text='$testimonial_text', rating=$rating, is_featured=$is_featured, is_active=$is_active, client_image_url='$client_image_url', client_image_filename='$client_image_filename' WHERE id=$id");
        } else {
            $conn->query("UPDATE testimonials SET client_name='$client_name', client_title='$client_title', client_company='$client_company', testimonial_text='$testimonial_text', rating=$rating, is_featured=$is_featured, is_active=$is_active, client_image_url='$client_image_url' WHERE id=$id");
        }
        $message = '<div class="alert alert-success">Testimonial updated successfully.</div>';
        ErrorLogger::log("Testimonial updated: ID $id", 'INFO');
    } else {
        $conn->query("INSERT INTO testimonials (client_name, client_title, client_company, testimonial_text, rating, is_featured, is_active, client_image_url, client_image_filename) VALUES ('$client_name', '$client_title', '$client_company', '$testimonial_text', $rating, $is_featured, $is_active, '$client_image_url', '$client_image_filename')");
        $message = '<div class="alert alert-success">Testimonial added successfully.</div>';
        ErrorLogger::log("Testimonial created: $client_name", 'INFO');
    }
}

$edit_item = null;
$show_form = false;
if (isset($_GET['edit'])) {
    $show_form = true;
    if ($_GET['edit'] !== 'new') {
        $id = intval($_GET['edit']);
        $edit_item = $conn->query("SELECT * FROM testimonials WHERE id = $id")->fetch_assoc();
    }
    // If edit=new, $edit_item stays null and form shows as "Add New"
}

// Get all testimonials
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pagination = getPaginatedItems($conn, 'testimonials', $page, 10, 'id DESC');
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
        .rating-display { color: #ffc107; font-size: 1.2rem; }
        .testimonial-preview { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 10px; }
        .client-image-preview { max-width: 100px; height: 100px; border-radius: 50%; object-fit: cover; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/admin-sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                    <h1>Manage Testimonials</h1>
                    <?php if (!$edit_item): ?>
                        <a href="?edit=new" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Testimonial
                        </a>
                    <?php endif; ?>
                </div>

                <?php echo $message; ?>

                <!-- Testimonials List Table -->
                <?php if (!$show_form): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>All Testimonials (<?php echo $pagination['total_count']; ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $columns = [
                            'client_name' => 'Client Name',
                            'client_title' => 'Title',
                            'client_company' => 'Company',
                            'rating' => 'Rating',
                            'is_featured' => 'Featured',
                            'is_active' => 'Active'
                        ];
                        displayAdminTable(
                            $pagination['items'],
                            $columns,
                            SITE_URL . '/admin/testimonials.php?edit=%d',
                            SITE_URL . '/admin/testimonials.php?delete=%d'
                        );
                        ?>
                    </div>
                </div>
                <?php displayPagination($pagination['current_page'], $pagination['total_pages'], SITE_URL . '/admin/testimonials.php'); ?>
                <?php else: ?>

                <!-- Edit/Add Form -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><?php echo $edit_item ? 'Edit Testimonial' : 'Add New Testimonial'; ?></h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="save">
                                    <?php if ($edit_item): ?>
                                        <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                                    <?php endif; ?>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="client_name" class="form-label">Client Name</label>
                                            <input type="text" class="form-control" id="client_name" name="client_name" value="<?php echo $edit_item && !empty($edit_item['client_name']) ? htmlspecialchars($edit_item['client_name']) : ''; ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="client_title" class="form-label">Client Title</label>
                                            <input type="text" class="form-control" id="client_title" name="client_title" value="<?php echo $edit_item && !empty($edit_item['client_title']) ? htmlspecialchars($edit_item['client_title']) : ''; ?>" placeholder="e.g., CEO, Manager">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="client_company" class="form-label">Client Company</label>
                                        <input type="text" class="form-control" id="client_company" name="client_company" value="<?php echo $edit_item && !empty($edit_item['client_company']) ? htmlspecialchars($edit_item['client_company']) : ''; ?>" placeholder="Company name">
                                    </div>

                                    <div class="mb-3">
                                        <label for="testimonial_text" class="form-label">Testimonial Text</label>
                                        <textarea class="form-control" id="testimonial_text" name="testimonial_text" rows="4" required><?php echo $edit_item && !empty($edit_item['testimonial_text']) ? htmlspecialchars($edit_item['testimonial_text']) : ''; ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="rating" class="form-label">Rating</label>
                                            <select class="form-control" id="rating" name="rating" required>
                                                <option value="5" <?php echo ($edit_item && $edit_item['rating'] == 5) ? 'selected' : ''; ?>>5 Stars ★★★★★</option>
                                                <option value="4" <?php echo ($edit_item && $edit_item['rating'] == 4) ? 'selected' : ''; ?>>4 Stars ★★★★☆</option>
                                                <option value="3" <?php echo ($edit_item && $edit_item['rating'] == 3) ? 'selected' : ''; ?>>3 Stars ★★★☆☆</option>
                                                <option value="2" <?php echo ($edit_item && $edit_item['rating'] == 2) ? 'selected' : ''; ?>>2 Stars ★★☆☆☆</option>
                                                <option value="1" <?php echo ($edit_item && $edit_item['rating'] == 1) ? 'selected' : ''; ?>>1 Star ★☆☆☆☆</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Status</label>
                                            <div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo (!$edit_item || $edit_item['is_active']) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="is_active">Active</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" <?php echo ($edit_item && $edit_item['is_featured']) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="is_featured">Featured</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="client_image" class="form-label">Client Image</label>
                                        <input type="file" class="form-control" id="client_image" name="client_image" accept="image/*">
                                        <small class="text-muted">Max 5MB. Recommended: 300x300px</small>
                                        <?php if ($edit_item && !empty($edit_item['client_image_url'])): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo htmlspecialchars($edit_item['client_image_url']); ?>" alt="Client" class="client-image-preview">
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-3">
                                        <label for="client_image_url" class="form-label">Or Image URL</label>
                                        <input type="url" class="form-control" id="client_image_url" name="client_image_url" value="<?php echo $edit_item && !empty($edit_item['client_image_url']) ? htmlspecialchars($edit_item['client_image_url']) : ''; ?>" placeholder="https://...">
                                    </div>

                                    <div class="btn-group" role="group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> <?php echo ($edit_item && isset($edit_item['id'])) ? 'Update' : 'Add'; ?> Testimonial
                                        </button>
                                        <a href="<?php echo SITE_URL; ?>/admin/testimonials.php" class="btn btn-secondary">
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
