<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=ecoride;charset=utf8', 'root', '');

$req = $pdo->prepare("SELECT * FROM vehicules WHERE utilisateur_id = ?");
$req->execute([$_SESSION['utilisateur_id']]);
echo json_encode($req->fetchAll(PDO::FETCH_ASSOC));