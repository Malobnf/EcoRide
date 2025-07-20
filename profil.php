<!-- Modification de profil.html en profil.php / tentative de page dynamique simplifiée (sans 1000 interactions entre différents fichiers) -->



<?php
session_start();
// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.html');
    exit;
}

// Connexion à la BDD
$pdo = new PDO('mysql:host=localhost;dbname=ecoride;charset=utf8', 'root', '');

// Récupérer les infos utilisateur
$stmt = $pdo->prepare('SELECT nom, email, telephone, vehicule, description, credits FROM utilisateurs WHERE id = ?');
$stmt->execute([$_SESSION['utilisateur_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit;
}
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
  <script src="profil.js" defer></script>
  <script src="js/script.js" defer></script>
  <script src="rechercheCovoit.js" defer></script>
  <title>EcoRide</title>
</head>
  <title>EcoRide - Profil</title>
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
      <a class="current-page">Profil</a>
      <a href="contact.html">Contact</a>
    </nav>
  </header>

  <button id="openTrajetsModal" type="button">Mes trajets</button>

  <div id="trajetsModalOverlay" class="modal-overlay hidden">
    <div id="trajetsModal" class="modal-content-trajets">
      <span class="close-modal" id="closeTrajetsModal"><i class="fas fa-circle-xmark"></i></span>

      <div class="modal-tabs">
        <button class="tab-button active" data-tab="futurs">Trajets à venir</button>
        <button class="tab-button" data-tab="passes">Trajets passés</button>
      </div>

      <div class="modal-tab-content" id="futurs">
        <div id="listeTrajetsFuturs"></div>
      </div>

      <div class="modal-tab-content" id="passes">
        <div id="listeTrajetsPasses"></div>
      </div>
    </div>
  </div>

  <!-- Popup de confirmation de l'annulation -->
   <div id="popupConfirm" class="hidden">
    <p>Souhaitez-vous vraiment annuler ce trajet ?</p>
    <button id="confirmerAnnulation">Oui</button>
    <button id="annulerAnnulation">Non</button>
   </div>
  

  <main class="profil-main">
    <div class="profile-pic"></div>
    <div class="user-full-name" id="userFullName"><?= htmlspecialchars($user['nom']) ?></div>
    <div class="rating">★★★★</div>
    <div class="reviews">
      <div class="review">Super conducteur, trajet agréable !</div>
      <div class="review">Ponctuel et très sympathique.</div>
      <div class="review">Je recommande sans hésiter.</div>
    </div>
    <p>Crédits disponibles : <span id="userCredits"><?= htmlspecialchars($user['credits']) ?></span><p>      
      
    <div class="user-info">
      <h3>Informations publiques</h3>
      <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></p>
      <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
      <p><strong>Téléphone :</strong> <?= htmlspecialchars($user['telephone']) ?></p>
      <p><strong>Véhicule :</strong> <?= htmlspecialchars($user['vehicule']) ?></p>
      <p><strong>À propos :</strong> <?= nl2br(htmlspecialchars($user['description'])) ?></p>
      <h4>Préférences :</h4>
        <form id="preferencesForm">
          <p>Choisissez vos préférences :</p>

          <label for="pref_non_fumeur">
            <input type="checkbox" id="pref_non_fumeur" name="preferences[]" value="non_fumeur">Non-fumeur
          </label><br>

          <label for="pref_animaux_ok">
            <input type="checkbox" id="pref_animaux_ok" name="preferences[]" value="animaux_ok">J'aime les animaux !
          </label><br>

          <label for="pref_musique">
            <input type="checkbox" id="pref_musique" name="preferences[]" value="musique">J'aime écouter de la musique !
          </label><br>

          <label for="pref_discussion">
            <input type="checkbox" id="pref_discussion" name="preferences[]" value="discussion">Je suis ouvert à la discussion !
          </label><br>
          
          <button type="submit">Enregistrer les préférences</button>
        </form>

        <div id="prefMessage"></div>
      </p>
    </div>

    <!-- Modification du profil -->
    

    <button id="vehiculeBtn"><a href="ajout-vehicule.html">Ajouter un véhicule</a></button>

    <button id="logoutBtn" onclick="window.location.href='deconnexion.php'">Déconnexion</button>

  </main>

  </body>
</html>






<!-- Script original : profil.html -->

<!--
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
  <script src="profil.js" defer></script>
  <script src="js/script.js" defer></script>
  <script src="rechercheCovoit.js" defer></script>
  <title>EcoRide</title>
</head>

<body>
  <header>
    <div class="deroulant" onclick="toggleMenu()">☰</div>
    <div class="marque"><a href="accueil.html">EcoRide</a></div>
    <div class="profile-icone">
      <a href="profil.html" id="icone-profil" rel= "profil">
        <i class="fas fa-circle-user fa-2x"></i>
      </a>
    </div>

    <nav class="side-menu" id="sideMenu">
      <a href="accueil.html">Accueil</a>
      <a href="covoit.html">Recherche</a>
      <a class="current-page">Profil</a>
      <a href="contact.html">Contact</a>
    </nav>
  </header>

  <div id="mesTrajets">
    <h2 id="mesTrajetsTab">Mes trajets réservés</h2>
    <div id="mesTrajetsContent" class="hidden">
      <div id="listeTrajets"></div>
    </div>
  </div>

  Popup de confirmation de l'annulation
   <div id="popupConfirm" class="hidden">
    <p>Souhaitez-vous vraiment annuler ce trajet ?</p>
    <button id="confirmerAnnulation">Oui</button>
    <button id="annulerAnnulation">Non</button>
   </div>
  
  <main class="profil-main">
    <div class="profile-pic"></div>
    <div class="user-full-name" id="userFullName"></div>
    <div class="rating">★★★★</div>
    <div class="reviews">
      <div class="review">Super conducteur, trajet agréable !</div>
      <div class="review">Ponctuel et très sympathique.</div>
      <div class="review">Je recommande sans hésiter.</div>
    </div>
    <p>Crédits disponibles : <span id="userCredits"></span><p>      
      
      <div class="user-info">
        <h3>Informations publiques</h3>
        <p><strong>Nom :</strong> Jean Dupont</p>
        <p><strong>Email :</strong> jean.dupont@email.com</p>
        <p><strong>Téléphone :</strong> 06 12 34 56 78</p>
        <p><strong>Véhicule :</strong> Peugeot 208, Bleu, 4 places</p>
        <p><strong>À propos :</strong> Conducteur depuis 5 ans, j'adore partager mes trajets et rencontrer de nouvelles personnes.</p>
        <h4>Préférences :</h4>
          <form id="preferencesForm">
            <p>Choisissez vos préférences :</p>

            <label for="pref_non_fumeur">
              <input type="checkbox" id="pref_non_fumeur" name="preferences[]" value="non_fumeur">Non-fumeur
            </label><br>

            <label for="pref_animaux_ok">
              <input type="checkbox" id="pref_animaux_ok" name="preferences[]" value="animaux_ok">J'aime les animaux !
            </label><br>

            <label for="pref_musique">
              <input type="checkbox" id="pref_musique" name="preferences[]" value="musique">J'aime écouter de la musique !
            </label><br>

            <label for="pref_discussion">
              <input type="checkbox" id="pref_discussion" name="preferences[]" value="discussion">Je suis ouvert à la discussion !
            </label><br>
            
            <button type="submit">Enregistrer les préférences</button>
          </form>

          <div id="prefMessage"></div>
        </p>
      </div>

    <button id="profilBtn">Modifier le profil</button>
    <div class="mod-info" id="modInfo">
      <form id="modifProfilForm">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required>
        
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>

        <label for="telephone">Téléphone :</label>
        <input type="tel" id="telephone" name="telephone" required>

        <label for="apropos">A propos :</label>
        <textarea id="apropos" name="description"></textarea>

        <button type="cancelModif">Enregistrer</button>
        <button type="button" id="cancelModif">Annuler</button>
      </form>
    </div>

    <button id="vehiculeBtn"><a href="ajout-vehicule.html">Ajouter un véhicule</a></button>

    <button id="logoutBtn" onclick="window.location.href='deconnexion.php'">Déconnexion</button>

  </main>

</body>
</html>
-->