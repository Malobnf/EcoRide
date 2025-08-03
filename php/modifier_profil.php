<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/db.php');
$pdo = getPdo();

if (!isset($_SESSION['utilisateur_id'])) {
  echo json_encode(['success' => false, 'message' => "Utilisateur non connecté"]);
  exit;
}

$utilisateur_id = $_SESSION['utilisateur_id'];
$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$email = $_POST['email'] ?? '';
$telephone = $_POST['telephone'] ?? '';
$description = $_POST['description'] ?? '';

try {
  $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, telephone = ?, description = ? WHERE id = ?");
  $stmt->execute([$nom, $prenom, $email, $telephone, $description, $utilisateur_id]);

  echo json_encode(['success' => true]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

