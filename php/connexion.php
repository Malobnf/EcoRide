<?php
declare(strict_types=1); 
session_start();
header('Content-Type: application/json');

// Connexion BDD
require_once (__DIR__ . '/db.php');
$pdo = getPdo();

// Récupération des données JSON 

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
if (!is_array($payload)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'JSON invalide']);
  exit;
}

$username = trim($payload['username'] ?? '');
$password = $payload['password'] ?? '';

if ($username === '' || $password === '') {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Champs vides']);
  exit;
}

// Vérification de l'utilisateur et de son rôle
$stmt = $pdo->prepare('SELECT id, mot_de_passe, role FROM utilisateurs WHERE nom = ?');
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

//Connexion invalide
if (!$user || !password_verify($password, $user['mot_de_passe'])) {
  http_response_code(401);
  echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
  exit;
}

// Connexion valide
session_regenerate_id(true);
$_SESSION['utilisateur_id'] = (int)$user['id'];
$_SESSION['role']           = (string)$user['role'];

$redirect = ($user['role'] === 'admin') ? '/index.php?page=admin' : '/index.php?page=profil';

echo json_encode([
  'success'  => true,
  'role'     => $user['role'],
  'redirect' => $redirect
], JSON_UNESCAPED_UNICODE);
exit;
