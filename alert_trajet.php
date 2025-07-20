<?php
session_start();
header('Content-Type: application/json');
require 'db.php'; // Connexion PDO

$input = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['utilisateur_id'] ?? null;

if (!$userId) {
  echo json_encode(['success' => false, 'message' => "Utilisateur non connecté"]);
  exit;
}

$action = $input['action'] ?? null;
$trajetId = $input['trajet_id'] ?? null;

if (!$trajetId || !$action) {
  echo json_encode(['success' => false, 'message' => "Paramètres manquants"]);
  exit;
}

try {
  // Vérifier places dispo
  $stmt = $pdo->prepare("SELECT places_disponibles FROM trajets WHERE id = ?");
  $stmt->execute([$trajetId]);
  $trajet = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$trajet) {
    echo json_encode(['success' => false, 'message' => "Trajet introuvable"]);
    exit;
  }

  if ($action === 'verifier_places') {
    $isFull = ($trajet['places_disponibles'] <= 0);
    echo json_encode(['success' => true, 'plein' => $isFull]);
    exit;
  }

  if ($action === 'demander_alerte') {
    if ($trajet['places_disponibles'] > 0) {
      echo json_encode(['success' => false, 'message' => "Il reste des places, pas besoin d'alerte."]);
      exit;
    }

    // Vérifier si déjà inscrit
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM alertes_places WHERE utilisateur_id = ? AND trajet_id = ?");
    $stmt->execute([$userId, $trajetId]);
    if ($stmt->fetchColumn() > 0) {
      echo json_encode(['success' => false, 'message' => "Vous avez déjà demandé une alerte pour ce trajet."]);
      exit;
    }

    // Insérer la demande d'alerte
    $stmt = $pdo->prepare("INSERT INTO alertes_places (utilisateur_id, trajet_id, date_demande) VALUES (?, ?, NOW())");
    $stmt->execute([$userId, $trajetId]);

    echo json_encode(['success' => true, 'message' => "Vous serez prévenu par mail dès qu'une place se libère."]);
    exit;
  }

  if ($action === 'envoyer_alertes') {
    // Cette partie est à appeler uniquement en interne ou via un cron (voir https://www.uptimia.com/fr/learn/programmer-taches-cron-en-php) pour que l'utilisateur ne puisse pas la déclencher. Un cron (?) permet d'effectuer une veille tous les X temps pour vérifier les places. Rien compris

    if ($trajet['places_disponibles'] <= 0) {
      echo json_encode(['success' => false, 'message' => "Pas de place disponible, pas d'alertes envoyées."]);
      exit;
    }

    // Récupérer utilisateurs inscrits à l'alerte
    $stmt = $pdo->prepare("
      SELECT u.email, u.nom
      FROM alertes_places ap
      JOIN utilisateurs u ON ap.utilisateur_id = u.id
      WHERE ap.trajet_id = ?
    ");
    $stmt->execute([$trajetId]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Envoyer mail à chaque utilisateur
    foreach ($users as $user) {
      $to = $user['email'];
      $subject = "Place disponible pour un trajet";
      $message = "Bonjour " . $user['nom'] . ",\n\nUne place s'est libérée pour le trajet #$trajetId. Connectez-vous vite pour réserver !";
      $headers = "From: no-reply@ecoride.com";

      mail($to, $subject, $message, $headers);
    }

    // Supprimer toutes les alertes pour ce trajet
    $stmt = $pdo->prepare("DELETE FROM alertes_places WHERE trajet_id = ?");
    $stmt->execute([$trajetId]);

    echo json_encode(['success' => true, 'message' => "Alertes envoyées aux utilisateurs."]);
    exit;
  }

  echo json_encode(['success' => false, 'message' => "Action inconnue"]);

} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => "Erreur : " . $e->getMessage()]);
}
