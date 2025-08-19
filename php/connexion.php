<?php
declare(strict_types=1); 
session_start();
header('Content-Type: application/json');

// Connexion BDD
require_once (__DIR__ . '/db.php');
$pdo = getPdo();

// Récupération des données JSON 

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
  echo json_encode(['success' => false, 'message' => "Champs vides"]);
  exit;
}

// Vérification de l'utilisateur et de son rôle
$stmt = $pdo->prepare('SELECT id, mot_de_passe, role FROM utilisateurs WHERE nom = ?');
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['mot_de_passe'])) {
  $_SESSION['utilisateur_id'] = $user['id'];
  $_SESSION['role'] = $user['role'];

  echo json_encode([
    'success' => true,
    'role' => $user['role']
  ]);
} else {
  echo json_encode(['success' => false, 'message' => "Identifiants incorrects"]);
}
exit;

// Déconnexion

