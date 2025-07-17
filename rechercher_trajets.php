<?php
header('Content-Type: application/json');
require 'db.php'; // Connexion PDO

$input = json_encode(file_get_contents('php://input'), true);

$depart = trim($input['depart'] ?? '');
$arrivee = trim($input['arrivee'] ?? '');
$date = trim($input['date'] ?? '');

if (!$depart || !$arrivee || !$date) {
  echo json_encode(['success' => false, 'message' => "Champs manquants"]);
  exit;
}

try {
  $stmt = $pdo->prepare("SELECT t.*, u.username AS conducteur FROM trajets t JOIN utilisateurs u ON t.conducteur_id = u.id WHERE t.ville_depart = :depart AND t.ville_arrivee = :arrivee AND t.date_trajet = :date_trajet");
  $stmt->execute([
    ':depart' => $depart,
    ':arrivee' => $arrivee,
    ':date_trajet' => $date
  ]);

  $trajets = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    'success' => true, 'trajets' =>$trajets 
  ]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => "Erreur serveur."]);
}