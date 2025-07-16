<?php
session_start();
header('Content-Type: application.json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => "Non connecté"]);
  exit;
}

try {
  $pdo = new PDO("mysql:host=localhost;dbname=ecoride", "admin", "30303030");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => "Erreur base de données."]);
  exit;
}

$stmt = $pdo->prepare('SELECT credits FROM utilisateurs WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
  echo json_encode(['success' => true, 'credits' => (int)$user['credits']]);
} else {
  echo json_encode(['success' => false, 'message' => "Utilisateur non trouvé."]);
}