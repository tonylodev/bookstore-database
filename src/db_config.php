<?php
function get_db_connection() {
    $host = getenv('BOOKSTORE_DB_HOST') ?: '127.0.0.1';
    $port = (int) (getenv('BOOKSTORE_DB_PORT') ?: 3306);
    $user = getenv('BOOKSTORE_DB_USER');
    $password = getenv('BOOKSTORE_DB_PASSWORD');
    $database = getenv('BOOKSTORE_DB_NAME') ?: 'OnlineBookstore';

    if ($user === false || $user === '') {
        throw new RuntimeException(
            'BOOKSTORE_DB_USER is required. See the README for configuration instructions.'
        );
    }

    $conn = mysqli_connect(
        $host,
        $user,
        $password === false ? '' : $password,
        $database,
        $port
    );

    if (!$conn) {
        throw new RuntimeException(
            'Database connection failed. Check the BOOKSTORE_DB_* environment variables.'
        );
    }

    mysqli_set_charset($conn, 'utf8mb4');

    if (!mysqli_query($conn, 'SET SESSION TRANSACTION READ ONLY')) {
        mysqli_close($conn);
        throw new RuntimeException('Could not enable the read-only database session.');
    }

    return $conn;
}
?>
