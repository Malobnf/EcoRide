<?php
require __DIR__ . '/db.php';

try {
    $pdo = getPdo();
    echo "Connexion OK<br>";

    // Test rapide : lister les tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "<pre>";
    var_dump($tables);
    echo "</pre>";
} catch (Throwable $e) {
    http_response_code(500);
    echo "Erreur BDD :<br>";
    echo "<pre>" . htmlspecialchars($e->getMessage(), ENT_QUOTES) . "</pre>";
}
