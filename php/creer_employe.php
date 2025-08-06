<?php
session_start();
require_once(__DIR__ . '/db.php');
$pdo = getPdo();

if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?page=connexion_html.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$nom, $prenom, $email, $passwordHash, 'employe']);

        header('Location: index.php?page=admin.php?success=1');
        exit;
    } else {
        echo "Tous les champs sont obligatoires.";
    }
} else {
    header('Location: index.php?page=admin.php');
    exit;
}
?>
