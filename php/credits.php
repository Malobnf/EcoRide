<?php
session_start();
header('Content-Type: application/json');

require_once(__DIR__ . '/db.php');
$pdo = getPdo();

if (!isset($_SESSION['utilisateur_id'])) {
  echo json_encode(['success' => false, 'message' => "Non connecté"]);
  exit;
}

$userId = $_SESSION['utilisateur_id'];

try {
  $stmt = $pdo->prepare("SELECT credits FROM utilisateurs WHERE id = ?");
  $stmt->execute([$userId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    echo json_encode(['success' => true, 'credits' => $user['credits']]);
  } else {
    echo json_encode(['success' => false, 'message' => "Utilisateur introuvable"]);
  }

} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => "Erreur base de données." . $e->getMessage()]);
  exit;
}