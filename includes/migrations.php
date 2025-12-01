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
        self::convertImageUrlsToRelativePaths($conn);
        self::createCustomIconsTable($conn);
    }
    
    /**
     * Create email_settings table if it doesn't exist
     */
    private static function createEmailSettingsTable($conn) {
        // Check if table already exists
        $result = $conn->query("SHOW TABLES LIKE 'email_settings'");
        if ($result && $result->num_rows > 0) {
            // Table exists, no need to log
            return true;
        }
        
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
        
        if ($conn->query($sql)) {
            ErrorLogger::log("Email settings table created", 'INFO');
            return true;
        } else {
            ErrorLogger::log("Failed to create email settings table: " . $conn->error, 'ERROR');
            return false;
        }
    }

    /**
     * Convert absolute image URLs to relative paths for domain portability
     * This allows images to work across different domains without re-uploading
     */
    private static function convertImageUrlsToRelativePaths($conn) {
        // Check if migration has already been run
        $check = $conn->query("SELECT COUNT(*) as count FROM portfolio_items WHERE featured_image_url LIKE 'http://%' OR featured_image_url LIKE 'https://%'");
        $result = $check->fetch_assoc();
        
        if ($result['count'] == 0) {
            // No absolute URLs found, migration already done or not needed
            return true;
        }

        // Tables and columns to update
        $updates = [
            'portfolio_items' => ['featured_image_url'],
            'portfolio_images' => ['image_url'],
            'about' => ['image_url'],
            'testimonials' => ['client_image_url'],
            'website_settings' => ['logo_url', 'favicon_url']
        ];

        foreach ($updates as $table => $columns) {
            foreach ($columns as $column) {
                // Extract relative path from absolute URLs
                $sql = "UPDATE `$table` 
                        SET `$column` = SUBSTRING(`$column`, POSITION('/uploads/' IN `$column`))
                        WHERE (`$column` LIKE 'http://%' OR `$column` LIKE 'https://%')
                        AND `$column` LIKE '%/uploads/%'";
                
                if ($conn->query($sql)) {
                    $affected = $conn->affected_rows;
                    if ($affected > 0) {
                        ErrorLogger::log("Converted $affected image URLs to relative paths in $table.$column", 'INFO');
                    }
                } else {
                    ErrorLogger::log("Failed to convert URLs in $table.$column: " . $conn->error, 'WARNING');
                }
            }
        }

        return true;
    }

    /**
     * Create custom_icons table for SVG icon management
     */
    private static function createCustomIconsTable($conn) {
        // Check if table already exists
        $result = $conn->query("SHOW TABLES LIKE 'custom_icons'");
        if ($result && $result->num_rows > 0) {
            // Table exists, no need to log
            return true;
        }

        $sql = "CREATE TABLE IF NOT EXISTS custom_icons (
            id INT NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL UNIQUE,
            slug VARCHAR(100) NOT NULL UNIQUE,
            svg_content LONGTEXT NOT NULL,
            svg_filename VARCHAR(255),
            category VARCHAR(100),
            color VARCHAR(7) DEFAULT '#000000',
            size VARCHAR(20) DEFAULT '24',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY category (category)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";

        if ($conn->query($sql)) {
            ErrorLogger::log("Custom icons table created", 'INFO');
            return true;
        } else {
            ErrorLogger::log("Failed to create custom icons table: " . $conn->error, 'ERROR');
            return false;
        }
    }
}
?>
