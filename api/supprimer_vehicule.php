<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=ecoride;charset=utf8', 'root', '');

$id = $_GET['id'] ?? null;

if ($id) {
  $req = $pdo->prepare("DELETE FROM vehicules WHERE id = ? AND utilisateur_id = ?");
  $req->execute([$id, $_SESSION['utilisateur_id']]);
}