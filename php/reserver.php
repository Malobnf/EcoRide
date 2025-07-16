<?php

session_start();
header('Content-Type: application/json');

// Vérifie la connexion de l'utilisateur
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'not_logged_in']);
  exit;
}

// Récupération des données JSON envoyées depuis JS
$data = json_decode(file_get_contents('php://input'), true);
$trajetId = intval($data['trajetId']);
$userId = $_SESSION['user_id'];

// Connexion BDD
// PDO = PHP Data Object, permet d'interagir avec la BDD de manière sécurisée.
try {
  $pdo = new PDO('mysql:host=localhost; dbname=ecoride', "root", "");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Permet d'afficher une erreur plutôt que retourner 'false'
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur de connexion BDD']);
    exit;
}

// Vérifie le trajet
$stmt = $pdo->prepare("SELECT places, prix FROM trajets WHERE id = ?");
$stmt->execute([$trajetId]);
$trajet = $stmt->fetch();

if (!$trajet || $trajet['places'] <= 0) {
  echo json_encode(['status' => 'no_places']);
  exit;
}

// Vérifie les crédits de l'utilisateur
$stmt = $pdo->prepare("SELECT credits FROM utilisateurs WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if($user['credits'] < $trajet['prix']) {
  echo json_encode(['status' => 'not_enough_credits']);
  exit;
}

try {
// Si tout est validé, commencer la réservation
$pdo->beginTransaction();

// Enregistrer la réservation
$stmt = $pdo->prepare("INSERT INTO reservations (utilisateur_id, trajet_id) VALUES (?, ?)");
$stmt->execute([$userId, $trajetId]);

// Réduire le nombre de crédits
$stmt = $pdo->prepare("UPDATE utilisateurs SET credits = credits - ? WHERE id = ?");
$stmt->execute([$trajet['prix'], $userId]);

// Réduire le nombre de places
$stmt = $pdo->prepare("UPDATE trajets SET places = places - 1 WHERE id = ?");
$stmt->execute([$trajetId]);

// Valider la transaction
$pdo->commit();

// Retourner le nombre de places restantes
$newPlaces = $trajet['places'] - 1;
echo json_encode(['status' => 'success', 'remaining_places' => $newPlaces]);

} catch (PDOException $e) {
  $pdo->rollBack();
  echo json_encode(['status' => 'error', 'message' => 'Erreur de réservation', 'error_info' => $e->getMessage()]);
}