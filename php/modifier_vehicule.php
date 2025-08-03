<?php
ob_start();

session_start();
require_once '../php/db.php';
$pdo = getPdo();
header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur_id'])) {
  echo json_encode(['success' => false, 'message' => "Utilisateur non connecté"]);
  file_put_contents("log_modif.txt", ob_get_contents());
  ob_end_flush();
  exit;
}

$data = $_POST;
$id = $data['id'] ?? null;

if (!$id) {
  echo json_encode(['success' => false, 'message' => "ID du véhicule manquant"]);
  file_put_contents("log_modif.txt", ob_get_contents());
  ob_end_flush();
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM vehicules WHERE id = ? AND utilisateur_id = ?");
$stmt->execute([$id, $_SESSION['utilisateur_id']]);
$vehicule = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vehicule) {
  echo json_encode(['success' => false, 'message' => "Véhicule introuvable"]);
  file_put_contents("log_modif.txt", ob_get_contents());
  ob_end_flush();
  exit;
}

$date_immat = $data['date_immat'] ?? '';
$places = $data['places'] ?? '';

$stmt = $pdo->prepare("UPDATE vehicules SET plaque = ?, date_immat = ?, marque = ?, modele = ?, couleur = ?, places = ? WHERE id = ? AND utilisateur_id = ?");
$success = $stmt->execute([
  $data['plaque'], $date_immat , $data['marque'], $data['modele'], $data['couleur'], $places,
  $id, $_SESSION['utilisateur_id']
]);

echo json_encode(['success' => $success]);

file_put_contents("log_modif.txt", ob_get_contents());
ob_end_flush();  // Envoie la sortie tamponnée au client
