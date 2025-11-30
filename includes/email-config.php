<?php
/**
 * Email Configuration and Notification System
 */

class EmailConfig {
    private static $config = null;
    
    /**
     * Load email configuration from database
     */
    public static function load($conn) {
        if (self::$config === null) {
            $result = $conn->query("SELECT * FROM email_settings LIMIT 1");
            if ($result && $result->num_rows > 0) {
                self::$config = $result->fetch_assoc();
            } else {
                // Return default config if not found
                self::$config = [
                    'smtp_host' => '',
                    'smtp_port' => 587,
                    'smtp_username' => '',
                    'smtp_password' => '',
                    'from_email' => '',
                    'from_name' => 'Portfolio',
                    'admin_email' => '',
                    'enable_notifications' => 1
                ];
            }
        }
        return self::$config;
    }
    
    /**
     * Get a config value
     */
    public static function get($key, $default = null) {
        if (self::$config === null) {
            return $default;
        }
        return self::$config[$key] ?? $default;
    }
    
    /**
     * Send email using PHPMailer or mail()
     */
    public static function sendEmail($to, $subject, $body, $isHtml = true) {
        $config = self::$config;
        
        if (empty($config['from_email'])) {
            ErrorLogger::log("Email not sent: from_email not configured", 'WARNING');
            return false;
        }
        
        // Use PHPMailer if available, otherwise use mail()
        if (self::usePhpMailer($config)) {
            return self::sendViaPhpMailer($to, $subject, $body, $isHtml, $config);
        } else {
            return self::sendViaMail($to, $subject, $body, $isHtml, $config);
        }
    }
    
    /**
     * Check if PHPMailer should be used
     */
    private static function usePhpMailer($config) {
        return !empty($config['smtp_host']) && !empty($config['smtp_username']);
    }
    
    /**
     * Send email via PHPMailer
     */
    private static function sendViaPhpMailer($to, $subject, $body, $isHtml, $config) {
        try {
            // Check if PHPMailer is available
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                return self::sendViaMail($to, $subject, $body, $isHtml, $config);
            }
            
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['smtp_username'];
            $mail->Password = $config['smtp_password'];
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['smtp_port'];
            
            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->isHTML($isHtml);
            $mail->Body = $body;
            
            $mail->send();
            ErrorLogger::log("Email sent to: $to", 'INFO');
            return true;
        } catch (Exception $e) {
            ErrorLogger::log("PHPMailer error: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
    
    /**
     * Send email via PHP mail() function
     */
    private static function sendViaMail($to, $subject, $body, $isHtml, $config) {
        $headers = "From: " . $config['from_email'] . "\r\n";
        $headers .= "Reply-To: " . $config['from_email'] . "\r\n";
        
        if ($isHtml) {
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        }
        
        $result = mail($to, $subject, $body, $headers);
        
        if ($result) {
            ErrorLogger::log("Email sent to: $to", 'INFO');
        } else {
            ErrorLogger::log("Failed to send email to: $to", 'ERROR');
        }
        
        return $result;
    }
}

/**
 * Email Templates
 */
class EmailTemplate {
    /**
     * Review notification email for admin
     */
    public static function reviewNotificationAdmin($reviewerName, $rating, $reviewText, $portfolioTitle) {
        $subject = "New Review Received - $portfolioTitle";
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #667eea; color: white; padding: 20px; border-radius: 5px; }
                .content { padding: 20px; background: #f9f9f9; margin: 20px 0; border-radius: 5px; }
                .rating { color: #ffc107; font-size: 18px; }
                .footer { text-align: center; color: #999; font-size: 12px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>New Review Received</h2>
                </div>
                <div class='content'>
                    <p><strong>Portfolio Item:</strong> $portfolioTitle</p>
                    <p><strong>Reviewer:</strong> $reviewerName</p>
                    <p><strong>Rating:</strong> <span class='rating'>" . str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) . "</span></p>
                    <p><strong>Review:</strong></p>
                    <p>$reviewText</p>
                </div>
                <div class='footer'>
                    <p>This is an automated notification from your portfolio website.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return ['subject' => $subject, 'body' => $body];
    }
    
    /**
     * Review confirmation email for reviewer
     */
    public static function reviewConfirmation($reviewerName, $portfolioTitle) {
        $subject = "Thank You for Your Review";
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #667eea; color: white; padding: 20px; border-radius: 5px; }
                .content { padding: 20px; background: #f9f9f9; margin: 20px 0; border-radius: 5px; }
                .footer { text-align: center; color: #999; font-size: 12px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Thank You for Your Review</h2>
                </div>
                <div class='content'>
                    <p>Hi $reviewerName,</p>
                    <p>Thank you for taking the time to review <strong>$portfolioTitle</strong>. Your feedback is valuable and helps us improve.</p>
                    <p>We appreciate your support!</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message from our portfolio website.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return ['subject' => $subject, 'body' => $body];
    }
    
    /**
     * Contact message notification for admin
     */
    public static function contactNotificationAdmin($senderName, $senderEmail, $message) {
        $subject = "New Contact Message from $senderName";
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #667eea; color: white; padding: 20px; border-radius: 5px; }
                .content { padding: 20px; background: #f9f9f9; margin: 20px 0; border-radius: 5px; }
                .footer { text-align: center; color: #999; font-size: 12px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>New Contact Message</h2>
                </div>
                <div class='content'>
                    <p><strong>From:</strong> $senderName</p>
                    <p><strong>Email:</strong> <a href='mailto:$senderEmail'>$senderEmail</a></p>
                    <p><strong>Message:</strong></p>
                    <p>" . nl2br(htmlspecialchars($message)) . "</p>
                </div>
                <div class='footer'>
                    <p>This is an automated notification from your portfolio website.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return ['subject' => $subject, 'body' => $body];
    }
    
    /**
     * Contact confirmation email for sender
     */
    public static function contactConfirmation($senderName) {
        $subject = "We Received Your Message";
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #667eea; color: white; padding: 20px; border-radius: 5px; }
                .content { padding: 20px; background: #f9f9f9; margin: 20px 0; border-radius: 5px; }
                .footer { text-align: center; color: #999; font-size: 12px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Message Received</h2>
                </div>
                <div class='content'>
                    <p>Hi $senderName,</p>
                    <p>Thank you for reaching out! We have received your message and will get back to you within 24 hours.</p>
                    <p>We appreciate your interest and look forward to connecting with you.</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message from our portfolio website.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return ['subject' => $subject, 'body' => $body];
    }
}
?>
