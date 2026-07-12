<?php
$DB_HOST = '127.0.0.1';
$DB_USER = 'bookstore_reader';   
$DB_PASS = 'choose-a-local-password';
$DB_NAME = 'OnlineBookstore';

function get_db_connection() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    $conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if (!$conn) die('Database connection failed: ' . mysqli_connect_error());
    return $conn;
}
?>
