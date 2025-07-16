<?php
session_start();
header('Content-Type: application.json');

if (!isset($_SESSION['utilisateur_id'])) {
  echo json_encode(['success' => false, 'message' => "Non connecté"]);
  exit;
}

$userId = $_SESSION['utilisateur_id'];

try {
  $pdo = new PDO("mysql:host=localhost;dbname=ecoride", "admin", "30303030");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $pdo->prepare("SELECT credits FROM utilisateurs WHERE id = ?");
  $stmt->execute([$userId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    echo json_encode(['success' => true, 'credits' => $user['credits']]);
  } else {
    echo json_encode(['success' => false, 'message' => "Utilisateur introuvable"]);
  }

} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => "Erreur base de données."]);
  exit;
}