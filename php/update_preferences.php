<?php
session_start();
header('Content-Type: application/json');
require '/db.php'; 
$pdo = getPdo();


$utilisateur_id = $_SESSION['utilisateur_id'] ?? null;

if (!$utilisateur_id) {
  echo json_encode(['success' => false, 'message' => "Utilisateur non connecté"]);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['preferences']) || !is_array($data['preferences'])) {
  echo json_encode(['success' => false, 'message' => "Préférences non fournies"]);
  exit;
}

$preferences = $data['preferences'];
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