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
  <title>Mes trajets – EcoRide</title>
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
    <a class="current-page">Mes trajets</a>
    <a href="index.php?page=contact">Contact</a>
    <a href="index.php?page=deconnexion">Déconnexion</a>
  </nav>
</header>

<main class="profil-main">
  <h2>Mes trajets</h2>

  <div class="profil-actions">
    <a href="index.php?page=profil" class="btn-secondary">Retour au profil</a>
  </div>

  <div class="modal-tabs">
    <button class="tab-button active" data-tab="futurs">Trajets à venir</button>
    <button class="tab-button" data-tab="passes">Trajets passés</button>
  </div>

  <div id="futurs" class="mes-trajets-liste">
    <div id="listeTrajetsFuturs"></div>
  </div>

  <div id="passes" class="mes-trajets-liste" style="display:none;">
    <div id="listeTrajetsPasses"></div>
  </div>

  <div id="popupConfirm" class="hidden">
    <p>Souhaitez-vous vraiment annuler ce trajet ?</p>
    <button id="confirmerAnnulation">Oui</button>
    <button id="annulerAnnulation">Non</button>
  </div>
</main>

<script>
  const userRole = <?= json_encode($_SESSION['role'] ?? 'user') ?>;
</script>
<script src="../js/script.js" defer></script>
<script src="../js/mes_trajets_page.js" defer></script>
</body>
</html>
