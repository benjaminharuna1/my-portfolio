<?php
require '../config.php';
require '../includes/upload.php';
require '../includes/image-helper.php';

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
        $upload = uploadImage($_FILES['avatar']);
        if ($upload['success']) {
            // Delete old avatar if exists
            if (!empty($user['avatar_filename'])) {
                deleteImage($user['avatar_filename']);
            }
            $avatar_filename = $upload['filename'];
            $avatar_url = $upload['url'];
        } else {
            $message = '<div class="alert alert-danger">' . $upload['message'] . '</div>';
        }
    }
    
    if ($avatar_filename) {
        $conn->query("UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', phone='$phone', bio='$bio', avatar_url='$avatar_url', avatar_filename='$avatar_filename' WHERE id=$user_id");
        ErrorLogger::log("User avatar updated: $user_id", 'INFO');
    } else {
        $conn->query("UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', phone='$phone', bio='$bio', avatar_url='$avatar_url' WHERE id=$user_id");
        ErrorLogger::log("User profile updated: $user_id", 'INFO');
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
    <?php include '../includes/admin-head.php'; ?>
    <style>
        .profile-avatar { 
            width: 150px; 
            height: 150px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 3px solid #667eea;
            display: block;
            margin: 0 auto;
        }
        .avatar-preview { 
            width: 100px; 
            height: 100px; 
            border-radius: 50%; 
            object-fit: cover; 
            margin-top: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .avatar-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #e9ecef;
            margin: 0 auto;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/admin-sidebar.php'; ?>

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
                                        <small class="text-muted">Max 5MB. Recommended: 300x300px (will be cropped to circle)</small>
                                        <?php if ($user && !empty($user['avatar_url'])): ?>
                                            <div class="mt-3 text-center">
                                                <div class="avatar-container">
                                                    <img src="<?php echo getImageUrl($user['avatar_url']); ?>" alt="Avatar" class="avatar-preview">
                                                </div>
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
                            <div class="card-body text-center">
                                <?php if ($user && !empty($user['avatar_url'])): ?>
                                    <div class="avatar-container mb-3">
                                        <img src="<?php echo getImageUrl($user['avatar_url']); ?>" alt="Profile" class="profile-avatar">
                                    </div>
                                <?php else: ?>
                                    <div class="avatar-container mb-3">
                                        <i class="fas fa-user fa-4x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <h5><?php echo ($user && !empty($user['first_name']) ? htmlspecialchars($user['first_name']) : '') . ' ' . ($user && !empty($user['last_name']) ? htmlspecialchars($user['last_name']) : ''); ?></h5>
                                <p class="text-muted"><?php echo $user && !empty($user['email']) ? htmlspecialchars($user['email']) : 'No email'; ?></p>
                                
                                <hr>
                                
                                <div class="text-start">
                                    <div class="mb-2">
                                        <strong>Username:</strong>
                                        <p><?php echo $user && !empty($user['username']) ? htmlspecialchars($user['username']) : '—'; ?></p>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Member Since:</strong>
                                        <p><?php echo $user && !empty($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : '—'; ?></p>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Last Updated:</strong>
                                        <p><?php echo $user && !empty($user['updated_at']) ? date('M d, Y H:i', strtotime($user['updated_at'])) : '—'; ?></p>
                                    </div>
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
