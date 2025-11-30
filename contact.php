<?php
require 'config.php';
require 'includes/email-config.php';
$page_title = 'Contact';
$about = $conn->query("SELECT * FROM about LIMIT 1")->fetch_assoc();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $msg = $conn->real_escape_string($_POST['message']);
    
    if ($name && $email && $msg) {
        $conn->query("INSERT INTO contact_messages (name, email, message) VALUES ('$name', '$email', '$msg')");
        
        // Load email config
        EmailConfig::load($conn);
        
        // Send notification to admin
        if (EmailConfig::get('enable_notifications') && EmailConfig::get('admin_email')) {
            $admin_email_data = EmailTemplate::contactNotificationAdmin($name, $email, $msg);
            EmailConfig::sendEmail(
                EmailConfig::get('admin_email'),
                $admin_email_data['subject'],
                $admin_email_data['body'],
                true
            );
        }
        
        // Send confirmation to user
        $user_email_data = EmailTemplate::contactConfirmation($name);
        EmailConfig::sendEmail(
            $email,
            $user_email_data['subject'],
            $user_email_data['body'],
            true
        );
        
        $message = '<div class="alert alert-success">Thank you! Your message has been sent. We will get back to you within 24 hours.</div>';
    } else {
        $message = '<div class="alert alert-danger">Please fill in all fields.</div>';
    }
}
?>
<?php include 'includes/header.php'; ?>

<main>
    <section class="contact-section py-5">
        <div class="container">
            <h1 class="text-center mb-5">Contact Me</h1>
            
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="contact-info">
                        <div class="contact-item mb-4">
                            <i class="fas fa-phone fa-2x text-primary mb-3"></i>
                            <h5><?php echo htmlspecialchars($about['phone']); ?></h5>
                            <p class="text-muted">Monday - Friday from 7am - 5pm</p>
                        </div>
                        <div class="contact-item mb-4">
                            <i class="fas fa-map-marker-alt fa-2x text-primary mb-3"></i>
                            <h5><?php echo htmlspecialchars($about['location']); ?></h5>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope fa-2x text-primary mb-3"></i>
                            <h5><a href="mailto:<?php echo htmlspecialchars($about['email']); ?>"><?php echo htmlspecialchars($about['email']); ?></a></h5>
                            <p class="text-muted">Contact me every time!</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <?php echo $message; ?>
                    <form method="POST" class="contact-form">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
