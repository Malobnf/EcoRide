<?php
require __DIR__ . '/php/db.php';
try {
    $pdo = getPdo();
    echo "Connexion OK !";
} catch (Throwable $e) {
    echo "Erreur : " . $e->getMessage();
}
