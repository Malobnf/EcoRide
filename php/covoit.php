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
  <script src="../js/rechercheCovoit.js" defer></script>
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
      <a class="current-page">Recherche</a>
      <a href="index.php?page=creer-trajet_html">Proposer un trajet</a>
      <a href="index.php?page=profil">Profil</a>
      <a href="index.php?page=contact">Contact</a>
      <a href="index.php?page=deconnexion">Déconnexion</a>
    </nav>
  </header>

   <main class="search-page">
    <section class="search-card">
      <h1>Rechercher un trajet</h1>

      <div class="search-main">
        <!-- Colonne champs principaux -->
        <div class="search-fields">
          <div class="single-field">
            <label for="departVille">Ville de départ</label>
            <input class="input" type="text" name="depart" id="departVille" required placeholder="Ex : Lyon">
          </div>

          <div class="single-field">
            <label for="arriveeVille">Ville d'arrivée</label>
            <input class="input" type="text" name="arrivee" id="arriveeVille" placeholder="Ex : Grenoble">
          </div>

          <div class="single-field">
            <label for="departDate">Date</label>
            <input class="input" type="date" name="date" id="departDate" required>
          </div>

          <button id="searchBtn" class="searchBtn" type="button">
            <span>Rechercher un trajet</span>
            <i class="fa fa-arrow-right"></i>
          </button>
        </div>

        <!-- Colonne filtres -->
        <form id="filtre" class="filtre filters-card">
          <h3>Filtres</h3>

          <!-- Type de véhicule -->
          <label for="voiture">Propulsion</label>
          <select id="voiture" name="voiture">
            <option value="">Tous</option>
            <option value="electrique">Électrique</option>
            <option value="essence">Essence</option>
          </select>

          <!-- Prix -->
          <label for="prix">Prix maximum (€)</label>
          <input type="number" id="prix" name="prix" min="0" placeholder="Entrez un prix maximum">

          <!-- Note -->
          <label for="note">Note minimum du conducteur</label>
          <input type="number" id="note" name="note" min="0" max="4" step="0.5" placeholder="Entrez une note minimum">

          <button type="submit" class="filtreBtn">Appliquer les filtres</button>
        </form>
      </div>
    </section>

    <!-- Résultats -->
    <section class="results-card">
      <div id="message"></div>
      <div class="hidden" id="resultats"></div>
    </section>
  </main>


  <div id="trajetModal" class="modal hidden">
    <div class="modal-content">
      <span class="close-btn" id="closeModal"><i class="fa-solid fa-circle-xmark fa-2x"></i></span>
      <div class="conducteur-info">
        <img id="modalPhoto" src="../Images/default-user.png" alt="Photo de profil"/>
        <h2 id="modalNom">Nom du conducteur</h2>
        <p id="modalRating">⭐⭐⭐☆</p>
      </div>
      <div class="trajet-info">
        <p><strong>Date :</strong> <span id="modalDate"></span></p>
        <p><strong>Heure :</strong> <span id="modalHeure"></span></p>
        <p><strong>De :</strong> <span id="modalDepart"></span></p>
        <p><strong>A :</strong> <span id="modalArrivee"></span></p>
        <p><strong>Prix :</strong> <span id="modalPrix"></span> crédits</p>
        <p><strong>Places disponibles :</strong> <span id="modalPlaces"></span></p>
      </div>
      <button id="reserverBtn">Réserver</button>
      <button id="demanderAlerteBtn">Être alerté si une place se libère</button>
<div id="alerteMessage"></div>

    </div>
  </div>
  
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