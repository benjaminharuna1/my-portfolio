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
     * Load template file and replace variables
     */
    private static function loadTemplate($templateName, $variables = []) {
        $templatePath = dirname(__DIR__) . '/templates/email/' . $templateName . '.html';
        
        if (!file_exists($templatePath)) {
            ErrorLogger::log("Email template not found: $templatePath", 'WARNING');
            return '';
        }
        
        $content = file_get_contents($templatePath);
        
        // Replace variables
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }
    
    /**
     * Review notification email for admin
     */
    public static function reviewNotificationAdmin($reviewerName, $rating, $reviewText, $portfolioTitle) {
        $subject = "New Review Received - $portfolioTitle";
        
        $ratingStars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
        $ratingText = ['Poor', 'Fair', 'Average', 'Good', 'Excellent'][$rating - 1] ?? 'Excellent';
        
        $variables = [
            'PORTFOLIO_TITLE' => htmlspecialchars($portfolioTitle),
            'REVIEWER_NAME' => htmlspecialchars($reviewerName),
            'REVIEWER_EMAIL' => htmlspecialchars($reviewerName), // Placeholder
            'RATING_STARS' => $ratingStars,
            'RATING_TEXT' => $ratingText . ' (' . $rating . '/5)',
            'REVIEW_TEXT' => nl2br(htmlspecialchars($reviewText)),
            'ADMIN_LINK' => SITE_URL . '/admin/portfolio.php',
            'SITE_NAME' => SITE_NAME,
            'CURRENT_YEAR' => date('Y')
        ];
        
        $body = self::loadTemplate('review-notification-admin', $variables);
        
        return ['subject' => $subject, 'body' => $body];
    }
    
    /**
     * Review confirmation email for reviewer
     */
    public static function reviewConfirmation($reviewerName, $portfolioTitle) {
        $subject = "Thank You for Your Review";
        
        $variables = [
            'REVIEWER_NAME' => htmlspecialchars($reviewerName),
            'PORTFOLIO_TITLE' => htmlspecialchars($portfolioTitle),
            'SOCIAL_LINKS' => self::getSocialLinks(),
            'SITE_NAME' => SITE_NAME,
            'CURRENT_YEAR' => date('Y')
        ];
        
        $body = self::loadTemplate('review-confirmation', $variables);
        
        return ['subject' => $subject, 'body' => $body];
    }
    
    /**
     * Contact message notification for admin
     */
    public static function contactNotificationAdmin($senderName, $senderEmail, $message) {
        $subject = "New Contact Message from $senderName";
        
        $variables = [
            'SENDER_NAME' => htmlspecialchars($senderName),
            'SENDER_EMAIL' => htmlspecialchars($senderEmail),
            'MESSAGE' => nl2br(htmlspecialchars($message)),
            'ADMIN_LINK' => SITE_URL . '/admin/messages.php',
            'SITE_NAME' => SITE_NAME,
            'CURRENT_YEAR' => date('Y')
        ];
        
        $body = self::loadTemplate('contact-notification-admin', $variables);
        
        return ['subject' => $subject, 'body' => $body];
    }
    
    /**
     * Contact confirmation email for sender
     */
    public static function contactConfirmation($senderName, $senderEmail = '') {
        $subject = "We Received Your Message";
        
        // Get admin email from settings
        global $conn;
        $about = $conn->query("SELECT email FROM about LIMIT 1")->fetch_assoc();
        $contactEmail = $about['email'] ?? 'contact@example.com';
        
        $variables = [
            'SENDER_NAME' => htmlspecialchars($senderName),
            'CONTACT_EMAIL' => htmlspecialchars($contactEmail),
            'SOCIAL_LINKS' => self::getSocialLinks(),
            'SITE_NAME' => SITE_NAME,
            'CURRENT_YEAR' => date('Y')
        ];
        
        $body = self::loadTemplate('contact-confirmation', $variables);
        
        return ['subject' => $subject, 'body' => $body];
    }
    
    /**
     * Generate social links HTML
     */
    private static function getSocialLinks() {
        global $conn;
        $result = $conn->query("SELECT url, platform FROM social_links LIMIT 4");
        
        $links = '';
        while ($social = $result->fetch_assoc()) {
            $links .= '<a href="' . htmlspecialchars($social['url']) . '">' . htmlspecialchars($social['platform']) . '</a>';
        }
        
        return $links;
    }
}
?>
