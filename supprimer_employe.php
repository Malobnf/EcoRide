<?php
session_start();
if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: connexion.html');
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID employé manquant.");
}

$id = (int) $_GET['id'];

$pdo = new PDO('mysql:host=localhost;dbname=ecoride;charset=utf8', 'root', '');

$stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);

header("Location: admin.php");
exit;
