<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';
$pdo = getPdo();

// Vérification du rôle (admin uniquement)
if (!isset($_SESSION['utilisateur_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Non authentifié']);
  exit;
}
$st = $pdo->prepare('SELECT role FROM utilisateurs WHERE id = ?');
$st->execute([$_SESSION['utilisateur_id']]);
$role = $st->fetchColumn();
if ($role !== 'admin') {
  http_response_code(403);
  echo json_encode(['error' => 'Accès refusé']);
  exit;
}

try {
  // Trajets par jour / mois / année
  $trajetsJour = $pdo->query("
    SELECT DATE(date_trajet) AS periode, COUNT(*) AS total
    FROM trajets
    GROUP BY DATE(date_trajet)
    ORDER BY periode ASC
  ")->fetchAll(PDO::FETCH_ASSOC);

  $trajetsMois = $pdo->query("
    SELECT DATE_FORMAT(date_trajet, '%Y-%m') AS periode, COUNT(*) AS total
    FROM trajets
    GROUP BY DATE_FORMAT(date_trajet, '%Y-%m')
    ORDER BY periode ASC
  ")->fetchAll(PDO::FETCH_ASSOC);

  $trajetsAnnee = $pdo->query("
    SELECT YEAR(date_trajet) AS periode, COUNT(*) AS total
    FROM trajets
    GROUP BY YEAR(date_trajet)
    ORDER BY periode ASC
  ")->fetchAll(PDO::FETCH_ASSOC);

  // Crédits
  $creditsJour = $pdo->query("
    SELECT DATE(date_trajet) AS periode, COUNT(*) * 2 AS credits
    FROM trajets
    GROUP BY DATE(date_trajet)
    ORDER BY periode ASC
  ")->fetchAll(PDO::FETCH_ASSOC);

  $creditsMois = $pdo->query("
    SELECT DATE_FORMAT(date_trajet, '%Y-%m') AS periode, COUNT(*) * 2 AS credits
    FROM trajets
    GROUP BY DATE_FORMAT(date_trajet, '%Y-%m')
    ORDER BY periode ASC
  ")->fetchAll(PDO::FETCH_ASSOC);

  $creditsAnnee = $pdo->query("
    SELECT YEAR(date_trajet) AS periode, COUNT(*) * 2 AS credits
    FROM trajets
    GROUP BY YEAR(date_trajet)
    ORDER BY periode ASC
  ")->fetchAll(PDO::FETCH_ASSOC);

  $totalCredits = (int)$pdo->query("SELECT COUNT(*) * 2 AS total FROM trajets")->fetchColumn();

  echo json_encode([
    'trajets' => ['jour' => $trajetsJour, 'mois' => $trajetsMois, 'annee' => $trajetsAnnee],
    'credits' => ['jour' => $creditsJour, 'mois' => $creditsMois, 'annee' => $creditsAnnee],
    'totalCredits' => $totalCredits
  ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Erreur BDD : '.$e->getMessage()]);
}
