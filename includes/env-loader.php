<?php
/**
 * Environment Variable Loader
 * Loads variables from .env file
 */

class EnvLoader {
    private static $env_file = null;
    private static $variables = [];
    private static $loaded = false;

    /**
     * Load environment variables from .env file
     */
    public static function load($env_file = null) {
        if (self::$loaded) {
            return;
        }

        // Determine .env file path
        if ($env_file === null) {
            $env_file = dirname(__DIR__) . '/.env';
        }

        self::$env_file = $env_file;

        // Check if .env file exists
        if (!file_exists($env_file)) {
            throw new Exception(".env file not found at: $env_file");
        }

        // Read and parse .env file
        self::parseEnvFile($env_file);
        self::$loaded = true;
    }

    /**
     * Parse .env file and load variables
     */
    private static function parseEnvFile($file) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse key=value
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes if present
                if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                    (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                    $value = substr($value, 1, -1);
                }

                // Set environment variable
                self::$variables[$key] = $value;
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }

    /**
     * Get environment variable
     */
    public static function get($key, $default = null) {
        if (isset(self::$variables[$key])) {
            return self::$variables[$key];
        }

        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return $default;
    }

    /**
     * Get all environment variables
     */
    public static function all() {
        return self::$variables;
    }

    /**
     * Check if variable exists
     */
    public static function has($key) {
        return isset(self::$variables[$key]) || getenv($key) !== false;
    }

    /**
     * Get environment variable as boolean
     */
    public static function getBoolean($key, $default = false) {
        $value = self::get($key, $default);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get environment variable as integer
     */
    public static function getInteger($key, $default = 0) {
        $value = self::get($key, $default);
        return intval($value);
    }

    /**
     * Get environment variable as array (comma-separated)
     */
    public static function getArray($key, $default = []) {
        $value = self::get($key, null);
        if ($value === null) {
            return $default;
        }
        return array_map('trim', explode(',', $value));
    }

    /**
     * Set environment variable
     */
    public static function set($key, $value) {
        self::$variables[$key] = $value;
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }

    /**
     * Get .env file path
     */
    public static function getEnvFile() {
        return self::$env_file;
    }

    /**
     * Check if environment is loaded
     */
    public static function isLoaded() {
        return self::$loaded;
    }
}
?>
