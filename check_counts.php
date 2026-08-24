<?php
$pdo = new PDO('mysql:host=127.127.126.50;port=3306;dbname=codemaster', 'root', '');
$r = $pdo->query('SELECT source, COUNT(*) as c FROM problems GROUP BY source')->fetchAll(PDO::FETCH_ASSOC);
$total = 0;
foreach ($r as $row) {
    echo $row['source'] . ': ' . $row['c'] . PHP_EOL;
    $total += $row['c'];
}
echo 'Total: ' . $total . PHP_EOL;
$withTests = $pdo->query('SELECT COUNT(*) FROM problems WHERE tests_json IS NOT NULL AND tests_json != \'null\' AND tests_json != \'[]\'')->fetchColumn();
echo 'With tests: ' . $withTests . PHP_EOL;
