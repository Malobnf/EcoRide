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
$id      = (int)($data['id'] ?? 0);
$newRole = trim((string)($data['role'] ?? ''));

if ($id <= 0 || !in_array($newRole, ['user','employe','admin'], true)) {
  echo json_encode(['success'=>false, 'message'=>'Paramètres invalides']); exit;
}

// Empêcher la possibilité de s'enlever son propre rôle admin
if ($id === (int)$_SESSION['utilisateur_id'] && $newRole !== 'admin') {
  echo json_encode(['success'=>false, 'message'=>'Impossible de modifier votre propre rôle']); exit;
}

// Empêcher la possibilité de retirer un admin s'il est le seul
if ($newRole !== 'admin') {
  $c = (int)$pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role='admin'")->fetchColumn();
  $isTargetAdmin = (bool)$pdo->query("SELECT 1 FROM utilisateurs WHERE id={$id} AND role='admin'")->fetchColumn();
  if ($isTargetAdmin && $c <= 1) {
    echo json_encode(['success'=>false, 'message'=>'Impossible de retirer le dernier admin']); exit;
  }
}

$st = $pdo->prepare("UPDATE utilisateurs SET role=? WHERE id=?");
$st->execute([$newRole, $id]);

echo json_encode(['success'=>true, 'message'=>'Rôle mis à jour']);
