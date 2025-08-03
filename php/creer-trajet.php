<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/db.php');
$pdo = getPdo();

if (!isset($_SESSION['utilisateur_id'])) {
  echo json_encode(['success' => false, 'message' => "Non connecté"]);
  exit;
}

$userId = $_SESSION['utilisateur_id'];

// Vérification des champs
$requiredFields = ['depart', 'arrivee', 'date', 'heure', 'prix', 'passagers', 'voiture'];
foreach ($requiredFields as $field) {
  if (empty($_POST[$field])) {
    echo json_encode(['success' => false, 'message' => "Champ manquant : $field"]);
    exit;
  }
}

$depart = htmlspecialchars(trim($_POST['depart']));
$arrivee = htmlspecialchars(trim($_POST['arrivee']));
$date = $_POST['date'];
$heure = $_POST['heure'];
$prix = intval($_POST['prix']);
$places = intval($_POST['passagers']);
$type = htmlspecialchars(trim($_POST['voiture']));

// Insertion du trajet dans BDD

try {
  $stmt = $pdo->prepare("INSERT INTO trajets (conducteur_id , ville_depart, ville_arrivee, date_trajet, heure_depart, prix, places_disponibles, type_voiture) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$userId, $depart, $arrivee, $date, $heure, $prix, $places, $type]);

  echo json_encode(['success' => true]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => "Erreur d'enregistrement" . $e->getMessage()]);
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
  <script src="../js/script.js"></script>
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