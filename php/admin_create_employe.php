<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['utilisateur_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
  http_response_code(403);
  echo json_encode(['success'=>false, 'message'=>'Accès refusé']); exit;
}

require_once __DIR__ . '/db.php';
$pdo = getPdo();

$data = json_decode(file_get_contents('php://input'), true) ?? [];

$nom       = trim((string)($data['nom'] ?? ''));
$prenom    = trim((string)($data['prenom'] ?? ''));
$email     = trim((string)($data['email'] ?? ''));
$telephone = trim((string)($data['telephone'] ?? ''));
$password  = (string)($data['password'] ?? '');

if ($nom === '' || $prenom === '' || $email === '' || $password === '') {
  echo json_encode(['success'=>false, 'message'=>'Champs requis manquants']); exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo json_encode(['success'=>false, 'message'=>'Email invalide']); exit;
}

$st = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ?");
$st->execute([$email]);
if ((int)$st->fetchColumn() > 0) {
  echo json_encode(['success'=>false, 'message'=>'Email déjà utilisé']); exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$ins = $pdo->prepare("
  INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role, credits)
  VALUES (?,?,?,?,?,'employe',0)
");
$ins->execute([$nom, $prenom, $email, $telephone, $hash]);

echo json_encode(['success'=>true, 'message'=>'Employé créé', 'id' => (int)$pdo->lastInsertId()]);
