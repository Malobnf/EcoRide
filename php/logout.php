<?php
$_SESSION = [];
session_destroy();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

header("Location: index.php?page=connexion");
exit;
