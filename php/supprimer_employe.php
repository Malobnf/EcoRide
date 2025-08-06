<?php
session_start();
require_once(__DIR__ . '/db.php');
$pdo = getPdo();

if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: connexion.html');
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID employé manquant.");
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?page=admin.php");
exit;
