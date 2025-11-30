<?php
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Manage Social Links';
$message = '';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM social_links WHERE id = $id");
    $message = '<div class="alert alert-success">Social link deleted.</div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $platform = $conn->real_escape_string($_POST['platform']);
    $url = $conn->real_escape_string($_POST['url']);
    $icon = $conn->real_escape_string($_POST['icon']);
    
    if (isset($_POST['id']) && $_POST['id']) {
        $id = intval($_POST['id']);
        $conn->query("UPDATE social_links SET platform='$platform', url='$url', icon='$icon' WHERE id=$id");
        $message = '<div class="alert alert-success">Social link updated.</div>';
    } else {
        $conn->query("INSERT INTO social_links (platform, url, icon) VALUES ('$platform', '$url', '$icon')");
        $message = '<div class="alert alert-success">Social link added.</div>';
    }
}

$edit_item = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_item = $conn->query("SELECT * FROM social_links WHERE id = $id")->fetch_assoc();
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
            <?php include '../includes/admin-sidebar.php'; ?>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                    <h1>Manage Social Links</h1>
                </div>

                <?php echo $message; ?>

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
                                        <label for="icon" class="form-label">Font Awesome Icon (e.g., fab fa-linkedin-in)</label>
                                        <input type="text" class="form-control" id="icon" name="icon" value="<?php echo $edit_item ? htmlspecialchars($edit_item['icon']) : ''; ?>" placeholder="fab fa-linkedin-in">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
