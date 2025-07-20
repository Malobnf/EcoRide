<?php
session_start();
header('Content-Type: application/json');

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

// Connexion BDD
try {
  $pdo = new PDO('mysql:host=localhost;dbname=ecoride;charset=utf8', "root", "");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Erreur de connexion BDD' . $e->getMessage()]);
  exit;
}

// Insertion du trajet dans BDD

try {
  $stmt = $pdo->prepare("INSERT INTO trajets (conducteur_id , ville_depart, ville_arrivee, date_trajet, heure_depart, prix, places_disponibles, type_voiture) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$userId, $depart, $arrivee, $date, $heure, $prix, $places, $type]);

  echo json_encode(['success' => true]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => "Erreur d'enregistrement" . $e->getMessage()]);
}