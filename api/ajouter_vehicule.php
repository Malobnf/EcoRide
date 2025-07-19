<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=ecoride;charset=utf8', 'root', '');

$req = $pdo->prepare("INSERT INTO vehicules (marque, modele, plaque, couleur, utilisateur_id) VALUES (?, ?, ?, ?, ?)");
$req->execute([
  $_POST['marque'],
  $_POST['modele'],
  $_POST['plaque'],
  $_POST['couleur'],
  $_SESSION['utilisateur_id']
]);