<?php
session_start();
require_once(__DIR__ . '/db.php');
$pdo = getPdo();

// Vérification des champs 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nom = $_POST['nom'] ?? '';
  $prenom = $_POST['prenom'] ?? '';
  $email = $_POST['email'] ?? '';
  $telephone = $_POST['telephone'] ?? '';
  $mot_de_passe = $_POST['password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

// Sécurité : clear entries
  $nom = htmlspecialchars(trim($nom));
  $prenom = htmlspecialchars(trim($prenom));
  $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
  $telephone = trim($telephone);
  $mot_de_passe = trim($mot_de_passe);
  $confirm_password = trim($confirm_password);

  if (!$email || !$telephone || !$mot_de_passe || !$confirm_password || !$nom || !$prenom) {
    die("Tous les champs sont obligatoires.");
  }

  if ($mot_de_passe !== $confirm_password) {
    die("Les mots de passe ne correspondent pas.");
  }

// Vérifie que l'email est unique
$check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
$check->execute([$email]);
if ($check->fetch()) {
  die("Cet email est déjà utilisé.");
}

// Password hash
$mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

// Insertion du nouveau compte dans la table utilisateur avec 20 crédits
$stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, credits) VALUES (?, ?, ?, ?, 20)");
$stmt->execute([$nom, $prenom, $email, $mot_de_passe_hash]);

// Connecter automatiquement l'utilisateur
$_SESSION['user'] = [
  'id' => $pdo->lastInsertId(),
  'nom' => $nom,
  'email' => $email
];

// ID utilisateur access to all .php file

$_SESSION['utilisateur_id'] = $_SESSION['user']['id'];

// Redirection après inscritpion
header("Location: profil.php");
exit;
}
