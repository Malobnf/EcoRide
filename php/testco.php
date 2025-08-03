<?php
require_once(__DIR__ . '/db.php');
$pdo = getPdo();

$stmt = $pdo -> query("SHOW TABLES");
$tables = $stmt -> fetchAll(PDO::FETCH_COLUMN);

echo "tables :" . implode(',', $tables);