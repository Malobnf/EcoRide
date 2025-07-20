<?php
header('Content-Type: application/json');
session_start();
require 'db.php'; 


$utilisateur_id = $_SESSION['utilisateur_id'] ?? null;

if (!$utilisateur_id) {
  echo json_encode(['success' => false, 'message' => "Utilisateur non connecté"]);
  exit;
}

try {
  $stmt = $pdo->prepare("SELECT preferences FROM utilisateurs WHERE id = :id");
  $stmt->execute([':id' => $utilisateur_id]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  $prefs = [];
  if ($result && $result['preferences']) {
    $prefs = json_decode($result['preferences'], true) ?: [];
  }

  echo json_encode(['success' => true, 'preferences' => $prefs]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => "Erreur serveur : " . $e->getMessage()]);
}