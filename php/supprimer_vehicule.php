<?php
session_start();
require_once '../php/db.php';
$pdo = getPdo();

$id = $_GET['id'] ?? null;

if ($id) {
  $req = $pdo->prepare("DELETE FROM vehicules WHERE id = ? AND utilisateur_id = ?");
  $req->execute([$id, $_SESSION['utilisateur_id']]);
}