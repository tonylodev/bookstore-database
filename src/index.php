<?php
// index.php
// Simple SQL Interface for Bookstore Project

require_once 'db_config.php';

$conn = get_db_connection();

$sql = '';
$error = '';
$message = '';
$result = null;
$row_count = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql = trim($_POST['sql'] ?? '');

    if ($sql === '') {
        $error = 'Please type a SQL statement.';
    } elseif (preg_match('/\bdrop\b/i', $sql)) {
        $error = 'DROP statements are not allowed.';
    } else {

        $is_select = preg_match('/^\s*select/i', $sql);

        $query_result = mysqli_query($conn, $sql);

        if ($query_result === false) {
            $error = 'SQL Error: ' . mysqli_error($conn);
        } else {
            if ($is_select) {
                $result = $query_result;
                $row_count = mysqli_num_rows($result);
            } else {
                if (preg_match('/^\s*create/i', $sql)) {
                    $message = 'Table Created/Updated';
                } elseif (preg_match('/^\s*insert/i', $sql)) {
                    $message = 'Row Inserted';
                } elseif (preg_match('/^\s*delete/i', $sql)) {
                    $message = 'Row(s) Deleted: ' . mysqli_affected_rows($conn);
                } elseif (preg_match('/^\s*update/i', $sql)) {
                    $message = 'Row(s) Updated: ' . mysqli_affected_rows($conn);
                } else {
                    $message = 'Statement executed successfully.';
                }
            }
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
        .message { color: green; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>

<h1>Yat Tung Lo - SQL Interface</h1>
<p>Enter SQL below (DROP is blocked):</p>

<form method="post">
    <textarea name="sql"><?php echo htmlspecialchars($sql); ?></textarea>
    <br><br>
    <input type="submit" value="Run SQL" style="padding:8px 16px;">
</form>

<?php if ($error): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if ($message && !$error): ?>
    <p class="message"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<?php if ($result && !$error): ?>
    <h2>Query Result (<?php echo $row_count; ?> rows)</h2>
    <table>
        <tr>
            <?php foreach (mysqli_fetch_fields($result) as $field): ?>
                <th><?php echo htmlspecialchars($field->name); ?></th>
            <?php endforeach; ?>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <?php foreach ($row as $value): ?>
                    <td><?php echo htmlspecialchars($value); ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

</body>
</html>

<?php mysqli_close($conn); ?>
