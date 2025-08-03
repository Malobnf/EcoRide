<?php
session_start();
require_once(__DIR__ . '/db.php');
$pdo = getPdo();

$req = $pdo->prepare("INSERT INTO vehicules (marque, modele, plaque, couleur, utilisateur_id) VALUES (?, ?, ?, ?, ?)");
$req->execute([
  $_POST['marque'],
  $_POST['modele'],
  $_POST['plaque'],
  $_POST['couleur'],
  $_SESSION['utilisateur_id']
]);