<?php
session_start();
header('Content-Type: application/json');

// Récupération des données JSON 

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
  echo json_encode(['success' => false, 'message' => "Champs vides"]);
  exit;
}

// Connexion BDD
try {
  $pdo = new PDO("mysql:host=localhost;dbname=ecoride", "root", "");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  error_log("Connexion BDD OK");
} catch (PDOException $e) {
  error_log("Erreur BDD" . $e->getMessage());
  echo json_encode(['success' => false, 'message' => "Erreur de connexion à la base de données"]);
  exit;
}

// Vérification de l'utilisateur
$stmt = $pdo->prepare('SELECT id, mot_de_passe FROM utilisateurs WHERE nom = ?');
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['mot_de_passe'])) {
  $_SESSION['utilisateur_id'] = $user['id'];
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => "Identifiants incorrects"]);
}
exit;

// Déconnexion

