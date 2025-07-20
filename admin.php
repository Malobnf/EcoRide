<?php
session_start();
if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: connexion.html');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=ecoride;charset=utf8', 'root', '');

// Gestion des requêtes POST pour création, modification, suppression ici

// Récupérer tous les employés
$stmt = $pdo->query("SELECT id, nom, prenom, email FROM utilisateurs WHERE role = 'employe'");
$employes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <script>
    const userRole = <?= json_encode($user['role']) ?>;
  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="profil.js" defer></script>
  <script src="js/script.js" defer></script>
  <script src="stats.js" defer></script>
  <title>Gestion Admin</title>
</head>


<body>

   <header>
    <div class="deroulant" onclick="toggleMenu()">☰</div>
    <div class="marque"><a href="accueil.html">EcoRide</a></div>
    <div class="profile-icone">
      <a href="profil.php" id="icone-profil" rel= "profil">
        <i class="fas fa-circle-user fa-2x"></i>
      </a>
    </div>

    <nav class="side-menu" id="sideMenu">
      <a href="accueil.html">Accueil</a>
      <a href="covoit.html">Recherche</a>
      <a href="creer-trajet.html">Proposer un trajet</a>
      <a class="current-page">Profil</a>
      <a href="contact.html">Contact</a>
    </nav>
  </header>
  
<h1>Gestion des comptes employés</h1>

<h2>Liste des employés</h2>
<table border="1">
    <tr><th>Nom</th><th>Prénom</th><th>Email</th><th>Actions</th></tr>
    <?php foreach ($employes as $emp): ?>
    <tr>
        <td><?= htmlspecialchars($emp['nom']) ?></td>
        <td><?= htmlspecialchars($emp['prenom']) ?></td>
        <td><?= htmlspecialchars($emp['email']) ?></td>
        <td>
            <!-- Actions : modifier, supprimer (liens ou boutons) -->
            <a class="action-admin" href="modifier_employe.php?id=<?= $emp['id'] ?>">Modifier</a> |
            <a class="action-admin" href="supprimer_employe.php?id=<?= $emp['id'] ?>" onclick="return confirm('Supprimer cet employé ?')">Supprimer</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Ajouter un nouvel employé</h2>
<form method="post" action="creer_employe.php">
    <input type="text" name="nom" placeholder="Nom" required>
    <input type="text" name="prenom" placeholder="Prénom" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <button type="submit">Créer</button>
</form>

<h2>Statistiques</h2>
<label for="periodeSelect">Période :</label>
<select id="periodeSelect">
  <option value="jour" selected>Jour</option>
  <option value="mois">Mois</option>
  <option value="annee">Année</option>
</select>

<div>
  <canvas id="chartTrajets"></canvas>
</div>

<div>
  <canvas id="chartCredits"></canvas>
</div>
<p>Total des crédits accumulés par EcoRide : <strong id="totalCredits"></strong></p>

</body>
</html>
