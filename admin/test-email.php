<?php
require '../config.php';
require '../includes/email-config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'Test Email System';
$test_result = '';

// Load email config
EmailConfig::load($conn);

$email_config = [
    'smtp_host' => EmailConfig::get('smtp_host'),
    'smtp_port' => EmailConfig::get('smtp_port'),
    'from_email' => EmailConfig::get('from_email'),
    'from_name' => EmailConfig::get('from_name'),
    'admin_email' => EmailConfig::get('admin_email'),
    'enable_notifications' => EmailConfig::get('enable_notifications')
];

// Test email sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'test_contact_email') {
        $test_email = $conn->real_escape_string($_POST['test_email']);
        
        // Test contact confirmation email
        $contact_data = EmailTemplate::contactConfirmation('Test User', $test_email);
        $result = EmailConfig::sendEmail($test_email, $contact_data['subject'], $contact_data['body'], true);
        
        if ($result) {
            $test_result = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> <strong>Success!</strong> Test contact confirmation email sent to ' . htmlspecialchars($test_email) . '</div>';
        } else {
            $test_result = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <strong>Failed!</strong> Could not send test email. Check your SMTP settings.</div>';
        }
    } elseif ($_POST['action'] === 'test_review_email') {
        $test_email = $conn->real_escape_string($_POST['test_email']);
        
        // Test review confirmation email
        $review_data = EmailTemplate::reviewConfirmation('Test Reviewer', 'Test Project');
        $result = EmailConfig::sendEmail($test_email, $review_data['subject'], $review_data['body'], true);
        
        if ($result) {
            $test_result = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> <strong>Success!</strong> Test review confirmation email sent to ' . htmlspecialchars($test_email) . '</div>';
        } else {
            $test_result = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <strong>Failed!</strong> Could not send test email. Check your SMTP settings.</div>';
        }
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
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/admin-sidebar.php'; ?>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                    <h1>Test Email System</h1>
                </div>

                <?php echo $test_result; ?>

                <div class="row mt-4">
                    <div class="col-md-8">
                        <!-- Email Configuration Status -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Email Configuration Status</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>SMTP Host:</strong></td>
                                        <td>
                                            <?php if (!empty($email_config['smtp_host'])): ?>
                                                <span class="badge bg-success"><?php echo htmlspecialchars($email_config['smtp_host']); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Not configured</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>SMTP Port:</strong></td>
                                        <td><?php echo $email_config['smtp_port']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>From Email:</strong></td>
                                        <td>
                                            <?php if (!empty($email_config['from_email'])): ?>
                                                <span class="badge bg-success"><?php echo htmlspecialchars($email_config['from_email']); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Not configured</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>From Name:</strong></td>
                                        <td><?php echo htmlspecialchars($email_config['from_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Admin Email:</strong></td>
                                        <td>
                                            <?php if (!empty($email_config['admin_email'])): ?>
                                                <span class="badge bg-success"><?php echo htmlspecialchars($email_config['admin_email']); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Not configured</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Notifications Enabled:</strong></td>
                                        <td>
                                            <?php if ($email_config['enable_notifications']): ?>
                                                <span class="badge bg-success">Yes</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">No</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>

                                <?php if (empty($email_config['smtp_host']) || empty($email_config['from_email'])): ?>
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> Email is not fully configured. 
                                    <a href="<?php echo SITE_URL; ?>/admin/settings.php">Configure email settings</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Test Emails -->
                        <div class="card">
                            <div class="card-header">
                                <h5>Send Test Emails</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Send test emails to verify your email configuration is working correctly.
                                </div>

                                <!-- Test Contact Email -->
                                <div class="mb-4">
                                    <h6>Test Contact Confirmation Email</h6>
                                    <p class="text-muted">This is the email sent to users when they submit a contact message.</p>
                                    <form method="POST" class="row g-2">
                                        <input type="hidden" name="action" value="test_contact_email">
                                        <div class="col-md-8">
                                            <input type="email" class="form-control" name="test_email" placeholder="Enter your email address" required>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-paper-plane"></i> Send Test
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <hr>

                                <!-- Test Review Email -->
                                <div class="mb-4">
                                    <h6>Test Review Confirmation Email</h6>
                                    <p class="text-muted">This is the email sent to users when they submit a review.</p>
                                    <form method="POST" class="row g-2">
                                        <input type="hidden" name="action" value="test_review_email">
                                        <div class="col-md-8">
                                            <input type="email" class="form-control" name="test_email" placeholder="Enter your email address" required>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-paper-plane"></i> Send Test
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Email Flow Documentation -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Email Flow Documentation</h5>
                            </div>
                            <div class="card-body">
                                <h6>Contact Form Emails</h6>
                                <ul>
                                    <li><strong>User receives:</strong> Contact confirmation email thanking them for their message</li>
                                    <li><strong>Admin receives:</strong> Notification email with the full message content</li>
                                    <li><strong>Template:</strong> contact-confirmation.html & contact-notification-admin.html</li>
                                </ul>

                                <hr>

                                <h6>Review Submission Emails</h6>
                                <ul>
                                    <li><strong>Reviewer receives:</strong> Thank you email for submitting their review</li>
                                    <li><strong>Admin receives:</strong> Notification email with review details</li>
                                    <li><strong>Template:</strong> review-confirmation.html & review-notification-admin.html</li>
                                </ul>

                                <hr>

                                <h6>Email Configuration</h6>
                                <ul>
                                    <li>SMTP settings are configured in <strong>Settings > Email Configuration</strong></li>
                                    <li>Email templates are located in <strong>/templates/email/</strong></li>
                                    <li>Email logs are recorded in the system logs</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Quick Links</h5>
                            </div>
                            <div class="card-body">
                                <a href="<?php echo SITE_URL; ?>/admin/settings.php" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="fas fa-cog"></i> Email Settings
                                </a>
                                <a href="<?php echo SITE_URL; ?>/admin/messages.php" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="fas fa-envelope"></i> View Messages
                                </a>
                                <a href="<?php echo SITE_URL; ?>/admin/portfolio.php" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="fas fa-star"></i> View Reviews
                                </a>
                                <a href="<?php echo SITE_URL; ?>/admin/logs.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-file-alt"></i> View Logs
                                </a>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>Email Providers</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">Popular SMTP providers:</p>
                                <ul class="small">
                                    <li><strong>Gmail:</strong> smtp.gmail.com:587</li>
                                    <li><strong>Outlook:</strong> smtp-mail.outlook.com:587</li>
                                    <li><strong>SendGrid:</strong> smtp.sendgrid.net:587</li>
                                    <li><strong>Mailgun:</strong> smtp.mailgun.org:587</li>
                                </ul>
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
