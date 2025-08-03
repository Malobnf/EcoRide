<?php
// Affiche les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Routeur simple
$page = $_GET['page'] ?? 'accueil';
$page = basename($page);  // évite les chemins dangereux

$file = [
    __DIR__ . "/php/{$page}.php",
    __DIR__ . "/html/{$page}.html"
];

if (file_exists($file)) {
    require $file;
    exit;
}


http_response_code(404);
echo "Page introuvable : $page";

