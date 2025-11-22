<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <script src="../js/script.js" defer></script>
  <title>EcoRide – Proposer un trajet</title>
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
      <a class="current-page">Proposer un trajet</a>
      <a href="index.php?page=profil">Profil</a>
      <a href="index.php?page=contact">Contact</a>
      <a href="index.php?page=deconnexion">Déconnexion</a>
    </nav>
  </header>

  <main class="create-trip-page">
    <section class="create-trip-card">
      <h1>Proposer un trajet</h1>

      <div class="create-trip-grid">
        <!-- Colonne infos trajet -->
        <div class="trip-fields">
          <div class="field-group">
            <label for="departVille">Ville de départ</label>
            <input class="input" type="text" name="depart" id="departVille" required placeholder="Ex : Lyon">
          </div>

          <div class="field-group">
            <label for="arriveeVille">Ville d'arrivée</label>
            <input class="input" type="text" name="arrivee" id="arriveeVille" required placeholder="Ex : Grenoble">
          </div>

          <div class="field-group two-cols">
            <div>
              <label for="departDate">Date du départ</label>
              <input class="input" type="date" name="date" id="departDate" required>
            </div>
            <div>
              <label for="departHeure">Horaire du départ</label>
              <select id="departHeure" name="heure" required></select>
            </div>
          </div>
        </div>

        <!-- Colonne voiture / passagers / prix -->
        <div class="trip-extra">
          <div class="section-vehicule">
            <h2>Informations trajet</h2>

            <div class="field-group">
              <label for="voitureChoix">Quelle voiture utiliser ?</label>
              <p class="hint">Choisissez un véhicule parmi ceux enregistrés dans votre profil.</p>
              <div class="voiture-choix" id="voitureChoix">
                <p class="hint">Chargement de vos véhicules...</p>
              </div>
            </div>

            <div class="field-group">
              <label for="setNbPassagers">Nombre de passagers</label>
              <input type="number" id="setNbPassagers" min="1" placeholder="Ex : 3">
            </div>

            <div class="field-group">
              <label for="setPrixTrajet">Prix (en crédits)</label>
              <p class="hint">Un minimum de 2 crédits est requis pour EcoRide et son fonctionnement.</p>
              <input type="number" id="setPrixTrajet" min="2" placeholder="Ex : 5">
            </div>

            <button id="confBtn" class="btn-proposer">
              <i class="fa fa-arrow-right"></i>
              Proposer ce trajet
            </button>

          </div>
        </div>
      </div>
    </section>

    <!-- Popup de confirmation -->
    <div id="confTrajet" class="popup hidden">
      <div class="popup-content">
        <h2>Trajet enregistré avec succès !</h2>
        <p id="resume-content"></p>
        <button id="closePopupBtn">Fermer</button>
      </div>
    </div>
  </main>

  <footer>
    <div>
      <a rel="mentions-legales" href="../mentions-legales.pdf" target="_blank">Mentions légales</a>
    </div>
    <div>
      <a rel="contact" href="index.php?page=contact">Contact</a>
    </div>
  </footer>

</body>
</html>
