<?php
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$page_title = 'System Logs';
$message = '';

// Handle log clearing
if (isset($_GET['clear'])) {
    $date = $_GET['clear'];
    $log_file = dirname(__DIR__) . '/logs/error_' . $date . '.log';
    
    if (file_exists($log_file) && unlink($log_file)) {
        $message = '<div class="alert alert-success">Log file cleared successfully.</div>';
    } else {
        $message = '<div class="alert alert-danger">Failed to clear log file.</div>';
    }
}

// Get all log files
$log_files = ErrorLogger::getLogFiles();

// Get selected log content
$selected_log = null;
$log_content = '';

if (isset($_GET['view'])) {
    $date = $_GET['view'];
    $log_content = ErrorLogger::getLogsByDate($date);
    $selected_log = $date;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../includes/admin-head.php'; ?>
    <style>
        .log-viewer { background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 5px; font-family: 'Courier New', monospace; font-size: 12px; max-height: 600px; overflow-y: auto; line-height: 1.5; }
        .log-error { color: #f48771; }
        .log-warning { color: #dcdcaa; }
        .log-info { color: #569cd6; }
        .log-success { color: #6a9955; }
        .log-file-item { padding: 10px; border-left: 3px solid #667eea; margin-bottom: 10px; background: #f8f9fa; border-radius: 3px; }
        .log-file-item strong { color: #667eea; }
        .log-file-size { font-size: 0.85rem; color: #666; }
        .log-file-date { font-size: 0.85rem; color: #999; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/admin-sidebar.php'; ?>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                    <h1>System Logs</h1>
                </div>

                <?php echo $message; ?>

                <div class="row mt-4">
                    <!-- Log Files List -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Log Files</h5>
                            </div>
                            <div class="card-body" style="max-height: 700px; overflow-y: auto;">
                                <?php if (empty($log_files)): ?>
                                    <p class="text-muted">No log files found.</p>
                                <?php else: ?>
                                    <?php foreach ($log_files as $log): ?>
                                    <div class="log-file-item">
                                        <strong><?php echo htmlspecialchars($log['filename']); ?></strong>
                                        <div class="log-file-date">
                                            <i class="fas fa-calendar"></i> <?php echo date('M d, Y H:i', $log['date']); ?>
                                        </div>
                                        <div class="log-file-size">
                                            <i class="fas fa-database"></i> <?php echo number_format($log['size'] / 1024, 2); ?> KB
                                        </div>
                                        <div class="mt-2">
                                            <a href="?view=<?php echo substr($log['filename'], 6, 10); ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="?clear=<?php echo substr($log['filename'], 6, 10); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Clear this log file?')">
                                                <i class="fas fa-trash"></i> Clear
                                            </a>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Log Content -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>
                                    <?php if ($selected_log): ?>
                                        Log Content - <?php echo htmlspecialchars($selected_log); ?>
                                    <?php else: ?>
                                        Select a log file to view
                                    <?php endif; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($selected_log && !empty($log_content)): ?>
                                    <div class="log-viewer">
                                        <?php
                                        $lines = explode("\n", $log_content);
                                        foreach ($lines as $line) {
                                            if (empty($line)) continue;
                                            
                                            $class = 'log-info';
                                            if (strpos($line, '[ERROR]') !== false || strpos($line, '[FATAL ERROR]') !== false) {
                                                $class = 'log-error';
                                            } elseif (strpos($line, '[WARNING]') !== false) {
                                                $class = 'log-warning';
                                            } elseif (strpos($line, '[EXCEPTION]') !== false) {
                                                $class = 'log-error';
                                            }
                                            
                                            echo '<div class="' . $class . '">' . htmlspecialchars($line) . '</div>';
                                        }
                                        ?>
                                    </div>
                                    <div class="mt-3">
                                        <a href="?view=<?php echo htmlspecialchars($selected_log); ?>&download=1" class="btn btn-success">
                                            <i class="fas fa-download"></i> Download Log
                                        </a>
                                    </div>
                                <?php elseif ($selected_log): ?>
                                    <p class="text-muted">Log file is empty or not found.</p>
                                <?php else: ?>
                                    <p class="text-muted">Select a log file from the list to view its contents.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Log Statistics -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Log Statistics</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3><?php echo count($log_files); ?></h3>
                                            <p class="text-muted">Total Log Files</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3><?php echo number_format(array_sum(array_column($log_files, 'size')) / 1024, 2); ?> KB</h3>
                                            <p class="text-muted">Total Size</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3><?php echo !empty($log_files) ? date('M d, Y', $log_files[0]['date']) : 'N/A'; ?></h3>
                                            <p class="text-muted">Latest Log</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <a href="?clear_all=1" class="btn btn-danger" onclick="return confirm('Clear all logs? This cannot be undone.')">
                                                <i class="fas fa-trash-alt"></i> Clear All Logs
                                            </a>
                                        </div>
                                    </div>
                                </div>
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
