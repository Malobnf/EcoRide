<?php
declare(strict_types=1);

if (empty($_SESSION['utilisateur_id'])) {
    header('Location: index.php?page=connexion_html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes véhicules – EcoRide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="../js/script.js" defer></script>
</head>
<body>
<header>
  <div class="deroulant">☰</div>
  <div class="marque"><a href="index.php?page=accueil">EcoRide</a></div>
  <div class="profile-icone">
    <a href="index.php?page=profil" id="icone-profil" rel="profil">
      <i class="fas fa-circle-user fa-2x"></i>
    </a>
  </div>

  <nav class="side-menu" id="sideMenu">
    <a href="index.php?page=accueil">Accueil</a>
    <a href="index.php?page=covoit">Recherche</a>
    <a href="index.php?page=creer-trajet_html">Proposer un trajet</a>
    <a href="index.php?page=profil">Profil</a>
    <a class="current-page">Mes véhicules</a>
    <a href="index.php?page=contact">Contact</a>
    <a href="index.php?page=deconnexion">Déconnexion</a>
  </nav>
</header>

<main class="profil-main">
  <h2>Mes véhicules</h2>

  <div class="profil-actions">
    <a href="index.php?page=profil" class="btn-secondary">Retour au profil</a>
  </div>

  <div id="listeVehicules"></div>

  <button id="ajouterVehiculeBtn" class="btn-primary btn-outline">Ajouter un véhicule</button>

  <form id="formAjoutVehicule" class="vehicule-form hidden">
  <h3 class="vehicule-form-title">Ajouter un véhicule</h3>

  <div class="vehicule-form-row">
    <div class="vehicule-field">
      <label for="veh_marque">Marque</label>
      <input type="text" id="veh_marque" name="marque" placeholder="Ex : Peugeot" required>
    </div>

    <div class="vehicule-field">
      <label for="veh_modele">Modèle</label>
      <input type="text" id="veh_modele" name="modele" placeholder="Ex : 208" required>
    </div>
  </div>

  <div class="vehicule-form-row">
    <div class="vehicule-field">
      <label for="veh_plaque">Immatriculation</label>
      <input type="text" id="veh_plaque" name="plaque" placeholder="Ex : AB-123-CD" required>
    </div>

    <div class="vehicule-field">
      <label for="annee_immat">Année d'immatriculation</label>
      <select name="annee_immat" id="annee_immat" required>
        <option value="">Sélectionnez une année</option>
        <!-- les années sont toujours remplies en JS -->
      </select>
    </div>
  </div>

  <div class="vehicule-form-row">
    <div class="vehicule-field">
      <label for="veh_couleur">Couleur</label>
      <input type="text" id="veh_couleur" name="couleur" placeholder="Ex : Bleu" required>
    </div>

    <div class="vehicule-field">
      <label for="veh_places">Places passagers</label>
      <input type="number" id="veh_places" name="places" placeholder="Ex : 3" min="1" required>
    </div>
  </div>

  <div class="vehicule-form-actions">
    <button type="submit" class="btn-primary">Enregistrer le véhicule</button>
  </div>
</form>


  <form id="formModifVehicule" class="hidden">
    <input type="hidden" name="id" id="vehiculeInput" value="">
    <input type="text" name="marque" required>
    <input type="text" name="modele" required>
    <input type="text" name="plaque" required>
    <input type="text" name="couleur" required>
    <button type="submit" class="btn-primary">Enregistrer</button>
  </form>

  <div id="vehiculeMessage"></div>
</main>

<script>
  const userRole = <?= json_encode($_SESSION['role'] ?? 'user') ?>;
</script>
<script src="../js/script.js" defer></script>
<script src="../js/mes_vehicules_page.js" defer></script>
</body>
</html>
