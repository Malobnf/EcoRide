<?php
header('Content-Type: application/json');
require 'db.php'; 

session_start();

$utilisateur_id = $_SESSION['utilisateur_id'] ?? null;

if (!$utilisateur_id) {
  echo json_encode(['success' => false, 'message' => "Utilisateur non connecté"]);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$plaque = trim($data['plaque'] ?? '');
$date_immat = trim($data['date_immat'] ?? '');
$marque = trim($data['marque'] ?? '');
$modele = trim($data['modele'] ?? '');
$couleur = trim($data['couleur'] ?? '');
$places = trim($data['places'] ?? '');

if (!$plaque || !$date_immat || !$marque || !$modele || !$couleur || $places < 1) {
  echo json_encode(['success' => false, 'message' => "Champs manquants ou invalides"]);
  exit;
}

try {
  $sql = "INSERT INTO vehicules (utilisateur_id, plaque, date_immat, marque, modele, couleur, places) VALUES (:utilisateur_id, :plaque, :date_immat, :marque, :modele, :couleur, :places)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':utilisateur_id' => $utilisateur_id,
    ':plaque' => $plaque,
    ':date_immat' => $date_immat,
    ':marque' => $marque,
    ':modele' => $modele,
    ':couleur' => $couleur,
    ':places' => $places
  ]);
  
  echo json_encode(['success' => true, 'message' => "Véhicule ajouté avec succès"]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => "Erreur serveur : " . $e->getMessage()]);
}

