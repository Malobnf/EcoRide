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
  <script src="../js/script.js"></script>
  <title>EcoRide</title>
</head>

<body>
  <header>
    <div class="deroulant">☰</div>
    <div class="marque"><a href="index.php?page=accueil">EcoRide</a></div>
    <div class="profile-icone">
      <a href="index.php?page=profil" id="icone-profil" rel= "profil">
        <i class="fas fa-circle-user fa-2x"></i>
      </a>
    </div>

    <nav class="side-menu" id="sideMenu">
      <a href="index.php?page=accueil">Accueil</a>
      <a href="index.php?page=covoit">Recherche</a>
      <a class="current-page">Proposer un trajet</a>
      <a href="index.php?page=profil">Profil</a>
      <a href="index.php?page=contact">Contact</a>
    </nav>
  </header>

  <div class="container">
    <label for="search"><h2 id="input-recherche"></h2></label>
    <p>Ville de départ :</p>
      <div class="search-bar">
        <input class="input" type="text" name="depart" id="departVille" required placeholder="Départ...">
      </div>

    <label for="search"><h2 id="input-recherche"></h2></label>
    <p>Ville d'arrivée :</p>
      <div class="search-bar">
        <input class="input" type="text" name="arrivee" id="arriveeVille" required placeholder="Arrivée...">
      </div>
      
    <p>Date du départ:</p>
      <div class="search-bar">
        <input class="input" type="date" name="date" id="departDate" required>
      </div>

      
      <p>Horaire du départ:</p>
      <select id="departHeure" name="heure" required></select>
      <!-- <input type="time" id="departHeure" name="heure" step="900" required></input> -->
      
      <div class="section-vehicule">
        <p>Quelle voiture utiliser ?</p>
        
        <div class="voiture-choix" id="voitureChoix">
          <input type="radio" name="voiture" id="choixVoiture" required>
        </div>
        
        <p>Combien de passagers ?</p>
        <div class="set-passagers" id="setPassagers">
          <input type="number" id="setNbPassagers">
        </div>
        
        <p>Quel prix ? (Un minimum de 2 crédits est requis pour EcoRide et son fonctionnement)</p>
        <div class="set-prix" id="setPrix">
          <input type="number" id="setPrixTrajet">
        </div>
        
        <button id="confBtn" class="confBtn" type="button"><i class="fa fa-arrow-right"></i>Proposer un trajet</button>
      </div>
  </div>
        
    <div id="confTrajet" class="popup hidden">
      <div class="popup-content">
        <h2>Trajet enregistré avec succès !</h2>
        <p id="resume-content"></p>
        <button id="closePopupBtn">Fermer</button>
      </div>
    </div>

  <footer>
    <div>Mentions légales</div>
    <div>
      <a rel="contact" href="index.php?page=contact">Contact</a>
    </div>
    <div>Signaler un bug</div>
  </footer>

  </body>
</html>