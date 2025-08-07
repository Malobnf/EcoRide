<?php
// Affiche les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Routeur simple
$page = $_GET['page'] ?? 'accueil';
$page = basename($page);  // évite les chemins dangereux
$file = __DIR__ . "/php/{$page}.php"

echo "<pre>Fichier recherché : $file</pre>";

if (file_exists($file)) {
    require $file;
} else {
    echo "<h1>404 - Page introuvable</h1>";
    http_response_code(404);
}

