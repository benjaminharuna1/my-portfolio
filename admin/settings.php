<?php
require '../config.php';
require '../includes/upload.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Website Settings';
$message = '';

// Get current settings
$settings = $conn->query("SELECT * FROM website_settings LIMIT 1")->fetch_assoc();
if (!$settings) {
    $conn->query("INSERT INTO website_settings (website_name, website_description) VALUES ('My Portfolio', 'A professional portfolio website')");
    $settings = $conn->query("SELECT * FROM website_settings LIMIT 1")->fetch_assoc();
}

// Update settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_settings') {
    $website_name = $conn->real_escape_string($_POST['website_name']);
    $website_description = $conn->real_escape_string($_POST['website_description']);
    $logo_url = $conn->real_escape_string($_POST['logo_url']);
    $favicon_url = $conn->real_escape_string($_POST['favicon_url']);
    $logo_filename = '';
    $favicon_filename = '';
    
    // Handle logo upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
        $upload = uploadImage($_FILES['logo'], '../uploads');
        if ($upload['success']) {
            $logo_filename = $upload['filename'];
            $logo_url = $upload['url'];
        } else {
            $message = '<div class="alert alert-danger">Logo upload failed: ' . $upload['message'] . '</div>';
        }
    }
    
    // Handle favicon upload
    if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === 0) {
        $upload = uploadImage($_FILES['favicon'], '../uploads');
        if ($upload['success']) {
            $favicon_filename = $upload['filename'];
            $favicon_url = $upload['url'];
        } else {
            $message = '<div class="alert alert-danger">Favicon upload failed: ' . $upload['message'] . '</div>';
        }
    }
    
    // Build update query
    $update_parts = array(
        "website_name='$website_name'",
        "website_description='$website_description'",
        "logo_url='$logo_url'",
        "favicon_url='$favicon_url'"
    );
    
    if ($logo_filename) {
        $update_parts[] = "logo_filename='$logo_filename'";
    }
    if ($favicon_filename) {
        $update_parts[] = "favicon_filename='$favicon_filename'";
    }
    
    $update_query = "UPDATE website_settings SET " . implode(", ", $update_parts) . " WHERE id = " . $settings['id'];
    $conn->query($update_query);
    
    $message = '<div class="alert alert-success">Website settings updated successfully!</div>';
    $settings = $conn->query("SELECT * FROM website_settings LIMIT 1")->fetch_assoc();
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
    <style>
        .logo-preview { max-width: 200px; height: auto; border-radius: 5px; margin-top: 10px; }
        .favicon-preview { width: 64px; height: 64px; border-radius: 5px; margin-top: 10px; }
    </style>
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
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/portfolio.php">
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
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/profile.php">
                                <i class="fas fa-user-circle"></i> My Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo SITE_URL; ?>/admin/settings.php">
                                <i class="fas fa-cog"></i> Website Settings
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
                    <h1>Website Settings</h1>
                </div>

                <?php echo $message; ?>

                <div class="row mt-4">
                    <div class="col-md-8">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update_settings">

                            <!-- Website Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Website Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="website_name" class="form-label">Website Name</label>
                                        <input type="text" class="form-control" id="website_name" name="website_name" value="<?php echo $settings && !empty($settings['website_name']) ? htmlspecialchars($settings['website_name']) : ''; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="website_description" class="form-label">Website Description</label>
                                        <textarea class="form-control" id="website_description" name="website_description" rows="3"><?php echo $settings && !empty($settings['website_description']) ? htmlspecialchars($settings['website_description']) : ''; ?></textarea>
                                        <small class="text-muted">Used for SEO and meta tags</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Logo -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Website Logo</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Upload Logo</label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                        <small class="text-muted">Max 5MB. Recommended: 200x100px or square</small>
                                        <?php if ($settings && !empty($settings['logo_url'])): ?>
                                            <div class="mt-2">
                                                <strong>Current Logo:</strong>
                                                <img src="<?php echo htmlspecialchars($settings['logo_url']); ?>" alt="Logo" class="logo-preview">
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-3">
                                        <label for="logo_url" class="form-label">Or Logo URL</label>
                                        <input type="url" class="form-control" id="logo_url" name="logo_url" value="<?php echo $settings && !empty($settings['logo_url']) ? htmlspecialchars($settings['logo_url']) : ''; ?>" placeholder="https://...">
                                    </div>
                                </div>
                            </div>

                            <!-- Favicon -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Website Favicon</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="favicon" class="form-label">Upload Favicon</label>
                                        <input type="file" class="form-control" id="favicon" name="favicon" accept="image/*">
                                        <small class="text-muted">Max 5MB. Recommended: 64x64px or 32x32px (PNG or ICO)</small>
                                        <?php if ($settings && !empty($settings['favicon_url'])): ?>
                                            <div class="mt-2">
                                                <strong>Current Favicon:</strong>
                                                <img src="<?php echo htmlspecialchars($settings['favicon_url']); ?>" alt="Favicon" class="favicon-preview">
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-3">
                                        <label for="favicon_url" class="form-label">Or Favicon URL</label>
                                        <input type="url" class="form-control" id="favicon_url" name="favicon_url" value="<?php echo $settings && !empty($settings['favicon_url']) ? htmlspecialchars($settings['favicon_url']) : ''; ?>" placeholder="https://...">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                        </form>
                    </div>

                    <!-- Preview -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Preview</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <strong>Logo Preview:</strong>
                                    <?php if ($settings && !empty($settings['logo_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($settings['logo_url']); ?>" alt="Logo" class="img-fluid mt-2" style="max-width: 150px;">
                                    <?php else: ?>
                                        <p class="text-muted mt-2">No logo uploaded</p>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-4">
                                    <strong>Favicon Preview:</strong>
                                    <?php if ($settings && !empty($settings['favicon_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($settings['favicon_url']); ?>" alt="Favicon" class="mt-2" style="width: 64px; height: 64px;">
                                    <?php else: ?>
                                        <p class="text-muted mt-2">No favicon uploaded</p>
                                    <?php endif; ?>
                                </div>

                                <div class="alert alert-info">
                                    <strong>Note:</strong> Changes to logo and favicon may take a few minutes to appear due to browser caching. Try clearing your browser cache if changes don't appear immediately.
                                </div>
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
