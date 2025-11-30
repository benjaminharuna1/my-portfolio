<?php
/**
 * Database Helper Functions with Error Logging
 */

/**
 * Execute a database query with error logging
 */
function executeQuery($conn, $query, $log_query = false) {
    try {
        if ($log_query) {
            ErrorLogger::log("Executing query: " . substr($query, 0, 100) . "...", 'INFO');
        }
        
        $result = $conn->query($query);
        
        if (!$result) {
            throw new Exception("Query failed: " . $conn->error);
        }
        
        return $result;
    } catch (Exception $e) {
        ErrorLogger::log("Database query error: " . $e->getMessage(), 'ERROR');
        return false;
    }
}

/**
 * Execute an insert query and return the inserted ID
 */
function insertQuery($conn, $query) {
    try {
        ErrorLogger::log("Executing insert query: " . substr($query, 0, 100) . "...", 'INFO');
        
        if (!$conn->query($query)) {
            throw new Exception("Insert failed: " . $conn->error);
        }
        
        $insert_id = $conn->insert_id;
        ErrorLogger::log("Insert successful. ID: $insert_id", 'INFO');
        
        return $insert_id;
    } catch (Exception $e) {
        ErrorLogger::log("Database insert error: " . $e->getMessage(), 'ERROR');
        return false;
    }
}

/**
 * Execute an update query and return affected rows
 */
function updateQuery($conn, $query) {
    try {
        ErrorLogger::log("Executing update query: " . substr($query, 0, 100) . "...", 'INFO');
        
        if (!$conn->query($query)) {
            throw new Exception("Update failed: " . $conn->error);
        }
        
        $affected_rows = $conn->affected_rows;
        ErrorLogger::log("Update successful. Affected rows: $affected_rows", 'INFO');
        
        return $affected_rows;
    } catch (Exception $e) {
        ErrorLogger::log("Database update error: " . $e->getMessage(), 'ERROR');
        return false;
    }
}

/**
 * Execute a delete query and return affected rows
 */
function deleteQuery($conn, $query) {
    try {
        ErrorLogger::log("Executing delete query: " . substr($query, 0, 100) . "...", 'INFO');
        
        if (!$conn->query($query)) {
            throw new Exception("Delete failed: " . $conn->error);
        }
        
        $affected_rows = $conn->affected_rows;
        ErrorLogger::log("Delete successful. Affected rows: $affected_rows", 'INFO');
        
        return $affected_rows;
    } catch (Exception $e) {
        ErrorLogger::log("Database delete error: " . $e->getMessage(), 'ERROR');
        return false;
    }
}

/**
 * Fetch a single row
 */
function fetchRow($conn, $query) {
    try {
        $result = executeQuery($conn, $query);
        if ($result) {
            return $result->fetch_assoc();
        }
        return null;
    } catch (Exception $e) {
        ErrorLogger::log("Fetch row error: " . $e->getMessage(), 'ERROR');
        return null;
    }
}

/**
 * Fetch all rows
 */
function fetchAll($conn, $query) {
    try {
        $result = executeQuery($conn, $query);
        if ($result) {
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
        return [];
    } catch (Exception $e) {
        ErrorLogger::log("Fetch all error: " . $e->getMessage(), 'ERROR');
        return [];
    }
}

/**
 * Get the number of rows in a result
 */
function getRowCount($conn, $query) {
    try {
        $result = executeQuery($conn, $query);
        if ($result) {
            return $result->num_rows;
        }
        return 0;
    } catch (Exception $e) {
        ErrorLogger::log("Row count error: " . $e->getMessage(), 'ERROR');
        return 0;
    }
}
?>
