<?php
require '../config.php';
require '../includes/upload.php';
require '../includes/image-helper.php';

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

// Get email settings
$email_settings = $conn->query("SELECT * FROM email_settings LIMIT 1")->fetch_assoc();
if (!$email_settings) {
    $conn->query("INSERT INTO email_settings (smtp_host, smtp_port, smtp_username, smtp_password, from_email, from_name, admin_email, enable_notifications) VALUES ('', 587, '', '', '', 'Portfolio', '', 1)");
    $email_settings = $conn->query("SELECT * FROM email_settings LIMIT 1")->fetch_assoc();
}

// Update settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_settings') {
        $website_name = $conn->real_escape_string($_POST['website_name']);
        $website_description = $conn->real_escape_string($_POST['website_description']);
        $logo_url = $conn->real_escape_string($_POST['logo_url']);
        $favicon_url = $conn->real_escape_string($_POST['favicon_url']);
        $logo_filename = '';
        $favicon_filename = '';
        
        // Handle logo upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
            $upload = uploadImage($_FILES['logo']);
            if ($upload['success']) {
                // Delete old logo if exists
                if ($settings && !empty($settings['logo_filename'])) {
                    deleteImage($settings['logo_filename']);
                }
                $logo_filename = $upload['filename'];
                $logo_url = $upload['url'];
            } else {
                $message = '<div class="alert alert-danger">Logo upload failed: ' . $upload['message'] . '</div>';
            }
        }
        
        // Handle favicon upload
        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === 0) {
            $upload = uploadImage($_FILES['favicon']);
            if ($upload['success']) {
                // Delete old favicon if exists
                if ($settings && !empty($settings['favicon_filename'])) {
                    deleteImage($settings['favicon_filename']);
                }
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
    } elseif ($_POST['action'] === 'update_email_settings') {
        $smtp_host = $conn->real_escape_string($_POST['smtp_host']);
        $smtp_port = intval($_POST['smtp_port']);
        $smtp_username = $conn->real_escape_string($_POST['smtp_username']);
        $smtp_password = $conn->real_escape_string($_POST['smtp_password']);
        $from_email = $conn->real_escape_string($_POST['from_email']);
        $from_name = $conn->real_escape_string($_POST['from_name']);
        $admin_email = $conn->real_escape_string($_POST['admin_email']);
        $enable_notifications = isset($_POST['enable_notifications']) ? 1 : 0;
        
        $conn->query("UPDATE email_settings SET smtp_host='$smtp_host', smtp_port=$smtp_port, smtp_username='$smtp_username', smtp_password='$smtp_password', from_email='$from_email', from_name='$from_name', admin_email='$admin_email', enable_notifications=$enable_notifications WHERE id = " . $email_settings['id']);
        
        $message = '<div class="alert alert-success">Email settings updated successfully!</div>';
        $email_settings = $conn->query("SELECT * FROM email_settings LIMIT 1")->fetch_assoc();
    }
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
            <?php include '../includes/admin-sidebar.php'; ?>

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
                                                <img src="<?php echo getImageUrl($settings['logo_url']); ?>" alt="Logo" class="logo-preview">
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
                                                <img src="<?php echo getImageUrl($settings['favicon_url']); ?>" alt="Favicon" class="favicon-preview">
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

                        <!-- Email Settings -->
                        <form method="POST" class="mt-5">
                            <input type="hidden" name="action" value="update_email_settings">

                            <div class="card">
                                <div class="card-header">
                                    <h5>Email Configuration</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <strong>Note:</strong> Configure SMTP settings to enable email notifications for reviews and contact messages.
                                    </div>

                                    <div class="mb-3">
                                        <label for="smtp_host" class="form-label">SMTP Host</label>
                                        <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?php echo htmlspecialchars($email_settings['smtp_host'] ?? ''); ?>" placeholder="smtp.gmail.com">
                                        <small class="text-muted">e.g., smtp.gmail.com, smtp.mailtrap.io</small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="smtp_port" class="form-label">SMTP Port</label>
                                            <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="<?php echo $email_settings['smtp_port'] ?? 587; ?>" placeholder="587">
                                            <small class="text-muted">Usually 587 (TLS) or 465 (SSL)</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="from_email" class="form-label">From Email Address</label>
                                            <input type="email" class="form-control" id="from_email" name="from_email" value="<?php echo htmlspecialchars($email_settings['from_email'] ?? ''); ?>" placeholder="noreply@example.com">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="from_name" class="form-label">From Name</label>
                                        <input type="text" class="form-control" id="from_name" name="from_name" value="<?php echo htmlspecialchars($email_settings['from_name'] ?? 'Portfolio'); ?>" placeholder="Portfolio">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="smtp_username" class="form-label">SMTP Username</label>
                                            <input type="text" class="form-control" id="smtp_username" name="smtp_username" value="<?php echo htmlspecialchars($email_settings['smtp_username'] ?? ''); ?>" placeholder="your-email@gmail.com">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="smtp_password" class="form-label">SMTP Password</label>
                                            <input type="password" class="form-control" id="smtp_password" name="smtp_password" value="<?php echo htmlspecialchars($email_settings['smtp_password'] ?? ''); ?>" placeholder="••••••••">
                                            <small class="text-muted">For Gmail, use an App Password, not your regular password</small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="admin_email" class="form-label">Admin Email Address</label>
                                        <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($email_settings['admin_email'] ?? ''); ?>" placeholder="admin@example.com">
                                        <small class="text-muted">Where to send notifications about reviews and messages</small>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enable_notifications" name="enable_notifications" <?php echo ($email_settings['enable_notifications'] ?? 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="enable_notifications">
                                                Enable Email Notifications
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-2">Send notifications for new reviews and contact messages</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> Save Email Settings
                                    </button>
                                </div>
                            </div>
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
                                        <img src="<?php echo getImageUrl($settings['logo_url']); ?>" alt="Logo" class="img-fluid mt-2" style="max-width: 150px;">
                                    <?php else: ?>
                                        <p class="text-muted mt-2">No logo uploaded</p>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-4">
                                    <strong>Favicon Preview:</strong>
                                    <?php if ($settings && !empty($settings['favicon_url'])): ?>
                                        <img src="<?php echo getImageUrl($settings['favicon_url']); ?>" alt="Favicon" class="mt-2" style="width: 64px; height: 64px;">
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
