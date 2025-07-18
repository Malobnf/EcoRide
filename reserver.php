<?php
session_start();
header('Content-Type: application/json');
require 'db.php'; // Connexion PDO

$input = json_decode(file_get_contents('php://input'), true);

$idUtilisateur = $_SESSION['utilisateur_id'] ?? null;
$idTrajet = $input['id_trajet'] ?? null;

if (!$idUtilisateur) {
  echo json_encode(['success' => false, 'message' => "Utilisateur non connecté."]);
  exit;
}

if (!$idTrajet) {
  echo json_encode(['success' => false, 'message' => "Trajet introuvable."]);
  exit;
}

$commission = 2;

// Maj crédits utilisateur
try {
  $pdo->beginTransaction();

  $stmt = $pdo->prepare("SELECT prix, places_disponibles FROM trajets WHERE id = :id FOR UPDATE");
  $stmt->execute([':id' => $idTrajet]);
  $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if (!$trajet) {
    throw new Exception("Trajet introuvable.");
  }

  if ($trajet['places_disponibles'] <= 0) {
    throw new Exception("Aucune place disponible.");
  }

  $stmt = $pdo->prepare("SELECT credits FROM utilisateurs WHERE id = :id FOR UPDATE");
  $stmt->execute([':id' => $idUtilisateur]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user['credits'] < $trajet['prix']) {
    throw new Exception("Crédits insuffisants.");
  }

// Calcul des crédits pour le conducteur
  $montantConducteur = max(0, $trajet['prix'] - $commission); // Empêche un montant négatif

//Insertion dans la table "reservations"
  $stmt = $pdo->prepare("INSERT INTO reservations (utilisateur_id, trajet_id, date_reservation) VALUES (:uid, :tid, NOW())");
  $stmt->execute([
    ':uid' => $idUtilisateur,
    ':tid' => $idTrajet
  ]);

// Réduire places disponibleq
$stmt = $pdo->prepare("UPDATE trajets SET places_disponibles = places_disponibles - 1 WHERE id = :id");
$stmt->execute([':id' => $idTrajet]);

// Déduire les crédits
$stmt = $pdo->prepare("UPDATE utilisateurs SET credits = credits - :prix WHERE id = :id");
$stmt->execute([
  ':prix' => $trajet['prix'],
  ':id' => $idUtilisateur
]);

// Ajout des crédits au conducteur : 
$stmt = $pdo->prepare("UPDATE utilisateurs SET credits = credits + :montant WHERE id = :id");
$stmt->execute([
  ':montant' => $montantConducteur,
  ':id' => $trajet['conducteur_id']
]);

// Enregistrer la commission pour l'entreprise
$stmt = $pdo->prepare("INSERT INTO entreprise_credits (montant, trajet_id, utilisateur_id) VALUES (:montant, :trajet_id, :utilisateur_id");
$stmt->execute([
  ':montant' => $commission,
  ':trajet_id' => $idTrajet,
  ':utilisateur_id' => $idUtilisateur
]);

// Valider transaction
$pdo->commit();
echo json_encode(['success' => true]);
} catch (Exception $e) {
  $pdo->rollback();
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
