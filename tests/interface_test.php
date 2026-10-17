<?php
// No database or third-party test framework is needed for controller regressions.
function check($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
}

$scenarios = ['get', 'success', 'invalid_query', 'connection_failure',
    'session_failure', 'missing_user', 'blocked', 'empty', 'array_input', 'escaping', 'no_result'];
chdir(__DIR__);
foreach ($scenarios as $scenario) {
    $process = proc_open([PHP_BINARY, '-n', 'interface_fixture.php', $scenario],
        [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    check(is_resource($process), 'Could not start PHP test process.');
    $output = stream_get_contents($pipes[1]);
    $errors = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    check(proc_close($process) === 0 && $errors === '', "$scenario: unexpected PHP error");
    $page = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
    $html = $page['html'];
    check(strpos($html, '</html>') !== false, "$scenario: page did not finish rendering");
    check(strpos($html, 'Simulated database detail') === false, "$scenario: raw error leaked");

    if (in_array($scenario, ['connection_failure', 'session_failure', 'missing_user'], true)) {
        check($page['status'] === 503, "$scenario: expected HTTP 503");
        check(strpos($html, 'Database unavailable.') !== false, "$scenario: missing error");
        check(count($page['queries']) === ($scenario === 'session_failure' ? 1 : 0),
            "$scenario: query executed after connection failure");
        check($page['closed'] === ($scenario === 'session_failure'),
            "$scenario: connection cleanup failed");
    } else {
        check($page['status'] === 200 && $page['closed'], "$scenario: response/cleanup failed");
        check($page['queries'][0] === 'SET SESSION TRANSACTION READ ONLY',
            "$scenario: missing read-only session");
    }

    if ($scenario === 'invalid_query') {
        check(strpos($html, 'Query failed.') !== false, 'Query error was not displayed.');
        check(strpos($html, '<h2>Query Result') === false, 'Failed query rendered a result.');
    }
    if ($scenario === 'no_result') {
        check(strpos($html, 'must return a result set.') !== false, 'Missing result-set error.');
        check(strpos($html, '<h2>Query Result') === false, 'Non-result SELECT rendered a table.');
    }
    if (in_array($scenario, ['get', 'blocked', 'empty', 'array_input'], true)) {
        check(count($page['queries']) === 1, "$scenario: unexpected user query");
        if ($scenario !== 'get') {
            $message = $scenario === 'blocked' ? 'Only read-only SELECT' : 'Please type a SELECT';
            check(strpos($html, $message) !== false, "$scenario: missing validation error");
        }
    }
    if (in_array($scenario, ['success', 'escaping'], true)) {
        check(strpos($html, 'Query Result (1 rows)') !== false, 'Missing successful result.');
        check(strpos($html, '<td><em>NULL</em></td>') !== false, 'NULL was not explicit.');
        check(strpos($html, '<td></td>') !== false, 'Empty string was not preserved.');
        check(strpos($html, '<td>0</td>') !== false, 'Zero was not preserved.');
        check(strpos($html, '&lt;column&gt;') !== false, 'Column was not escaped.');
        check(strpos($html, '<script>') === false, 'Query/result HTML was not escaped.');
        check(strpos($html, '&lt;script&gt;') !== false, 'Missing escaped result text.');
    }
    echo "PASS $scenario\n";
}
echo count($scenarios) . " interface scenarios passed.\n";
