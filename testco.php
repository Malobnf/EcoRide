<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
  $pdo = new PDO("mysql:host=localhost", "root", "");
  echo "Connexion réussie à MySQL et à la base ecoride ✅";
} catch (PDOException $e) {
  echo "Erreur de connexion MySQL : " . $e->getMessage();
}
