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

$roles = isset($_GET['roles']) ? explode(',', (string)$_GET['roles']) : ['admin','employe','user'];
$roles = array_values(array_filter(array_map('trim', $roles)));
$params = [];
$sql = "SELECT id, nom, prenom, email, telephone, role FROM utilisateurs";

if ($roles) {
  $in = implode(',', array_fill(0, count($roles), '?'));
  $sql .= " WHERE role IN ($in)";
  $params = $roles;
}

$sql .= " ORDER BY FIELD(role,'admin','employe','user'), nom, prenom";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success'=>true, 'users'=>$users], JSON_UNESCAPED_UNICODE);
