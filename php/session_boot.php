<?php
declare(strict_types=1);

// Détecter HTTPS
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

// Paramètres du cookie de session (avant session_start)
session_set_cookie_params([
  'lifetime' => 0,         // cookie de session (jusqu’à fermeture du navigateur)
  'path'     => '/',
  'secure'   => $isHttps,
  'httponly' => true,      // JS ne peut pas lire le cookie
  'samesite' => 'Lax',
]);

// Renforcements
ini_set('session.use_only_cookies', '1'); // Jamais d’ID en URL
ini_set('session.use_strict_mode', '1');  // Refuse un ID de session non émis par le serveur

session_name('ECORIDESESSID');
session_start();
