<?php
require __DIR__ . '/php/session_boot.php';

// Routeur simple
$page = $_GET['page'] ?? 'accueil';
$page = basename($page);  // évite les chemins dangereux
$file = __DIR__ . "/php/{$page}.php";

if (file_exists($file)) {
    require $file;
    exit;
}

http_response_code(404);
$accept = $_SERVER['HTTP_ACCEPT'] ?? '';
if (stripos($accept, 'application/json') !== false) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not Found']);
} else {
    header('Content-Type: text/html; charset=utf-8');
    echo "<h1>404 - Page introuvable</h1>";
}

