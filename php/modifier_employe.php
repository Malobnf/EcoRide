<?php
session_start();
require_once(__DIR__ . '/db.php');
$pdo = getPdo();

if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: connexion.html');
    exit;
}

// Vérifie que l’ID est passé en GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID employé manquant.");
}

$id = (int) $_GET['id'];

// Si formulaire soumis (POST), traite la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';

    $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ? WHERE id = ?");
    $stmt->execute([$nom, $prenom, $email, $id]);

    header("Location: index.php?page=admin.php");
    exit;
}

// Sinon, récupère les données de l’employé
$stmt = $pdo->prepare("SELECT nom, prenom, email FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$employe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employe) {
    die("Employé non trouvé.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier Employé</title>
</head>
<body>
  <h1>Modifier Employé</h1>
  <form method="post">
    <input type="text" name="nom" value="<?= htmlspecialchars($employe['nom']) ?>" required>
    <input type="text" name="prenom" value="<?= htmlspecialchars($employe['prenom']) ?>" required>
    <input type="email" name="email" value="<?= htmlspecialchars($employe['email']) ?>" required>
    <button type="submit">Enregistrer</button>
  </form>
  <a href="index.php?page=admin.php">Retour à la gestion</a>
</body>
</html>
