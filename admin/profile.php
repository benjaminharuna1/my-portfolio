<?php
require '../config.php';
require '../includes/upload.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'My Profile';
$message = '';
$user_id = $_SESSION['user_id'];

// Get current user
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $bio = $conn->real_escape_string($_POST['bio']);
    $avatar_url = $conn->real_escape_string($_POST['avatar_url']);
    $avatar_filename = '';
    
    // Handle avatar upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $upload = uploadImage($_FILES['avatar'], '../uploads');
        if ($upload['success']) {
            $avatar_filename = $upload['filename'];
            $avatar_url = $upload['url'];
        } else {
            $message = '<div class="alert alert-danger">' . $upload['message'] . '</div>';
        }
    }
    
    if ($avatar_filename) {
        $conn->query("UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', phone='$phone', bio='$bio', avatar_url='$avatar_url', avatar_filename='$avatar_filename' WHERE id=$user_id");
    } else {
        $conn->query("UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', phone='$phone', bio='$bio', avatar_url='$avatar_url' WHERE id=$user_id");
    }
    
    $message = '<div class="alert alert-success">Profile updated successfully!</div>';
    $user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
}

// Update password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_password') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $conn->query("UPDATE users SET password='$hashed_password' WHERE id=$user_id");
                $message = '<div class="alert alert-success">Password updated successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger">Password must be at least 6 characters long.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">New passwords do not match.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Current password is incorrect.</div>';
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
        .profile-avatar { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #667eea; }
        .avatar-preview { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-top: 10px; }
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
                            <a class="nav-link active" href="<?php echo SITE_URL; ?>/admin/profile.php">
                                <i class="fas fa-user-circle"></i> My Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/settings.php">
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
                    <h1>My Profile</h1>
                </div>

                <?php echo $message; ?>

                <div class="row mt-4">
                    <!-- Profile Information -->
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Profile Information</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="update_profile">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="first_name" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="last_name" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="bio" class="form-label">Bio</label>
                                        <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                        <small class="text-muted">Tell visitors about yourself</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="avatar" class="form-label">Profile Picture</label>
                                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                        <small class="text-muted">Max 5MB. Recommended: 300x300px</small>
                                        <?php if ($user && !empty($user['avatar_url'])): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="Avatar" class="avatar-preview">
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-3">
                                        <label for="avatar_url" class="form-label">Or Avatar URL</label>
                                        <input type="url" class="form-control" id="avatar_url" name="avatar_url" value="<?php echo htmlspecialchars($user['avatar_url'] ?? ''); ?>" placeholder="https://...">
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Profile
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Account Info -->
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Account Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Username:</strong>
                                    <p><?php echo $user && !empty($user['username']) ? htmlspecialchars($user['username']) : '—'; ?></p>
                                </div>
                                <div class="mb-3">
                                    <strong>Email:</strong>
                                    <p><?php echo $user && !empty($user['email']) ? htmlspecialchars($user['email']) : '—'; ?></p>
                                </div>
                                <div class="mb-3">
                                    <strong>Member Since:</strong>
                                    <p><?php echo $user && !empty($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : '—'; ?></p>
                                </div>
                                <div class="mb-3">
                                    <strong>Last Updated:</strong>
                                    <p><?php echo $user && !empty($user['updated_at']) ? date('M d, Y H:i', strtotime($user['updated_at'])) : '—'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Change Password</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_password">
                                    
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <small class="text-muted">Minimum 6 characters</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>

                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key"></i> Update Password
                                    </button>
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
