<!-- Modification de profil.html en profil.php / tentative de page dynamique simplifiée (sans 1000 interactions entre différents fichiers) -->



<?php
session_start();
require_once __DIR__ . '/db.php';
$pdo = getPdo();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.html');
    exit;
}

// Récupérer les infos utilisateur
$stmt = $pdo->prepare('SELECT nom, prenom, email, telephone, vehicule, description, credits, role FROM utilisateurs WHERE id = ?');
$stmt->execute([$_SESSION['utilisateur_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stmtVehicules = $pdo->prepare('SELECT id, marque, modele, couleur, places FROM vehicules WHERE utilisateur_id = ?');
$stmtVehicules->execute([$_SESSION['utilisateur_id']]);
$vehicules = $stmtVehicules->fetchAll(PDO::FETCH_ASSOC);

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
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <script>
    const userRole = <?= json_encode($user['role']) ?>;
  </script>
  <script src="../js/profil.js" defer></script>
  <script src="../js/gestionCovoit.js" defer></script>
  <script src="../js/script.js" defer></script>
  <script src="../js/rechercheCovoit.js" defer></script>
  <title>EcoRide</title>
</head>

<body>
  <header>
    <div class="deroulant">☰</div>
    <div class="marque"><a href="accueil.html">EcoRide</a></div>
    <div class="profile-icone">
      <a href="index.php?page=profil" id="icone-profil" rel= "profil">
        <i class="fas fa-circle-user fa-2x"></i>
      </a>
    </div>

    <nav class="side-menu" id="sideMenu">
      <a href="index.php?page=accueil">Accueil</a>
      <a href="index.php?page=covoit">Recherche</a>
      <a href="index.php?page=creer-trajet">Proposer un trajet</a>
      <a class="current-page">Profil</a>
      <a href="index.php?page=contact">Contact</a>
    </nav>
  </header>

  <!-- Onglet de gestion des covoiturages (admin + employe) -->
  <div id="gestionCovoitModalOverlay" class="modal-overlay hidden">
    <div class="modal-content-trajets">
      <span class="close-modal" id="closeGestionCovoitModal"><i class="fas fa-circle-xmark"></i></span>
      <h3>Gestion covoiturages</h3>
      <div class="modal-tabs">
        <button class="tab-button active" data-tab="avis">Avis</button>
        <button class="tab-button" data-tab="conflits">Conflits</button>
      </div>
      <div class="modal-tab-content" id="avis">
        <div id="listeAvis"></div>
      </div>
      <div class="modal-tab-content hidden" id="conflits">
        <div id="listeConflits"></div>
      </div>
    </div>
  </div>

  <button id="openTrajetsModal" type="button">Mes trajets</button>
  <button id="vehiculeBtn">Mes véhicules</button>
  
  <button id="adminTabBtn" style="display:none;">Gestion admin</button>

  <?php if (in_array($user['role'], ['admin', 'employe'])): ?>
    <button id="gestionCovoitBtn">Gestion covoiturages</button>
  <?php endif; ?>

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

  <div id="vehiculeModalOverlay" class="modal-overlay hidden">
    <div class="modal-content-trajets">
      <span class="close-modal" id="closeVehiculeModal"><i class="fas fa-circle-xmark"></i></span>
      <h3>Mes véhicules</h3>

      <div id="listeVehicules"></div>
      <button id="ajouterVehiculeBtn">Ajouter un véhicule</button>
      <form id="formModifVehicule" class="hidden">
        <input type="hidden" name="id" id="vehiculeInput" value="">
        <input type="text" name="marque">
        <input type="text" name="modele">
        <input type="text" name="plaque">
        <input type="text" name="couleur">
        <button type="submit">Enregistrer</button>
      </form>

      
  <!-- Formulaire ajout véhicule -->
      <form id="formAjoutVehicule" class="hidden">
        <input type="text" name="marque" placeholder="Marque" required>
        <input type="text" name="modele" placeholder="Modele" required>
        <input type="text" name="plaque" placeholder="Plaque" required>
        <input type="text" name="date-immat" placeholder="Date de première immatriculation" required>
        <input type="text" name="couleur" placeholder="Couleur" required>
        <input type="text" name="places" placeholder="Nombre de places passager" required>
        <button type="submit">Enregistrer</button>
      </form>
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
    <p>Crédits disponibles : <span id="userCredits"><?= htmlspecialchars($user['credits']) ?></span></p>      
      
    <div class="user-info">
      <h3>Informations publiques</h3>
      <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></p>
      <p><strong>Prénom :</strong> <?= htmlspecialchars($user['prenom']) ?></p>
      <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
      <p><strong>Téléphone :</strong> <?= htmlspecialchars($user['telephone']) ?></p>
      <div><strong>Véhicule :</strong></div>
      <?php if (count($vehicules) > 0): ?>
        <ul>
          <?php foreach ($vehicules as $v): ?>
            <li>
              <?= htmlspecialchars($v['marque']) ?> <?= htmlspecialchars($v['modele']) ?>,
              <?= htmlspecialchars($v['couleur']) ?>
              <a href="../api/modifier_vehicule.php?id=<?=$v['id'] ?>" class="modifier-btn"></a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Aucun véhicule enregistré.</p>
      <?php endif; ?>
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
    </div>

    <!-- Modification du profil -->
    <button id="profilBtn">Modifier le profil</button>
    <div id="editProfilePopup" class="modal-overlay hidden">
      <div class="modal-content-trajets">
        <span class="close-modal" id="closeEditProfile"><i class="fas fa-circle-xmark"></i></span>
        <h3>Modifier le profil</h3>

        <form id="editProfileForm">
          <div class="edit-field">
            <label>Nom :</label>
            <span id="editNomText"><?= htmlspecialchars($user['nom']) ?></span>
            <input type="text" id="editNomInput" name="nom" class="hidden" value="<?= htmlspecialchars($user['nom']) ?>">
            <i class="fas fa-pen edit-icon" data-target="editNom"></i>
          </div>

          <div class="edit-field">
            <label>Prénom :</label>
            <span id="editPrenomText"><?= htmlspecialchars($user['prenom']) ?></span>
            <input type="text" id="editPrenomInput" name="prenom" class="hidden" value="<?= htmlspecialchars($user['prenom']) ?>">
            <i class="fas fa-pen edit-icon" data-target="editPrenom"></i>
          </div>
          
          <div class="edit-field">
            <label>Email :</label>
            <span id="editEmailText"><?= htmlspecialchars($user['email']) ?></span>
            <input type="email" id="editEmailInput" name="email" class="hidden" value="<?= htmlspecialchars($user['email']) ?>">
            <i class="fas fa-pen edit-icon" data-target="editEmail"></i>
          </div>
          
          <div class="edit-field">
            <label>Téléphone :</label>
            <span id="editTelText"><?= htmlspecialchars($user['telephone']) ?></span>
            <input type="tel" id="editTelInput" name="telephone" class="hidden" value="<?= htmlspecialchars($user['telephone']) ?>">
            <i class="fas fa-pen edit-icon" data-target="editTel"></i>
          </div>
          
          <div class="edit-field">
            <label>Description :</label>
            <span id="editDescText"><?= htmlspecialchars($user['description']) ?></span>
            <textarea id="editDescInput" name="description" class="hidden"><?= htmlspecialchars($user['description']) ?></textarea>
            <i class="fas fa-pen edit-icon" data-target="editDesc"></i>
          </div>

          <button type="submit" id="saveProfileBtn">Sauvegarder les modifications</button>
        </form>
          
        
      </main>

      <button id="logoutBtn" onclick="window.location.href='index.php?page=deconnexion'">Déconnexion</button>

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