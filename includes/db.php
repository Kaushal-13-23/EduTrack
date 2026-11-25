<?php
// Reusable MySQL (mysqli) connection and prepared statement helper for USIAMS.

if (PHP_SAPI !== 'cli' && isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    http_response_code(403);
    exit('Forbidden');
}

ini_set('display_errors', '0');
ini_set('log_errors', '1');

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'usiams_db';

// Enable mysqli exceptions so connection and query errors are handled safely.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($conn) || !($conn instanceof mysqli)) {
    try {
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $conn->set_charset('utf8mb4');
    } catch (mysqli_sql_exception $e) {
        error_log('USIAMS DB connection failed: ' . $e->getMessage());
        if (PHP_SAPI !== 'cli' && !headers_sent()) {
            http_response_code(500);
        }
        echo 'Database service is currently unavailable. Please try again later.';
        exit;
    }
}

/**
 * Execute a prepared SQL statement safely using mysqli.
 *
 * @param mysqli $conn   Active mysqli connection.
 * @param string $sql    SQL query with ? placeholders.
 * @param string $types  Parameter type string (e.g. 'ssi'), optional.
 * @param array  $params Parameter values matching $types, optional.
 * @return mysqli_result|bool Result set for SELECT queries, true for DML success, false on error.
 */
function execute_prepared(mysqli $conn, string $sql, string $types = '', array $params = [])
{
    $stmt = null;
    try {
        $stmt = $conn->prepare($sql);
        if ($types !== '' && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();

        // Return mysqli_result for queries producing rows, or true for INSERT/UPDATE/DELETE.
        if ($stmt->field_count > 0) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        $stmt->close();
        return true;
    } catch (mysqli_sql_exception $e) {
        error_log('USIAMS DB query failed: ' . $e->getMessage());
        if ($stmt instanceof mysqli_stmt) {
            $stmt->close();
        }
        return false;
    }
}
