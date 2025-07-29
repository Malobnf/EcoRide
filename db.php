<?php
$url = getenv("JAWSDB_URL");
$dbparts = parse_url($url);

$host = $dbparts['host'];
$user = $dbparts['user'];
$pass = $dbparts['pass'];
$dbname = ltrim($dbparts['path'], '/');

$dsn = "mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ Connexion réussie à JawsDB MySQL !";
} catch (PDOException $e) {
    die("❌ Erreur de connexion à la base JawsDB : " . $e->getMessage());
}
