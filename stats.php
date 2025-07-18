<?php
header('Content-Type: application/json');

try {
  $pdo = new PDO('mysql:host=localhost;dbname=ecoride;charset=utf8', 'root', '');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Covoits par Jour
  $trajetsJour = $pdo->query("
    SELECT DATE(date_trajet) AS periode, COUNT(*) AS total
    FROM trajets
    GROUP BY DATE(date_trajet)
    ORDER BY periode ASC
  ")->fetchAll(PDO::FETCH_ASSOC);
  
  // Covoits par mois
  $trajetsMois = $pdo->query("
    SELECT DATE_FORMAT(date_trajet, '%Y-%m') AS periode, COUNT(*) AS total
    FROM trajets
    GROUP BY DATE_FORMAT(date_trajet, '%Y-%m')
    ORDER BY periode ASC
  ")->fetchAll(PDO::FETCH_ASSOC);

  // Covoits par an
  $trajetsAnnee = $pdo->query("
    SELECT YEAR(date_trajet) AS periode, COUNT(*) AS total
    FROM trajets
    GROUP BY YEAR(date_trajet)
    ORDER BY periode ASC
  ")->fetchAll(PDO::FETCH_ASSOC);

  // Crédits par jour
  $creditsJour = $pdo->query("
    SELECT DATE(date_trajet) AS periode, COUNT(*) * 2 AS credits
    FROM trajets
    GROUP BY DATE(date_trajet)
    ORDER BY periode ASC
  ")->fetchAll(PDO::FETCH_ASSOC);

  // Crédits par mois
  $creditsMois = $pdo->query("
    SELECT DATE_FORMAT(date_trajet, '%Y-%m') AS periode, COUNT(*) * 2 AS credits
    FROM trajets
    GROUP BY DATE_FORMAT(date_trajet, '%Y-%m')
    ORDER BY periode ASC
  ")->fetchAll(PDO::FETCH_ASSOC);

  // Crédits par an
  $creditsAnnee = $pdo->query("
    SELECT YEAR(date_trajet) AS periode, COUNT(*) AS total
    FROM trajets
    GROUP BY YEAR(date_trajet)
    ORDER BY periode ASC
  ")->fetchAll(PDO::FETCH_ASSOC);

  // Total crédits
  $totalCredits = $pdo->query("
    SELECT COUNT(*) * 2 AS total FROM trajets
  ")->fetchColumn();

  echo json_encode([
    'trajets' => [
      'jour' => $trajetsJour,
      'mois' => $trajetsMois,
      'annee' => $trajetsAnnee
    ],
    'credits' => [
      'jour' => $creditsJour,
      'mois' => $creditsMois,
      'annee' => $creditsAnnee      
    ],
    'totalCredits' => $totalCredits
  ]);

} catch (PDOException $e) {
  echo json_encode([
    'error' => 'Erreur BDD : ' . $e->getMessage()
  ]);
}