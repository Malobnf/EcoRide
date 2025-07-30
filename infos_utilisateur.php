<?php
session_start();
header('Content-Type: application/json');
require 'db.php';
$pdo = getPdo();

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => "Utilisateur non connecté"]);
  exit;
}

try {
  // Vérification de l'utilisateur
  $stmt = $pdo->prepare('SELECT nom, prenom FROM utilisateurs WHERE id = ?');
  $stmt->execute([$user_id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if($user) {
    echo json_encode(['success' => true, 'nom' => $user['nom'], 'prenom' => $user['prenom']]);
  } else {
    echo json_encode(['success' => false, 'message' => "Utilisateur non-trouvé"]);
  }

} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => "Erreur base de données."]);
}