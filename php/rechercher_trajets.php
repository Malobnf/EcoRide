<?php
header('Content-Type: application/json');
session_start();
require 'db.php'; // Connexion PDO
$pdo = getPdo();

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

$depart = trim($data['depart'] ?? '');
$arrivee = trim($data['arrivee'] ?? '');
$date = trim($data['date'] ?? '');

if (
  !isset($data['depart'], $data['arrivee'], $data['date']) ||
  empty(trim($data['depart'])) ||
  empty(trim($data['arrivee'])) ||
  empty(trim($data['date']))
) {
    echo json_encode(['success' => false, 'message' => "Champs manquants"]);
    exit;
}

try {
  $stmt = $pdo->prepare("
    SELECT 
      t.id, t.ville_depart, t.ville_arrivee, t.date_trajet, t.heure_depart, t.prix, t.places_disponibles,
      u.nom AS conducteur
    FROM trajets t
    JOIN utilisateurs u ON t.conducteur_id = u.id
    WHERE 
      t.ville_depart = :depart AND 
      t.ville_arrivee = :arrivee AND 
      t.date_trajet = :date_trajet
  ");

  $stmt->execute([
    ':depart' => $depart,
    ':arrivee' => $arrivee,
    ':date_trajet' => $date
  ]);

  $trajets = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'trajets' => $trajets]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => "Erreur serveur : " . $e->getMessage()]);
}


























/*
$input = json_decode(file_get_contents('php://input'), true);

$depart = trim($input['depart'] ?? '');
$arrivee = trim($input['arrivee'] ?? '');
$date = trim($input['date'] ?? '');

if (!$depart || !$arrivee || !$date) {
  echo json_encode(['success' => false, 'message' => "Champs manquants"]);
  exit;
}

try {
  $stmt = $pdo->prepare("SELECT t.*, u.nom AS conducteur FROM trajets t JOIN utilisateurs u ON t.conducteur_id = u.id WHERE t.ville_depart = :depart AND t.ville_arrivee = :arrivee AND t.date_trajet = :date_trajet");
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
  echo json_encode(['success' => false, 'message' => "Erreur serveur." . $e->getMessage()]);
} 
  */