<?php
$host = 'localhost';
$dbname = 'ecoride';
$user = 'admin';
$pass = '';

try {
  $pdo = new PDO("mysql:host=localhost;dbname=ecoride;charset=utf8", "root", "");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => "Erreur de connexion base de données :" . $e->getMessage()]));
}