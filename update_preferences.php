<?php
header('Content-Type: application/json');
session_start();
require 'db.php'; 


$utilisateur_id = $_SESSION['utilisateur_id'] ?? null;

if (!$utilisateur_id) {
  echo json_encode(['success' => false, 'message' => "Utilisateur non connecté"]);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$preferences = $data['preferences'] ?? '';

$validPrefs = ['non_fumeur', 'animaux_ok', 'musique', 'discussion'];
$filteredPrefs = array_values(array_intersect($preferences, $validPrefs));

// Sauvegarde dans colonne "preferences" dans table "utilisateurs"
try {
  $stmt = $pdo->prepare("UPDATE utilisateurs SET preferences = :prefs WHERE id = :id");
  $stmt->execute([
    ':prefs' => json_encode($filteredPrefs),
    ':id' => $utilisateur_id
  ]);

  echo json_encode(['success' => true, 'message' => "Préférences mises à jour"]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => "Erreur serveur : " .$e->getMessage()]);
}