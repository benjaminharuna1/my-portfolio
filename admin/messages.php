<?php
require '../config.php';
require '../includes/email-config.php';
require '../includes/admin-list-helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Messages';
$message = '';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM contact_messages WHERE id = $id");
    $message = '<div class="alert alert-success">Message deleted successfully.</div>';
    ErrorLogger::log("Message deleted: ID $id", 'INFO');
}

if (isset($_GET['mark_read'])) {
    $id = intval($_GET['mark_read']);
    $conn->query("UPDATE contact_messages SET is_read = 1 WHERE id = $id");
    $message = '<div class="alert alert-success">Message marked as read.</div>';
    ErrorLogger::log("Message marked as read: ID $id", 'INFO');
}

// Get all messages
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pagination = getPaginatedItems($conn, 'contact_messages', $page, 15, 'created_at DESC');
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
                    <h1>Contact Messages</h1>
                    <span class="badge bg-primary">Total: <?php echo $pagination['total_count']; ?></span>
                </div>

                <?php echo $message; ?>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>All Messages</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $columns = [
                            'name' => 'Name',
                            'email' => 'Email',
                            'message' => 'Message',
                            'created_at' => 'Date',
                            'is_read' => 'Status'
                        ];
                        
                        // Custom display for messages table
                        if (empty($pagination['items'])) {
                            echo '<div class="alert alert-info">No messages found.</div>';
                        } else {
                            ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Message</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th style="width: 150px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pagination['items'] as $msg): ?>
                                            <tr <?php echo !$msg['is_read'] ? 'class="table-light"' : ''; ?>>
                                                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                                <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($msg['message'], 0, 50)); ?>...</td>
                                                <td><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></td>
                                                <td>
                                                    <?php if ($msg['is_read']): ?>
                                                        <span class="badge bg-success">Read</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Unread</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <?php if (!$msg['is_read']): ?>
                                                            <a href="?mark_read=<?php echo $msg['id']; ?>" class="btn btn-info" title="Mark as Read">
                                                                <i class="fas fa-check"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        <a href="?delete=<?php echo $msg['id']; ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php displayPagination($pagination['current_page'], $pagination['total_pages'], SITE_URL . '/admin/messages.php'); ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
