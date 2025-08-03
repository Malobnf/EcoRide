<?php
session_start();
require_once(__DIR__ . '/db.php');

if (!isset($_GET['id'])) {
  echo json_encode(['success' => false, 'message' => 'ID manquant']);
  exit;
}

$id = (int)$_GET['id'];
$user_id = $_SESSION['utilisateur_id'];

$stmt = $pdo->prepare("SELECT * FROM vehicules WHERE id = ? AND utilisateur_id = ?");
$stmt->execute([$id, $user_id]);
$vehicule = $stmt->fetch(PDO::FETCH_ASSOC);

if ($vehicule) {
  echo json_encode(['success' => true, 'vehicule' => $vehicule]);
} else {
  echo json_encode(['success' => false, 'message' => 'Véhicule introuvable']);
}
