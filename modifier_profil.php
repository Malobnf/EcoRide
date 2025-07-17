<?php
session_start();
header('Content-Type: application/json');


if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => "Utilisateur non connecté"]);
  exit;
}

$nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
$apropos = filter_input(INPUT_POST, 'apropos', FILTER_SANITIZE_STRING);

if (!$nom || $email) {
  echo json_encode(['success' => false, 'message' => "Nom et email obligatoires"]);
  exit;
}

// Connexion BDD
try {
  $pdo = new PDO("mysql:host=localhost;dbname=ecoride", "utilisateur", "motdepasse");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, email = ?, telephone = ?, apropos = ? WHERE id = ?");
  $stmt->execute([$nom, $email, $telephone, $apropos, $_SESSION['user_id']]);

  echo json_encode(['success' => true]);

} catch (Exception $e) {
  error_log($e->getMessage());
  echo json_encode(['success' => false, 'message' => "Erreur serveur."]);
}
