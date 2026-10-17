<?php
// Run with php -n: simulate the MySQLi boundary, execute the real page/controller.
if (extension_loaded('mysqli')) {
    fwrite(STDERR, "Run these isolated tests with php -n.\n");
    exit(1);
}

error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

class mysqli_sql_exception extends RuntimeException {}
class mysqli_result {}
define('MYSQLI_REPORT_ERROR', 1);
define('MYSQLI_REPORT_STRICT', 2);
$scenario = $argv[1];
$queries = [];
$closed = false;
$detail = 'Simulated database detail that must stay out of the page';

function mysqli_report($mode) {
    if ($mode !== (MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)) {
        throw new RuntimeException('Strict MySQLi reporting must be enabled.');
    }
}
function mysqli_connect($host, $user, $password, $database, $port) {
    global $scenario, $detail;
    if ($scenario === 'connection_failure') {
        throw new mysqli_sql_exception($detail);
    }
    return new stdClass();
}
function mysqli_set_charset($conn, $charset) { return true; }
function mysqli_query($conn, $sql) {
    global $scenario, $queries, $detail;
    $queries[] = $sql;
    if (($scenario === 'session_failure' && count($queries) === 1)
        || ($scenario === 'invalid_query' && count($queries) === 2)) {
        throw new mysqli_sql_exception($detail);
    }
    return count($queries) === 1 || $scenario === 'no_result' ? true : new mysqli_result();
}
function mysqli_num_rows($result) { return 1; }
function mysqli_fetch_fields($result) {
    return [(object) ['name' => '<column>'], (object) ['name' => 'empty'],
        (object) ['name' => 'zero'], (object) ['name' => 'text']];
}
function mysqli_fetch_assoc($result) {
    static $fetched = false;
    if ($fetched) { return null; }
    $fetched = true;
    return [null, '', '0', '<script>alert("demo")</script>'];
}
function mysqli_close($conn) {
    global $closed;
    $closed = true;
}

putenv('BOOKSTORE_DB_USER=bookstore_reader');
putenv('BOOKSTORE_DB_PASSWORD');
putenv('BOOKSTORE_DB_HOST=127.0.0.1');
putenv('BOOKSTORE_DB_PORT=3306');
putenv('BOOKSTORE_DB_NAME=OnlineBookstore');
if ($scenario === 'missing_user') { putenv('BOOKSTORE_DB_USER'); }

$_SERVER['REQUEST_METHOD'] = $scenario === 'get' ? 'GET' : 'POST';
$_POST['sql'] = 'SELECT * FROM Book';
if ($scenario === 'blocked') { $_POST['sql'] = 'DELETE FROM Book'; }
if ($scenario === 'empty') { $_POST['sql'] = '  '; }
if ($scenario === 'array_input') { $_POST['sql'] = ['SELECT 1']; }
if ($scenario === 'escaping') { $_POST['sql'] = "SELECT '</textarea><script>'"; }
if ($scenario === 'no_result') { $_POST['sql'] = 'SELECT 1 INTO @demo_value'; }

ob_start();
require __DIR__ . '/../src/index.php';
$html = ob_get_clean();
echo json_encode(['html' => $html, 'status' => http_response_code() ?: 200,
    'queries' => $queries, 'closed' => $closed], JSON_THROW_ON_ERROR);
