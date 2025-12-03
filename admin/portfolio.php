<?php
require '../config.php';
require '../includes/upload.php';
require '../includes/image-helper.php';
require '../includes/admin-list-helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Manage Portfolio';
$message = '';

// Check if redirected after deletion
if (isset($_GET['deleted'])) {
    if ($_GET['deleted'] === 'image') {
        $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Image deleted successfully.</div>';
    } elseif ($_GET['deleted'] === 'item') {
        $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Portfolio item deleted successfully.</div>';
    }
}

// Delete portfolio item
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $item = $conn->query("SELECT featured_image_filename FROM portfolio_items WHERE id = $id")->fetch_assoc();
    if ($item && $item['featured_image_filename']) {
        deleteImage($item['featured_image_filename']);
    }
    // Delete associated images
    $images = $conn->query("SELECT image_filename FROM portfolio_images WHERE portfolio_id = $id");
    while ($img = $images->fetch_assoc()) {
        if ($img['image_filename']) {
            deleteImage($img['image_filename']);
        }
    }
    $conn->query("DELETE FROM portfolio_items WHERE id = $id");
    ErrorLogger::log("Portfolio item deleted: ID $id", 'INFO');
    
    // Redirect back to portfolio list
    header('Location: ' . SITE_URL . '/admin/portfolio.php?deleted=item');
    exit;
}

// Delete portfolio image
if (isset($_GET['delete_image'])) {
    $image_id = intval($_GET['delete_image']);
    $image = $conn->query("SELECT image_filename, portfolio_id FROM portfolio_images WHERE id = $image_id")->fetch_assoc();
    $portfolio_id = $image['portfolio_id'] ?? 0;
    
    if ($image && $image['image_filename']) {
        deleteImage($image['image_filename']);
    }
    $conn->query("DELETE FROM portfolio_images WHERE id = $image_id");
    
    // Redirect back to edit view instead of exiting
    header('Location: ' . SITE_URL . '/admin/portfolio.php?edit=' . $portfolio_id . '&deleted=image');
    exit;
}

// Add/Edit portfolio item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_item') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $body = $conn->real_escape_string($_POST['body']);
    $category = $conn->real_escape_string($_POST['category']);
    $link = $conn->real_escape_string($_POST['link']);
    $featured_image_url = $conn->real_escape_string($_POST['featured_image_url']);
    $featured_image_filename = '';
    $status = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : 'published';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Handle featured image upload
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $upload = uploadImage($_FILES['featured_image']);
        if ($upload['success']) {
            // Delete old image if exists
            if (isset($_POST['id']) && $_POST['id']) {
                $old_item = $conn->query("SELECT featured_image_filename FROM portfolio_items WHERE id = " . intval($_POST['id']))->fetch_assoc();
                if ($old_item && !empty($old_item['featured_image_filename'])) {
                    deleteImage($old_item['featured_image_filename']);
                }
            }
            $featured_image_filename = $upload['filename'];
            $featured_image_url = $upload['url'];
        } else {
            $message = '<div class="alert alert-danger">' . $upload['message'] . '</div>';
        }
    }
    
    if (isset($_POST['id']) && $_POST['id']) {
        $id = intval($_POST['id']);
        if ($featured_image_filename) {
            $conn->query("UPDATE portfolio_items SET title='$title', description='$description', body='$body', category='$category', link='$link', featured_image_url='$featured_image_url', featured_image_filename='$featured_image_filename', status='$status', is_featured=$is_featured WHERE id=$id");
        } else {
            $conn->query("UPDATE portfolio_items SET title='$title', description='$description', body='$body', category='$category', link='$link', featured_image_url='$featured_image_url', status='$status', is_featured=$is_featured WHERE id=$id");
        }
        $message = '<div class="alert alert-success">Portfolio item updated successfully.</div>';
        ErrorLogger::log("Portfolio item updated: ID $id, Status: $status, Featured: $is_featured", 'INFO');
    } else {
        $conn->query("INSERT INTO portfolio_items (title, description, body, category, link, featured_image_url, featured_image_filename, status, is_featured) VALUES ('$title', '$description', '$body', '$category', '$link', '$featured_image_url', '$featured_image_filename', '$status', $is_featured)");
        $message = '<div class="alert alert-success">Portfolio item added successfully.</div>';
        ErrorLogger::log("Portfolio item created: $title, Status: $status", 'INFO');
    }
}

