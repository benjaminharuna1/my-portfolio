<?php
/**
 * Error Handler and Logger
 * Captures all errors, warnings, and exceptions and logs them to a file
 */

class ErrorLogger {
    private static $log_file = null;
    private static $log_dir = null;

    public static function init() {
        // Set up log directory
        self::$log_dir = dirname(__DIR__) . '/logs';
        
        // Create logs directory if it doesn't exist
        if (!is_dir(self::$log_dir)) {
            mkdir(self::$log_dir, 0755, true);
        }

        // Set log file path with date
        $date = date('Y-m-d');
        self::$log_file = self::$log_dir . '/error_' . $date . '.log';

        // Set error handlers
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Handle PHP errors
     */
    public static function handleError($errno, $errstr, $errfile, $errline) {
        $error_types = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_PARSE => 'PARSE ERROR',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE ERROR',
            E_CORE_WARNING => 'CORE WARNING',
            E_COMPILE_ERROR => 'COMPILE ERROR',
            E_COMPILE_WARNING => 'COMPILE WARNING',
            E_USER_ERROR => 'USER ERROR',
            E_USER_WARNING => 'USER WARNING',
            E_USER_NOTICE => 'USER NOTICE',
            E_STRICT => 'STRICT',
            E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR',
            E_DEPRECATED => 'DEPRECATED',
            E_USER_DEPRECATED => 'USER DEPRECATED',
        ];

        $error_type = isset($error_types[$errno]) ? $error_types[$errno] : 'UNKNOWN';
        
        $log_message = self::formatLogMessage(
            $error_type,
            $errstr,
            $errfile,
            $errline
        );

        self::writeLog($log_message);

        // Don't execute PHP internal error handler
        return true;
    }

    /**
     * Handle uncaught exceptions
     */
    public static function handleException($exception) {
        $log_message = self::formatLogMessage(
            'EXCEPTION',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        self::writeLog($log_message);
    }

    /**
     * Handle fatal errors at shutdown
     */
    public static function handleShutdown() {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $log_message = self::formatLogMessage(
                'FATAL ERROR',
                $error['message'],
                $error['file'],
                $error['line']
            );

            self::writeLog($log_message);
        }
    }

    /**
     * Format log message
     */
    private static function formatLogMessage($type, $message, $file, $line, $trace = null) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'N/A';
        $uri = $_SERVER['REQUEST_URI'] ?? 'N/A';

        $log = "[{$timestamp}] [{$type}] IP: {$ip} | Method: {$method} | URI: {$uri}\n";
        $log .= "Message: {$message}\n";
        $log .= "File: {$file} (Line: {$line})\n";
        
        if ($trace) {
            $log .= "Stack Trace:\n{$trace}\n";
        }

        $log .= str_repeat("-", 80) . "\n";

        return $log;
    }

    /**
     * Write log to file
     */
    private static function writeLog($message) {
        if (self::$log_file && is_writable(dirname(self::$log_file))) {
            file_put_contents(self::$log_file, $message, FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Log custom messages
     */
    public static function log($message, $type = 'INFO') {
        $log_message = self::formatLogMessage($type, $message, __FILE__, __LINE__);
        self::writeLog($log_message);
    }

    /**
     * Get log file path
     */
    public static function getLogFile() {
        return self::$log_file;
    }

    /**
     * Get all logs for today
     */
    public static function getTodayLogs() {
        if (self::$log_file && file_exists(self::$log_file)) {
            return file_get_contents(self::$log_file);
        }
        return '';
    }

    /**
     * Get logs for a specific date
     */
    public static function getLogsByDate($date) {
        $log_file = self::$log_dir . '/error_' . $date . '.log';
        if (file_exists($log_file)) {
            return file_get_contents($log_file);
        }
        return '';
    }

    /**
     * Clear today's logs
     */
    public static function clearTodayLogs() {
        if (self::$log_file && file_exists(self::$log_file)) {
            unlink(self::$log_file);
            return true;
        }
        return false;
    }

    /**
     * Get list of all log files
     */
    public static function getLogFiles() {
        $logs = [];
        if (is_dir(self::$log_dir)) {
            $files = scandir(self::$log_dir, SCANDIR_SORT_DESCENDING);
            foreach ($files as $file) {
                if (strpos($file, 'error_') === 0 && strpos($file, '.log') !== false) {
                    $logs[] = [
                        'filename' => $file,
                        'path' => self::$log_dir . '/' . $file,
                        'size' => filesize(self::$log_dir . '/' . $file),
                        'date' => filemtime(self::$log_dir . '/' . $file)
                    ];
                }
            }
        }
        return $logs;
    }
}

// Initialize error handler
ErrorLogger::init();
?>
