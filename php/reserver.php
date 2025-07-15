<?php

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'not_logged_in']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$trajetId = intval($data['$trajetId']);
$userId = $_SESSION['user_id'];

// Connexion BDD
$conn = new PDO('mysql:host=localhost,dbname=ecoride', "user", "mdp",);

// Vérifie le trajet
$stmt = $conn->prepare("SELECT places, prix FROM trajets WHERE id = ?");
$stmt->execute([$trajetId]);
$trajet = $stmt->fetch();

if (!$trajet || $trajet['places'] <= 0) {
  echo json_encode(['status' => 'no_places']);
  exit;
}

// Vérifie les crédits de l'utilisateur
$stmt = $conn->prepare("SELECT credits FROM utilisateurs WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if($user['credits'] < $trajet['prix']) {
  echo json_encode(['status' => 'not_enough_credits']);
  exit;
}

// Si tout est validé, effectuer réservation
$conn->beginTransaction();

// Enregistrer la réservation
$conn->prepare("INSERT INTO reservations (utilisateur_id, trajet_id VALUES (?, ?)")
     ->execute([$trajet['prix'], $userId]);

// Réduire le nombre de crédits
$conn->prepare("UPDATE utilisateurs SET credits = credits - ? WHERE id = ?")
     ->execute([$trajet['prix'], $userId]);

// Réduire le nombre de places
$conn->prepare("UPDATE trajets SET places = places - ? WHERE id = ?")
     ->execute([$trajetId]);

$conn->commit();

// Retourner le nombre de places restantes
$newPlaces = $trajet['places'] - 1;
echo json_encode(['status' => 'success', 'remaining_places' => $newPlaces]);