// Save images with alt text
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_images_with_alt') {
    $portfolio_id = intval($_POST['portfolio_id']);
    $upload_count = 0;
    $error_count = 0;
    
    // Process each uploaded image with its alt text
    if (isset($_POST['image_data']) && is_array($_POST['image_data'])) {
        foreach ($_POST['image_data'] as $image_data) {
            $image_url = $conn->real_escape_string($image_data['url']);
            $image_filename = $conn->real_escape_string($image_data['filename']);
            $alt_text = $conn->real_escape_string($image_data['alt_text']);
            
            $sort_order = $conn->query("SELECT MAX(sort_order) as max_order FROM portfolio_images WHERE portfolio_id = $portfolio_id")->fetch_assoc()['max_order'];
            $sort_order = ($sort_order === null) ? 0 : $sort_order + 1;
            
            if ($conn->query("INSERT INTO portfolio_images (portfolio_id, image_url, image_filename, alt_text, sort_order) VALUES ($portfolio_id, '$image_url', '$image_filename', '$alt_text', $sort_order)")) {
                $upload_count++;
            } else {
                $error_count++;
            }
        }
        
        // Display results
        if ($upload_count > 0 && $error_count === 0) {
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' . $upload_count . ' image(s) added successfully.</div>';
        } elseif ($upload_count > 0 && $error_count > 0) {
            $message = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> ' . $upload_count . ' image(s) added, but ' . $error_count . ' failed.</div>';
        } else {
            $message = '<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Failed to save images.</div>';
        }
    }
}

// Add portfolio images (multiple) - Upload and preview
$uploaded_images = [];
$show_alt_text_form = false;
$upload_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_image') {
    $portfolio_id = intval($_POST['portfolio_id']);
    $error_count = 0;
    $success_count = 0;
    
    // Handle multiple file uploads
    if (isset($_FILES['portfolio_images']) && is_array($_FILES['portfolio_images']['name'])) {
        $file_count = count($_FILES['portfolio_images']['name']);
        
        for ($i = 0; $i < $file_count; $i++) {
            // Skip empty files
            if ($_FILES['portfolio_images']['error'][$i] !== 0) {
                $error_count++;
                ErrorLogger::log("File upload error for file $i: " . $_FILES['portfolio_images']['error'][$i], 'WARNING');
                continue;
            }
            
            // Create single file array for uploadImage function
            $single_file = [
                'name' => $_FILES['portfolio_images']['name'][$i],
                'type' => $_FILES['portfolio_images']['type'][$i],
                'tmp_name' => $_FILES['portfolio_images']['tmp_name'][$i],
                'error' => $_FILES['portfolio_images']['error'][$i],
                'size' => $_FILES['portfolio_images']['size'][$i]
            ];
            
            $upload = uploadImage($single_file);
            if ($upload['success']) {
                $uploaded_images[] = [
                    'filename' => $upload['filename'],
                    'url' => $upload['url'],
                    'name' => $_FILES['portfolio_images']['name'][$i]
                ];
                $success_count++;
                ErrorLogger::log("Image uploaded successfully: " . $_FILES['portfolio_images']['name'][$i], 'INFO');
            } else {
                $error_count++;
                ErrorLogger::log("Image upload failed: " . $_FILES['portfolio_images']['name'][$i] . " - " . $upload['message'], 'WARNING');
            }
        }
        
        if ($success_count > 0) {
            $show_alt_text_form = true;
            $upload_message = '<div class="alert alert-info">Uploaded ' . $success_count . ' image(s). Please add alt text for each.</div>';
        } elseif ($error_count > 0) {
            $upload_message = '<div class="alert alert-danger">Failed to upload ' . $error_count . ' image(s). Please try again.</div>';
        }
    }
}

