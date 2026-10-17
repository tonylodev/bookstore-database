<?php
// index.php
// Simple SQL Interface for Bookstore Project

require_once __DIR__ . '/db_config.php';

$conn = null;
$sql = '';
$error = '';
$result = null;
$row_count = 0;

try {
    $conn = get_db_connection();
} catch (RuntimeException $exception) {
    http_response_code(503);
    $error = 'Database unavailable. Check the server and BOOKSTORE_DB_* configuration.';
}

function is_read_only_select($sql) {
    if (!preg_match('/\Aselect\b/i', ltrim($sql))) {
        return false;
    }

    $blocked_patterns = [
        '/\binto\s+(?:out|dump)file\b/i',
        '/\bload_file\s*\(/i',
        '/\bfor\s+update\b/i',
        '/\block\s+in\s+share\s+mode\b/i',
    ];

    foreach ($blocked_patterns as $pattern) {
        if (preg_match($pattern, $sql)) {
            return false;
        }
    }

    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submitted_sql = $_POST['sql'] ?? '';
    $sql = is_string($submitted_sql) ? trim($submitted_sql) : '';

    if ($conn === null) {
        // Keep the connection error and do not attempt a query.
    } elseif ($sql === '') {
        $error = 'Please type a SELECT statement.';
    } elseif (!is_read_only_select($sql)) {
        $error = 'Only read-only SELECT statements are allowed.';
    } else {
        try {
            $query_result = mysqli_query($conn, $sql);
            if ($query_result instanceof mysqli_result) {
                $result = $query_result;
                $row_count = mysqli_num_rows($result);
            } else {
                $error = 'The SELECT statement must return a result set.';
            }
        } catch (mysqli_sql_exception $exception) {
            $error = 'Query failed. Check the SELECT syntax and table or column names.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Yat Tung Lo - SQL Interface</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        h1 { color: #333333; }
        textarea { width: 100%; height: 150px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #888888; padding: 6px; text-align: left; }
        th { background-color: #eeeeee; }
        .error { color: red; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>

<h1>Yat Tung Lo - SQL Interface</h1>
<p>Enter a read-only SELECT query below. Data-changing, file-access, and locking statements are blocked.</p>

<form method="post">
    <textarea name="sql"><?php echo htmlspecialchars($sql, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></textarea>
    <br><br>
    <input type="submit" value="Run SQL" style="padding:8px 16px;">
</form>

<?php if ($error): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if ($result && !$error): ?>
    <h2>Query Result (<?php echo $row_count; ?> rows)</h2>
    <table>
        <tr>
            <?php foreach (mysqli_fetch_fields($result) as $field): ?>
                <th><?php echo htmlspecialchars($field->name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></th>
            <?php endforeach; ?>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <?php foreach ($row as $value): ?>
                    <td><?php echo $value === null ? '<em>NULL</em>' : htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

</body>
</html>

<?php if ($conn !== null) { mysqli_close($conn); } ?>
