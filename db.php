<?php
$host = 'etdq12exrvdjisg6.cbetxkdyhwsb.us-east-1.rds.amazonaws.com';
$port = '3306';
$dbname = 'dbtihpk3ml0inl13';
$user = 'hyig8y2kcbjlzzj4';
$pass = 'q58gn7sukb56s3en';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ Connexion réussie à JawsDB MySQL !";
} catch (PDOException $e) {
    die("❌ Erreur de connexion à la base JawsDB : " . $e->getMessage());
}