$edit_item = null;
$show_form = false;
if (isset($_GET['edit'])) {
    $show_form = true;
    if ($_GET['edit'] !== 'new') {
        $id = intval($_GET['edit']);
        $edit_item = $conn->query("SELECT * FROM portfolio_items WHERE id = $id")->fetch_assoc();
    }
    // If edit=new, $edit_item stays null and form shows as "Add New"
}

// Get all portfolio items
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pagination = getPaginatedItems($conn, 'portfolio_items', $page, 10, 'id DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
    <style>
        .ql-container { font-size: 16px; min-height: 300px; }
        .ql-editor { min-height: 300px; }
        .portfolio-image-preview { max-width: 100px; height: auto; border-radius: 5px; }
        .image-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-top: 15px; }
        .image-item { position: relative; border-radius: 5px; overflow: hidden; cursor: grab; transition: all 0.2s ease; }
        .image-item:active { cursor: grabbing; }
        .image-item.drag-over { opacity: 0.5; transform: scale(0.95); }
        .image-item-inner { position: relative; width: 100%; height: 120px; }
        .image-item img { width: 100%; height: 100%; object-fit: cover; }
        .image-item-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: space-between; padding: 5px; opacity: 0; transition: opacity 0.3s ease; }
        .image-item:hover .image-item-overlay { opacity: 1; }
        .drag-handle { color: white; font-size: 16px; font-weight: bold; cursor: grab; }
        .image-item:active .drag-handle { cursor: grabbing; }
        .image-item .delete-btn { background: rgba(255,0,0,0.8); color: white; border: none; border-radius: 3px; padding: 4px 8px; cursor: pointer; font-size: 12px; transition: background 0.2s ease; }
        .image-item .delete-btn:hover { background: rgba(255,0,0,1); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/admin-sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                    <h1>Manage Portfolio</h1>
                    <?php if (!$edit_item): ?>
                        <a href="?edit=new" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Portfolio Item
                        </a>
                    <?php endif; ?>
                </div>

                <?php echo $message; ?>

                <!-- Portfolio Items List Table -->
                <?php if (!$show_form): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>All Portfolio Items (<?php echo $pagination['total_count']; ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $columns = [
                            'title' => 'Title',
                            'category' => 'Category',
                            'featured_image_url' => 'Image',
                            'status' => 'Status',
                            'is_featured' => 'Featured'
                        ];
                        displayAdminTable(
                            $pagination['items'],
                            $columns,
                            SITE_URL . '/admin/portfolio.php?edit=%d',
                            SITE_URL . '/admin/portfolio.php?delete=%d'
                        );
                        ?>
                    </div>
                </div>
                <?php displayPagination($pagination['current_page'], $pagination['total_pages'], SITE_URL . '/admin/portfolio.php'); ?>
                <?php else: ?>

                <!-- Edit/Add Form -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><?php echo $edit_item ? 'Edit Portfolio Item' : 'Add New Portfolio Item'; ?></h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="save_item">
                                    <?php if ($edit_item): ?>
                                        <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="<?php echo $edit_item && !empty($edit_item['title']) ? htmlspecialchars($edit_item['title']) : ''; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Short Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="2" required><?php echo $edit_item && !empty($edit_item['description']) ? htmlspecialchars($edit_item['description']) : ''; ?></textarea>
                                        <small class="text-muted">This appears on the portfolio list page</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="body" class="form-label">Full Description (Rich Text)</label>
                                        <div id="editor" style="background: white;"></div>
                                        <textarea id="body" name="body" style="display:none;"></textarea>
                                        <small class="text-muted">This appears on the project detail page</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <div class="input-group">
                                            <select class="form-control" id="category" name="category">
                                                <option value="">Select a category...</option>
                                                <?php
                                                $categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
                                                while ($cat = $categories->fetch_assoc()):
                                                ?>
                                                <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo ($edit_item && $edit_item['category'] === $cat['name']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cat['name']); ?>
                                                </option>
                                                <?php endwhile; ?>
                                            </select>
                                            <a href="<?php echo SITE_URL; ?>/admin/categories.php?edit=new" class="btn btn-outline-primary" target="_blank" title="Add new category">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                        <small class="text-muted">Select from existing categories or click + to add a new one</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="link" class="form-label">Project Link</label>
                                        <input type="url" class="form-control" id="link" name="link" value="<?php echo $edit_item && !empty($edit_item['link']) ? htmlspecialchars($edit_item['link']) : ''; ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">Featured Image (Displayed on Portfolio List)</label>
                                        <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                                        <small class="text-muted">Max 5MB. Recommended: 600x400px</small>
                                        <?php if ($edit_item && !empty($edit_item['featured_image_url'])): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo getImageUrl($edit_item['featured_image_url']); ?>" alt="Featured" class="portfolio-image-preview">
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-3">
                                        <label for="featured_image_url" class="form-label">Or Featured Image URL</label>
                                        <input type="text" class="form-control" id="featured_image_url" name="featured_image_url" value="<?php echo $edit_item && !empty($edit_item['featured_image_url']) ? htmlspecialchars($edit_item['featured_image_url']) : ''; ?>" placeholder="https://...">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="published" <?php echo (!$edit_item || $edit_item['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                                                <option value="draft" <?php echo ($edit_item && $edit_item['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                            </select>
                                            <small class="text-muted">Published items are visible on the portfolio page</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Options</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" <?php echo ($edit_item && $edit_item['is_featured']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_featured">
                                                    Featured Work
                                                </label>
                                            </div>
                                            <small class="text-muted d-block">Featured items appear in the featured works section</small>
                                        </div>
                                    </div>

                                    <div class="btn-group" role="group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> <?php echo ($edit_item && isset($edit_item['id'])) ? 'Update' : 'Add'; ?> Portfolio Item
                                        </button>
                                        <a href="<?php echo SITE_URL; ?>/admin/portfolio.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Add Images to Portfolio -->
                        <?php if ($edit_item): ?>
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Portfolio Images Gallery</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($upload_message)): ?>
                                    <?php echo $upload_message; ?>
                                <?php endif; ?>

                                <?php if ($show_alt_text_form && !empty($uploaded_images)): ?>
                                    <!-- Alt Text Form (shown after upload) -->
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Please add alt text for each image for better accessibility.
                                    </div>
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="action" value="save_images_with_alt">
                                        <input type="hidden" name="portfolio_id" value="<?php echo $edit_item['id']; ?>">
                                        
                                        <?php foreach ($uploaded_images as $index => $image): ?>
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <img src="<?php echo htmlspecialchars($image['url']); ?>" alt="Preview" class="img-fluid rounded" style="max-height: 150px;">
                                                    </div>
                                                    <div class="col-md-9">
                                                        <h6 class="card-title"><?php echo htmlspecialchars($image['name']); ?></h6>
                                                        <div class="mb-3">
                                                            <label class="form-label">Alt Text for this image</label>
                                                            <input type="text" class="form-control" name="image_data[<?php echo $index; ?>][alt_text]" placeholder="Describe what's in this image" value="">
                                                            <small class="text-muted">This helps with accessibility and SEO</small>
                                                        </div>
                                                        <input type="hidden" name="image_data[<?php echo $index; ?>][url]" value="<?php echo htmlspecialchars($image['url']); ?>">
                                                        <input type="hidden" name="image_data[<?php echo $index; ?>][filename]" value="<?php echo htmlspecialchars($image['filename']); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                        
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save"></i> Save All Images
                                            </button>
                                            <a href="<?php echo SITE_URL; ?>/admin/portfolio.php?edit=<?php echo $edit_item['id']; ?>" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Cancel
                                            </a>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <!-- Upload Form -->
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="action" value="add_image">
                                        <input type="hidden" name="portfolio_id" value="<?php echo $edit_item['id']; ?>">
                                        
                                        <div class="mb-3">
                                            <label for="portfolio_images" class="form-label">Add Images to Gallery</label>
                                            <input type="file" class="form-control" id="portfolio_images" name="portfolio_images[]" accept="image/*" multiple required>
                                            <small class="text-muted">Select one or multiple images. Max 5MB each. Recommended: 800x600px</small>
                                            <div id="filePreview" class="mt-2"></div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-upload"></i> Upload Images
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Display Gallery -->
                                <?php
                                $images = $conn->query("SELECT * FROM portfolio_images WHERE portfolio_id = " . $edit_item['id'] . " ORDER BY sort_order");
                                if ($images->num_rows > 0):
                                ?>
                                <div class="image-gallery" id="sortableGallery" style="margin-top: 20px;">
                                    <?php while ($img = $images->fetch_assoc()): ?>
                                    <?php if (!empty($img['image_url'])): ?>
                                    <div class="image-item" draggable="true" data-image-id="<?php echo $img['id']; ?>">
                                        <div class="image-item-inner">
                                            <img src="<?php echo getImageUrl($img['image_url']); ?>" alt="<?php echo !empty($img['alt_text']) ? htmlspecialchars($img['alt_text']) : 'Gallery Image'; ?>">
                                            <div class="image-item-overlay">
                                                <span class="drag-handle" title="Drag to reorder">⋮⋮</span>
                                                <a href="?delete_image=<?php echo $img['id']; ?>" class="delete-btn" onclick="return confirm('Delete this image?')">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php endwhile; ?>
                                </div>
                                <small class="text-muted d-block mt-2">💡 Drag images to reorder the gallery</small>
                                <?php else: ?>
                                <p class="text-muted">No images added yet. Upload your first image above.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Quill editor
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            }
        });

        // Load existing content
        <?php if ($edit_item && !empty($edit_item['body'])): ?>
        quill.root.innerHTML = <?php echo json_encode($edit_item['body']); ?>;
        <?php endif; ?>

        // Update hidden textarea before form submission
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('body').value = quill.root.innerHTML;
        });

        // Drag and drop reordering
        let draggedElement = null;

        const gallery = document.getElementById('sortableGallery');
        if (gallery) {
            const items = gallery.querySelectorAll('.image-item');
            
            items.forEach(item => {
                item.addEventListener('dragstart', function(e) {
                    draggedElement = this;
                    this.style.opacity = '0.5';
                    e.dataTransfer.effectAllowed = 'move';
                });

                item.addEventListener('dragend', function(e) {
                    this.style.opacity = '1';
                    items.forEach(i => i.classList.remove('drag-over'));
                    draggedElement = null;
                });

                item.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    if (this !== draggedElement) {
                        this.classList.add('drag-over');
                    }
                });

                item.addEventListener('dragleave', function(e) {
                    this.classList.remove('drag-over');
                });

                item.addEventListener('drop', function(e) {
                    e.preventDefault();
                    if (this !== draggedElement) {
                        // Swap elements
                        gallery.insertBefore(draggedElement, this);
                        updateImageOrder();
                    }
                    this.classList.remove('drag-over');
                });
            });
        }

        function updateImageOrder() {
            const items = document.querySelectorAll('#sortableGallery .image-item');
            const order = [];
            items.forEach((item, index) => {
                order.push({
                    id: item.dataset.imageId,
                    sort_order: index
                });
            });

            // Send order to server
            fetch('<?php echo SITE_URL; ?>/admin/update-image-order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ images: order })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Image order updated');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // File preview for multiple image uploads
        const fileInput = document.getElementById('portfolio_images');
        const filePreview = document.getElementById('filePreview');

        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                filePreview.innerHTML = '';
                const files = this.files;
                
                if (files.length === 0) {
                    return;
                }

                const previewContainer = document.createElement('div');
                previewContainer.className = 'alert alert-info';
                previewContainer.innerHTML = `<strong>${files.length} file(s) selected:</strong>`;
                
                const fileList = document.createElement('ul');
                fileList.className = 'mb-0 mt-2';
                
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    
                    const listItem = document.createElement('li');
                    listItem.innerHTML = `
                        <strong>${file.name}</strong> 
                        <span class="text-muted">(${fileSize} MB)</span>
                    `;
                    fileList.appendChild(listItem);
                }
                
                previewContainer.appendChild(fileList);
                filePreview.appendChild(previewContainer);
            });
        }
    </script>
</body>
</html>
