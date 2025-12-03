<?php
require '../config.php';
require '../includes/upload.php';
require '../includes/image-helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Manage About';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $greeting = $conn->real_escape_string($_POST['greeting']);
    $title = $conn->real_escape_string($_POST['title']);
    $subtitle = $conn->real_escape_string($_POST['subtitle']);
    $description = $conn->real_escape_string($_POST['description']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $location = $conn->real_escape_string($_POST['location']);
    $image_url = $conn->real_escape_string($_POST['image_url']);
    $image_filename = '';
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload = uploadImage($_FILES['image']);
        if ($upload['success']) {
            // Delete old image if exists
            if ($about && !empty($about['image_filename'])) {
                deleteImage($about['image_filename']);
            }
            $image_filename = $upload['filename'];
            $image_url = $upload['url'];
        } else {
            $message = '<div class="alert alert-danger">' . $upload['message'] . '</div>';
        }
    }
    
    if ($image_filename) {
        $conn->query("UPDATE about SET greeting='$greeting', title='$title', subtitle='$subtitle', description='$description', email='$email', phone='$phone', location='$location', image_url='$image_url', image_filename='$image_filename' WHERE id=1");
    } else {
        $conn->query("UPDATE about SET greeting='$greeting', title='$title', subtitle='$subtitle', description='$description', email='$email', phone='$phone', location='$location', image_url='$image_url' WHERE id=1");
    }
    $message = '<div class="alert alert-success">About section updated.</div>';
}

$about = $conn->query("SELECT * FROM about LIMIT 1")->fetch_assoc();
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
                    <h1>Manage About</h1>
                </div>

                <?php echo $message; ?>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Edit About Section</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="greeting" class="form-label">Greeting Message</label>
                                        <input type="text" class="form-control" id="greeting" name="greeting" value="<?php echo $about && !empty($about['greeting']) ? htmlspecialchars($about['greeting']) : ''; ?>" placeholder="e.g., Hello, welcome to my portfolio" required>
                                        <small class="text-muted">This greeting appears on a new line above the introduction on the hero section</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="<?php echo $about && !empty($about['title']) ? htmlspecialchars($about['title']) : ''; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="subtitle" class="form-label">Professional Title/Subtitle</label>
                                        <input type="text" class="form-control" id="subtitle" name="subtitle" value="<?php echo $about && !empty($about['subtitle']) ? htmlspecialchars($about['subtitle']) : ''; ?>" placeholder="e.g., Web Developer, Designer" required>
                                        <small class="text-muted">This appears after "I'm a" in the introduction (e.g., "I'm a Web Developer")</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="5" required><?php echo $about && !empty($about['description']) ? htmlspecialchars($about['description']) : ''; ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $about && !empty($about['email']) ? htmlspecialchars($about['email']) : ''; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $about && !empty($about['phone']) ? htmlspecialchars($about['phone']) : ''; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="location" name="location" value="<?php echo $about && !empty($about['location']) ? htmlspecialchars($about['location']) : ''; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Upload Image</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        <small class="text-muted">Max 5MB. Formats: JPG, PNG, GIF, WebP</small>
                                        <?php if ($about && !empty($about['image_url'])): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo getImageUrl($about['image_url']); ?>" alt="Current" style="max-width: 150px; border-radius: 5px;">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <label for="image_url" class="form-label">Or Image URL</label>
                                        <input type="text" class="form-control" id="image_url" name="image_url" value="<?php echo htmlspecialchars($about['image_url']); ?>" placeholder="https://...">
                                        <small class="text-muted">Use this if not uploading a file</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
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
