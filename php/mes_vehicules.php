<?php
session_start();
require_once(__DIR__ . '/db.php');
$pdo = getPdo();

$req = $pdo->prepare("SELECT * FROM vehicules WHERE utilisateur_id = ?");
$req->execute([$_SESSION['utilisateur_id']]);
echo json_encode($req->fetchAll(PDO::FETCH_ASSOC));