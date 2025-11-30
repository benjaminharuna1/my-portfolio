<?php
/**
 * Database Migrations
 * Run migrations to update database schema
 */

class DatabaseMigrations {
    /**
     * Run all pending migrations
     */
    public static function runAll($conn) {
        self::createEmailSettingsTable($conn);
    }
    
    /**
     * Create email_settings table if it doesn't exist
     */
    private static function createEmailSettingsTable($conn) {
        $sql = "CREATE TABLE IF NOT EXISTS email_settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            smtp_host VARCHAR(255),
            smtp_port INT DEFAULT 587,
            smtp_username VARCHAR(255),
            smtp_password VARCHAR(255),
            from_email VARCHAR(255),
            from_name VARCHAR(255) DEFAULT 'Portfolio',
            admin_email VARCHAR(255),
            enable_notifications BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
        
        if ($conn->query($sql)) {
            ErrorLogger::log("Email settings table created/verified", 'INFO');
            return true;
        } else {
            ErrorLogger::log("Failed to create email settings table: " . $conn->error, 'ERROR');
            return false;
        }
    }
}
?>
