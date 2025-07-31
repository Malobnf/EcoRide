<?php
session_start();

// Inclure la configuration
require_once __DIR__ . '/php/db.php';
$pdo = getPdo();

// Logique de routage simple (optionnel)
$page = $_GET['page'] ?? 'accueil';

switch ($page) {
    case 'accueil':
        require_once __DIR__ . '/html/accueil.html';
        break;
    case 'profil':
        require_once __DIR__ . '/php/profil.php';
        break;
    default:
        http_response_code(404);
        echo "Page introuvable.";
}
