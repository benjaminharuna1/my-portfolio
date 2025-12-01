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
                                                        <button type="button" class="btn btn-primary view-message-btn" title="View Full Message" data-bs-toggle="modal" data-bs-target="#messageModal" data-name="<?php echo htmlspecialchars($msg['name'], ENT_QUOTES); ?>" data-email="<?php echo htmlspecialchars($msg['email'], ENT_QUOTES); ?>" data-message="<?php echo htmlspecialchars($msg['message'], ENT_QUOTES); ?>" data-date="<?php echo htmlspecialchars($msg['created_at'], ENT_QUOTES); ?>" data-read="<?php echo $msg['is_read']; ?>">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
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

                <!-- Message View Modal -->
                <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="messageModalLabel">Message Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label"><strong>From:</strong></label>
                                    <p id="modalName" class="form-control-plaintext"></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Email:</strong></label>
                                    <p id="modalEmail" class="form-control-plaintext">
                                        <a id="modalEmailLink" href=""></a>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Date:</strong></label>
                                    <p id="modalDate" class="form-control-plaintext"></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Status:</strong></label>
                                    <p id="modalStatus" class="form-control-plaintext"></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Message:</strong></label>
                                    <div id="modalMessage" class="border p-3 rounded" style="background-color: #f9f9f9; min-height: 150px; white-space: pre-wrap; word-wrap: break-word;"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a id="replyButton" href="" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-reply"></i> Reply via Email
                                </a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.view-message-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                const message = this.getAttribute('data-message');
                const date = this.getAttribute('data-date');
                const isRead = this.getAttribute('data-read') === '1';
                
                // Populate modal with message data
                document.getElementById('modalName').textContent = name;
                document.getElementById('modalEmail').innerHTML = '<a href="mailto:' + email + '">' + email + '</a>';
                document.getElementById('modalEmailLink').href = 'mailto:' + email;
                document.getElementById('modalEmailLink').textContent = email;
                document.getElementById('modalDate').textContent = new Date(date).toLocaleString();
                document.getElementById('modalStatus').innerHTML = isRead ? '<span class="badge bg-success">Read</span>' : '<span class="badge bg-warning">Unread</span>';
                document.getElementById('modalMessage').textContent = message;
                document.getElementById('replyButton').href = 'mailto:' + email + '?subject=Re: Your Message';
            });
        });
    </script>
</body>
</html>
