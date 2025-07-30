<?php
session_start();

// Inclure la configuration
require_once 'db.php';
$pdo = getPdo();

// Logique de routage simple (optionnel)
$page = $_GET['page'] ?? 'accueil';

switch ($page) {
    case 'accueil':
        include 'accueil.html';
        break;
    case 'profil':
        include 'profil.php';
        break;
    default:
        http_response_code(404);
        echo "Page introuvable.";
}
