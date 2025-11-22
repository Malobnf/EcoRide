<?php
declare(strict_types=1);

require __DIR__ . '/session_boot.php';

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';
$pdo = getPdo();

try {
    $sql = "
        SELECT DISTINCT propulsion
        FROM vehicules
        WHERE propulsion IS NOT NULL
          AND propulsion <> ''
        ORDER BY propulsion ASC
    ";
    $stmt = $pdo->query($sql);
    $propulsions = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        'success'      => true,
        'propulsions'  => $propulsions
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors du chargement des propulsions.',
        'debug'   => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
