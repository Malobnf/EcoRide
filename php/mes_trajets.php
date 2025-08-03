<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/db.php');
$pdo = getPdo();

if (!isset($_SESSION['utilisateur_id'])) {
  echo json_encode(['success' => false, 'message' => "Utilisateur non connecté."]);
  exit;
}

$idUtilisateur = $_SESSION['utilisateur_id'];

try {
    // Conducteur OU passager
    $stmt = $pdo->prepare("
    (
      SELECT 
        t.id AS id,
        t.ville_depart,
        t.ville_arrivee,
        t.date_trajet,
        t.etat,
        u.nom AS conducteur,
        t.prix,
        NULL AS reservation_id,
        'conducteur' AS role
      FROM trajets t
      JOIN utilisateurs u ON t.conducteur_id = u.id
      WHERE t.conducteur_id = :uid
    )
    UNION ALL
    (
      SELECT 
        t.id AS id,
        t.ville_depart,
        t.ville_arrivee,
        t.date_trajet,
        t.etat,
        u.nom AS conducteur,
        t.prix,
        r.id AS reservation_id,
        'passager' AS role
      FROM reservations r
      JOIN trajets t ON r.trajet_id = t.id
      JOIN utilisateurs u ON t.conducteur_id = u.id
      WHERE r.utilisateur_id = :uid
    )
    ORDER BY date_trajet DESC
  ");

  $stmt->execute([':uid' => $idUtilisateur]);
  $trajets = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if(empty($trajets)) {
    echo json_encode([
      'success' => true,
      'trajets' => [],
      'message' => "Aucun trajet réservé ou créé."
    ]);
  } else {
    echo json_encode(['success' => true, 'trajets' => $trajets]);
  }
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}